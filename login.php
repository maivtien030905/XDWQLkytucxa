<?php
session_start();
include 'db.php';

// Hiển thị lỗi để tránh trắng trang
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra khi người dùng nhấn nút Đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ✅ Truy vấn đúng cột trong database
    $sql = "SELECT * FROM taikhoan WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ Lỗi SQL prepare(): " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra tài khoản có tồn tại không
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // So sánh mật khẩu (chưa mã hóa)
        if ($password == $row['password']) {

            // 🔥 Lưu thông tin Session
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];  // <-- QUAN TRỌNG: phân quyền

            // ✨ Chuyển hướng sau khi đăng nhập
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Sai mật khẩu!";
        }
    } 
    if ($password == $row['password']) {

    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    // Chuyển hướng theo role
    if ($row['role'] == 'admin') {
        header("Location: index.php");
    } else {
        header("Location: sinhvien_home.php");
    }
    exit();
}
    else {
        $error = "❌ Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center text-primary mb-4">Đăng nhập hệ thống</h3>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
        <div class="text-center mt-3">
    <a href="register.php">Chưa có tài khoản? Đăng ký</a>
</div>
    </form>
</div>
</body>
</html>
