<?php
session_start();
include 'db.php';

// 🧩 Kiểm tra nếu chưa đăng nhập thì quay lại trang login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 🧩 Khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenphong = $_POST['tenphong'];
    $songuoitoida = $_POST['songuoitoida'];
    $giathue = $_POST['giathue'];

    if (!empty($tenphong) && !empty($songuoitoida) && !empty($giathue)) {
        $sql = "INSERT INTO phong (tenphong, songuoitoida, giathue)
                VALUES ('$tenphong', '$songuoitoida', '$giathue')";
        if ($conn->query($sql)) {
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger mt-3'>❌ Lỗi SQL: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3'>⚠️ Vui lòng nhập đầy đủ thông tin!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm phòng mới</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3 text-primary">🟩 Thêm phòng mới</h3>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên phòng</label>
            <input type="text" name="tenphong" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số người tối đa</label>
            <input type="number" name="songuoitoida" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá thuê (VNĐ)</label>
            <input type="number" name="giathue" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Lưu phòng</button>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
</body>
</html>
