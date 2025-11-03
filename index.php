<?php
include 'db.php';

// Lấy danh sách phòng + số sinh viên đang ở
$sql = "
    SELECT 
        phong.id,
        phong.ten_phong,
        phong.so_nguoi_toi_da,
        phong.gia_thue,
        COUNT(hopdong.id) AS so_sinhvien,
        (phong.so_nguoi_toi_da - COUNT(hopdong.id)) AS so_con_trong
    FROM phong
    LEFT JOIN hopdong ON phong.id = hopdong.phongid
    GROUP BY phong.id
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý ký túc xá</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Danh sách phòng ký túc xá</h2>

    <div class="mb-3 text-end">
        <a href="phong_them.php" class="btn btn-success btn-sm">+ Thêm phòng</a>
        <a href="hopdong_them.php" class="btn btn-info btn-sm">+ Thêm sinh viên vào phòng</a>
    </div>

    <table class="table table-bordered table-hover text-center shadow">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên phòng</th>
                <th>Số người tối đa</th>
                <th>Giá thuê (VNĐ)</th>
                <th>Số sinh viên đang ở</th>
                <th>Số chỗ trống</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['ten_phong']) ?></td>
                        <td><?= $row['so_nguoi_toi_da'] ?></td>
                        <td><?= number_format($row['gia_thue'], 0, ',', '.') ?></td>
                        <td><?= $row['so_sinhvien'] ?></td>
                        <td class="<?= ($row['so_con_trong'] > 0) ? 'text-success' : 'text-danger' ?>">
                            <?= $row['so_con_trong'] ?>
                        </td>
                        <td>
                            <a href="phong_xoa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Bạn có chắc muốn xóa phòng này không?')">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr><td colspan="7">Chưa có dữ liệu phòng!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
