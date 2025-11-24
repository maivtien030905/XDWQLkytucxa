<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mã hoá mật khẩu
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Luôn gán quyền sinh viên
    $role = "sinhvien";

    $sql = "INSERT INTO taikhoan (username, password, role)
            VALUES ('$username', '$hashed', '$role')";

    if ($conn->query($sql)) {
        echo "<script>alert('Đăng ký thành công! Hãy đăng nhập.'); window.location='login.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Đăng ký tài khoản</h2>
    <form method="POST">
        <label>Tên đăng nhập</label>
        <input type="text" name="username" required><br>

        <label>Mật khẩu</label>
        <input type="password" name="password" required><br>

        <button type="submit">Đăng ký</button>
    </form>
</body>
</html>
