<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$sql = "
    SELECT 
        hopdong.id,
        sinhvien.hoten,
        sinhvien.masv,
        phong.tenphong,
        hopdong.ngaybatdau,
        hopdong.ngayketthuc
    FROM hopdong
    INNER JOIN sinhvien ON hopdong.sinhvienid = sinhvien.id
    INNER JOIN phong ON hopdong.phongid = phong.id
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách hợp đồng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body class="bg-light">
<div class="container mt-5">

    <h3 class="text-center text-primary mb-4">📋 Danh sách hợp đồng ký túc xá</h3>

    <div class="text-end mb-3">
        <a href="hopdong_them.php" class="btn btn-success btn-sm">+ Thêm hợp đồng</a>
        <a href="index.php" class="btn btn-secondary btn-sm">← Quay lại</a>
    </div>

    <table class="table table-bordered table-hover text-center shadow">
        <thead class="table-warning">
            <tr>
                <th>ID</th>
                <th>Họ tên SV</th>
                <th>Mã SV</th>
                <th>Phòng</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['hoten']) ?></td>
                        <td><?= htmlspecialchars($row['masv']) ?></td>
                        <td><?= htmlspecialchars($row['tenphong']) ?></td>
                        <td><?= htmlspecialchars($row['ngaybatdau']) ?></td>
                        <td><?= htmlspecialchars($row['ngayketthuc']) ?></td>
                        <td>
                            <a href="hopdong_sua.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                            <a href="hopdong_xoa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Bạn có chắc muốn xóa hợp đồng này không?')">🗑️ Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-muted">Chưa có hợp đồng nào!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
