<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Kiểm tra username trùng
    $sql = "SELECT * FROM taikhoan WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "❌ Tên đăng nhập đã tồn tại!";
    } else {
        // Thêm mới tài khoản
        $sql_insert = "INSERT INTO taikhoan (username, password, role) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql_insert);
        $stmt2->bind_param("sss", $username, $password, $role);

        if ($stmt2->execute()) {
            $success = "✔️ Đăng ký thành công! Bạn có thể đăng nhập.";
        } else {
            $error = "❌ Lỗi khi đăng ký!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center text-success mb-4">Tạo tài khoản mới</h3>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
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

        <div class="mb-3">
            <label class="form-label">Quyền</label>
            <select name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="sinhvien">Sinh viên</option>
                <option value="quanly">Quản lý</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Đăng ký</button>

        <div class="text-center mt-3">
            <a href="login.php">← Quay lại đăng nhập</a>
        </div>
    </form>
</div>

</body>
</html>
