<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// --- Lấy danh sách phòng cùng số chỗ còn trống (sử dụng tên cột đúng với DB: tenphong, songuoitoida)
$sql_phong = "
    SELECT 
        phong.id,
        phong.tenphong,
        phong.songuoitoida,
        COUNT(hopdong.id) AS so_sinhvien,
        (phong.songuoitoida - COUNT(hopdong.id)) AS so_con_trong
    FROM phong
    LEFT JOIN hopdong ON phong.id = hopdong.phongid
    GROUP BY phong.id, phong.tenphong, phong.songuoitoida
";
$phong = $conn->query($sql_phong);
if (!$phong) {
    die("<div style='color:red;text-align:center;margin-top:20px'>
         ❌ Lỗi truy vấn phòng: " . $conn->error . "<br>
         Hãy kiểm tra lại tên cột trong bảng <b>phong</b>.
         </div>");
}

// Xử lý POST (thêm sinh viên + tạo hợp đồng)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // lấy input & trim để an toàn
    $hoten = trim($_POST['hoten'] ?? '');
    $masv = trim($_POST['masv'] ?? '');
    $lop = trim($_POST['lop'] ?? '');
    $sodt = trim($_POST['sodt'] ?? '');
    $phongid = (int)($_POST['phongid'] ?? 0);
    $ngaybatdau = $_POST['ngaybatdau'] ?? '';
    $ngayketthuc = $_POST['ngayketthuc'] ?? '';

    // kiểm tra dữ liệu nhập
    if ($hoten === '' || $masv === '' || $lop === '' || $phongid <= 0 || $ngaybatdau === '' || $ngayketthuc === '') {
        $message = "⚠️ Vui lòng nhập đầy đủ thông tin.";
    } else {
        // kiểm tra chỗ trống của phòng
        $stmt_check = $conn->prepare("
            SELECT (songuoitoida - COUNT(hopdong.id)) AS con_trong
            FROM phong
            LEFT JOIN hopdong ON phong.id = hopdong.phongid
            WHERE phong.id = ?
            GROUP BY phong.id, phong.songuoitoida
        ");
        if (!$stmt_check) {
            $message = "❌ Lỗi truy vấn kiểm tra phòng: " . $conn->error;
        } else {
            $stmt_check->bind_param("i", $phongid);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            $row_check = $res_check->fetch_assoc();
            $stmt_check->close();

            $con_trong = $row_check['con_trong'] ?? 0;
            if ($con_trong <= 0) {
                $message = "❌ Phòng đã đầy, không thể thêm sinh viên vào phòng này.";
            } else {
                // Thêm sinh viên vào bảng sinhvien (các cột: hoten, masv, lop, sodt)
                $stmt_sv = $conn->prepare("INSERT INTO sinhvien (hoten, masv, lop, sodt) VALUES (?, ?, ?, ?)");
                if (!$stmt_sv) {
                    $message = "❌ Lỗi khi chuẩn bị thêm sinh viên: " . $conn->error;
                } else {
                    $stmt_sv->bind_param("ssss", $hoten, $masv, $lop, $sodt);
                    if ($stmt_sv->execute()) {
                        $sinhvienid = $stmt_sv->insert_id;
                        $stmt_sv->close();

                        // Tạo hợp đồng (hopdong: sinhvienid, phongid, ngaybatdau, ngayketthuc)
                        $stmt_hd = $conn->prepare("INSERT INTO hopdong (sinhvienid, phongid, ngaybatdau, ngayketthuc) VALUES (?, ?, ?, ?)");
                        if (!$stmt_hd) {
                            $message = "❌ Lỗi khi chuẩn bị tạo hợp đồng: " . $conn->error;
                        } else {
                            $stmt_hd->bind_param("iiss", $sinhvienid, $phongid, $ngaybatdau, $ngayketthuc);
                            if ($stmt_hd->execute()) {
                                $message = "✅ Thêm sinh viên vào phòng thành công!";
                                // refresh lại danh sách phòng để cập nhật chỗ trống (tùy chọn)
                                $phong = $conn->query($sql_phong);
                            } else {
                                $message = "❌ Lỗi khi tạo hợp đồng: " . $stmt_hd->error;
                            }
                            $stmt_hd->close();
                        }
                    } else {
                        $message = "❌ Lỗi khi thêm sinh viên: " . $stmt_sv->error;
                        $stmt_sv->close();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sinh viên vào phòng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center text-primary mb-4">🧾 Thêm sinh viên vào phòng</h3>

    <?php if ($message): ?>
        <div class="alert <?= (strpos($message, '✅') === 0) ? 'alert-success' : 'alert-danger' ?> text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow w-75 mx-auto">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Họ tên:</label>
                <input type="text" name="hoten" class="form-control" required value="<?= isset($_POST['hoten']) ? htmlspecialchars($_POST['hoten']) : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Mã sinh viên:</label>
                <input type="text" name="masv" class="form-control" required value="<?= isset($_POST['masv']) ? htmlspecialchars($_POST['masv']) : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Lớp:</label>
                <input type="text" name="lop" class="form-control" required value="<?= isset($_POST['lop']) ? htmlspecialchars($_POST['lop']) : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Số điện thoại:</label>
                <input type="text" name="sodt" class="form-control" value="<?= isset($_POST['sodt']) ? htmlspecialchars($_POST['sodt']) : '' ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Phòng:</label>
            <select name="phongid" class="form-select" required>
                <option value="">-- Chọn phòng --</option>
                <?php
                // reset pointer nếu cần
                if ($phong) {
                    // fetch_assoc đã dùng trước có thể đã ở cuối, nên re-query
                    $phong = $conn->query($sql_phong);
                    while ($p = $phong->fetch_assoc()) :
                ?>
                    <option value="<?= $p['id'] ?>" <?= (isset($_POST['phongid']) && $_POST['phongid'] == $p['id']) ? 'selected' : '' ?>
                        <?= ($p['so_con_trong'] <= 0) ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($p['tenphong']) ?> (Còn trống: <?= $p['so_con_trong'] ?>)
                    </option>
                <?php
                    endwhile;
                }
                ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày bắt đầu:</label>
                <input type="date" name="ngaybatdau" class="form-control" required value="<?= isset($_POST['ngaybatdau']) ? htmlspecialchars($_POST['ngaybatdau']) : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày kết thúc:</label>
                <input type="date" name="ngayketthuc" class="form-control" required value="<?= isset($_POST['ngayketthuc']) ? htmlspecialchars($_POST['ngayketthuc']) : '' ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">💾 Thêm hợp đồng</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">← Quay lại</a>
    </form>
</div>
</body>
</html>
