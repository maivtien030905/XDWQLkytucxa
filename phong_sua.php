<?php
include 'db.php';
$message = "";

// Lấy ID phòng cần sửa
if (!isset($_GET['id'])) {
    die("<div style='color:red;text-align:center'>❌ Thiếu ID phòng!</div>");
}
$id = $_GET['id'];

// Lấy thông tin phòng
$sql = "SELECT * FROM phong WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    die("<div style='color:red;text-align:center'>❌ Phòng không tồn tại!</div>");
}
$phong = $result->fetch_assoc();

// Khi nhấn nút Lưu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenphong = $_POST['tenphong'];
    $songuoitoida = $_POST['songuoitoida'];
    $giathue = $_POST['giathue'];

    $sql_update = "UPDATE phong 
                   SET tenphong='$tenphong', songuoitoida='$songuoitoida', giathue='$giathue'
                   WHERE id = $id";

    if ($conn->query($sql_update)) {
        $message = "✅ Cập nhật thông tin phòng thành công!";
        header("refresh:1; url=index.php");
    } else {
        $message = "❌ Lỗi khi cập nhật: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin phòng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body class="bg-light">
<div class="container mt-5">

    <h3 class="text-center text-primary mb-4">✏️ Sửa thông tin phòng</h3>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="w-50 mx-auto card p-4 shadow">
        <div class="mb-3">
            <label>Tên phòng:</label>
            <input type="text" name="tenphong" value="<?= htmlspecialchars($phong['tenphong']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Số người tối đa:</label>
            <input type="number" name="songuoitoida" value="<?= $phong['songuoitoida'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Giá thuê (VNĐ):</label>
            <input type="number" name="giathue" value="<?= $phong['giathue'] ?>" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">💾 Lưu thay đổi</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">← Quay lại</a>
    </form>
</div>
</body>
</html>
