<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trang sinh viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-info">
            <h3>🎓 Xin chào sinh viên <?= $_SESSION['username'] ?></h3>
            <p>Bạn đang sử dụng quyền <b>sinhvien</b></p>
        </div>

        <a href="index.php" class="btn btn-primary">Về trang chính</a>
        <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
    </div>
</body>
</html>
