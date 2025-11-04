<?php
session_start();
include 'db.php';

// 🧩 Kiểm tra nếu chưa đăng nhập thì quay lại trang login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 🧩 Lấy danh sách phòng + số sinh viên đang ở
$sql = "
    SELECT 
        phong.id,
        phong.tenphong,
        phong.songuoitoida,
        phong.giathue,
        COUNT(hopdong.id) AS so_sinhvien,
        (phong.songuoitoida - COUNT(hopdong.id)) AS so_con_trong
    FROM phong
    LEFT JOIN hopdong ON phong.id = hopdong.phongid
    GROUP BY phong.id, phong.tenphong, phong.songuoitoida, phong.giathue
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

<body>

<div class="container mt-4 shadow-lg">

    <!-- 🧭 Thanh điều hướng -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold">🏠 Quản lý ký túc xá</h3>
        <div>
            <span class="me-3 text-secondary">
                Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
        </div>
    </div>

    <!-- 🧩 Các nút thao tác -->
    <div class="mb-3 text-end">
        <a href="phong_them.php" class="btn btn-success btn-sm">+ Thêm phòng</a>
        <a href="hopdong_them.php" class="btn btn-info btn-sm">+ Thêm sinh viên vào phòng</a>
        <a href="hopdong_danhsach.php" class="btn btn-warning btn-sm">📋 Xem danh sách hợp đồng</a>
    </div>

    <!-- 🧱 Bảng hiển thị dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Số người tối đa</th>
                    <th>Giá thuê (VNĐ)</th>
                    <th>Đang ở</th>
                    <th>Còn trống</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['tenphong']) ?></td>
                            <td><?= $row['songuoitoida'] ?></td>
                            <td><?= number_format($row['giathue'], 0, ',', '.') ?></td>
                            <td><?= $row['so_sinhvien'] ?></td>
                            <td class="<?= ($row['so_con_trong'] > 0) ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                                <?= $row['so_con_trong'] ?>
                            </td>
                            <td>
                                <a href="phong_chitiet.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                    👁️ Xem
                                </a>
                                <a href="phong_sua.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                    ✏️ Sửa
                                </a>
                                <a href="phong_xoa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc muốn xóa phòng này không?')">
                                   🗑️ Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-muted py-3">
                            🚪 Chưa có dữ liệu phòng nào!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
