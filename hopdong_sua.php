<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("<div style='color:red;text-align:center'>❌ Thiếu ID hợp đồng!</div>");
}

$id = $_GET['id'];

// Lấy thông tin hợp đồng cần sửa
$sql = "
    SELECT hopdong.*, sinhvien.hoten, sinhvien.masv 
    FROM hopdong
    INNER JOIN sinhvien ON hopdong.sinhvienid = sinhvien.id
    WHERE hopdong.id = $id
";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    die("<div style='color:red;text-align:center'>❌ Hợp đồng không tồn tại!</div>");
}
$hd = $result->fetch_assoc();

// Lấy danh sách phòng để chọn lại
$phong_result = $conn->query("SELECT * FROM phong");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phongid = $_POST['phongid'];
    $ngaybatdau = $_POST['ngaybatdau'];
    $ngayketthuc = $_POST['ngayketthuc'];

    $update_sql = "
        UPDATE hopdong 
        SET phongid = '$phongid',
            ngaybatdau = '$ngaybatdau',
            ngayketthuc = '$ngayketthuc'
        WHERE id = $id
    ";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('✅ Cập nhật hợp đồng thành công!'); window.location='hopdong_danhsach.php';</script>";
    } else {
        echo "<div class='alert alert-danger text-center'>Lỗi: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa hợp đồng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-primary text-center mb-4">✏️ Sửa hợp đồng của sinh viên: <?= htmlspecialchars($hd['hoten']) ?></h3>

    <form method="POST" class="card shadow p-4">
        <div class="mb-3">
            <label class="form-label">Phòng</label>
            <select name="phongid" class="form-select" required>
                <?php while ($p = $phong_result->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" <?= ($p['id'] == $hd['phongid']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['tenphong']) ?> (<?= number_format($p['giathue'], 0, ',', '.') ?>đ)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="date" name="ngaybatdau" value="<?= $hd['ngaybatdau'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="date" name="ngayketthuc" value="<?= $hd['ngayketthuc'] ?>" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
            <a href="hopdong_danhsach.php" class="btn btn-secondary">↩️ Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
