<?php
include 'db.php';
$message = "";

// Lấy danh sách phòng
$phong = $conn->query("SELECT * FROM phong");

// Xử lý thêm hợp đồng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = $_POST['ho_ten'];
    $masv = $_POST['ma_sv'];
    $lop = $_POST['lop'];
    $sodt = $_POST['so_dt'];
    $phongid = $_POST['phong_id'];
    $ngaybat_dau = $_POST['ngay_bat_dau'];
    $ngayketthuc = $_POST['ngay_ket_thuc'];

    // Thêm sinh viên
    $conn->query("INSERT INTO sinhvien (ho_ten, ma_sv, lop, so_dt) 
                  VALUES ('$ho_ten', '$ma_sv', '$lop', '$so_dt')");
    $sinhvien_id = $conn->insert_id;

    // Tạo hợp đồng
    $conn->query("INSERT INTO hopdong (sinhvien_id, phong_id, ngay_bat_dau, ngay_ket_thuc)
                  VALUES ('$sinhvien_id', '$phong_id', '$ngay_bat_dau', '$ngay_ket_thuc')");

    $message = "Thêm sinh viên vào phòng thành công!";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sinh viên vào phòng</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center text-info">Thêm sinh viên vào phòng</h3>

    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="w-75 mx-auto">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Họ tên:</label>
                <input type="text" name="ho_ten" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Mã sinh viên:</label>
                <input type="text" name="ma_sv" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Lớp:</label>
                <input type="text" name="lop" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Số điện thoại:</label>
                <input type="text" name="so_dt" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Phòng:</label>
            <select name="phong_id" class="form-control" required>
                <option value="">-- Chọn phòng --</option>
                <?php while ($p = $phong->fetch_assoc()) : ?>
                    <option value="<?= $p['id'] ?>"><?= $p['ten_phong'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Ngày bắt đầu:</label>
                <input type="date" name="ngay_bat_dau" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Ngày kết thúc:</label>
                <input type="date" name="ngay_ket_thuc" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-info w-100">Thêm hợp đồng</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">← Quay lại</a>
    </form>
</div>
</body>
</html>
