<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM lichsu_doi_phong ORDER BY ngay_doi DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đổi phòng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-4 shadow-lg p-4 rounded-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold">📜 Lịch sử đổi phòng</h3>
        <a href="index.php" class="btn btn-secondary btn-sm">⬅️ Quay lại</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên sinh viên</th>
                    <th>Phòng cũ</th>
                    <th>Phòng mới</th>
                    <th>Ngày đổi</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['ten_sinhvien']) ?></td>
                            <td class="text-danger"><?= htmlspecialchars($row['phong_cu']) ?></td>
                            <td class="text-success"><?= htmlspecialchars($row['phong_moi']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['ngay_doi'])) ?></td>
                            <td><?= htmlspecialchars($row['ghichu']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr><td colspan="6" class="text-muted py-3">⛔ Chưa có lịch sử đổi phòng nào!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
