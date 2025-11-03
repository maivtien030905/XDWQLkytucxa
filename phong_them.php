<?php
include 'db.php';

// Khi người dùng bấm nút "Thêm phòng"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $tenphong = $_POST['tenphong'] ?? '';
    $songuoitoida = $_POST['songuoitoida'] ?? 0;
    $giathue = $_POST['giathue'] ?? 0;

    // Kiểm tra dữ liệu hợp lệ
    if (!empty($tenphong) && $songuoitoida > 0 && $giathue > 0) {
        // Truy vấn INSERT đúng tên cột
        $sql = "INSERT INTO phong (tenphong, songuoitoida, giathue) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $tenphong, $songuoitoida, $giathue);

        if ($stmt->execute()) {
            echo "<script>
                alert('✅ Thêm phòng thành công!');
                window.location = 'index.php';
            </script>";
        } else {
            echo "<div class='alert alert-danger'>Lỗi khi thêm: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>⚠️ Vui lòng nhập đầy đủ thông tin hợp lệ!</div>";
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

<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Thêm phòng ký túc xá</h2>

    <form method="POST" class="p-4 bg-white rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên phòng</label>
            <input type="text" name="tenphong" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số người tối đa</label>
            <input type="number" name="songuoitoida" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá thuê (VNĐ)</label>
            <input type="number" name="giathue" class="form-control" min="0" required>
        </div>

        <button type="submit" class="btn btn-success">Thêm phòng</button>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

</body>
</html>
