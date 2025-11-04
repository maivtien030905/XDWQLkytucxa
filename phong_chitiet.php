<?php
include 'db.php';
if (!isset($_GET['id'])) {
    die("<div style='color:red;text-align:center'>❌ Thiếu ID phòng!</div>");
}

$phong_id = $_GET['id'];

// Lấy thông tin phòng
$sql_phong = "SELECT * FROM phong WHERE id = $phong_id";
$result_phong = $conn->query($sql_phong);
if (!$result_phong || $result_phong->num_rows == 0) {
    die("<div style='color:red;text-align:center'>❌ Phòng không tồn tại!</div>");
}
$phong = $result_phong->fetch_assoc();

// Lấy danh sách sinh viên trong phòng này
$sql_sv = "
    SELECT 
        sinhvien.hoten,
        sinhvien.masv,
        sinhvien.lop,
        sinhvien.sodt,
        hopdong.ngaybatdau,
        hopdong.ngayketthuc
    FROM hopdong
    INNER JOIN sinhvien ON hopdong.sinhvienid = sinhvien.id
    WHERE hopdong.phongid = $phong_id
";
$result_sv = $conn->query($sql_sv);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phòng <?= htmlspecialchars($phong['tenphong']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">

    <h3 class="text-center text-primary mb-4">
        🏠 Thông tin phòng: <?= htmlspecialchars($phong['tenphong']) ?>
    </h3>

    <div class="card mb-4 shadow">
        <div class="card-body">
            <p><b>Mã phòng:</b> <?= $phong['id'] ?></p>
            <p><b>Số người tối đa:</b> <?= $phong['songuoitoida'] ?></p>
            <p><b>Giá thuê:</b> <?= number_format($phong['giathue'], 0, ',', '.') ?> VNĐ</p>
        </div>
    </div>

    <h5 class="text-secondary">👩‍🎓 Danh sách sinh viên trong phòng:</h5>

    <table class="table table-bordered table-hover text-center shadow mt-3">
        <thead class="table-info">
            <tr>
                <th>Họ tên</th>
                <th>Mã SV</th>
                <th>Lớp</th>
                <th>Số ĐT</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_sv && $result_sv->num_rows > 0): ?>
                <?php while ($sv = $result_sv->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($sv['hoten']) ?></td>
                        <td><?= htmlspecialchars($sv['masv']) ?></td>
                        <td><?= htmlspecialchars($sv['lop']) ?></td>
                        <td><?= htmlspecialchars($sv['sodt']) ?></td>
                        <td><?= htmlspecialchars($sv['ngaybatdau']) ?></td>
                        <td><?= htmlspecialchars($sv['ngayketthuc']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-muted">Chưa có sinh viên nào trong phòng này!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary mt-3">← Quay lại danh sách phòng</a>
</div>
</body>
</html>
