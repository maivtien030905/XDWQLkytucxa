<?php
include 'config.php';

// Xử lý tìm kiếm theo tháng, năm hoặc phòng
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$thang = isset($_GET['thang']) ? $_GET['thang'] : '';
$nam = isset($_GET['nam']) ? $_GET['nam'] : '';

$sql = "
    SELECT 
        dn.id,
        p.tenphong,
        dn.thang,
        dn.nam,
        dn.chisodiencu,
        dn.chisodienmoi,
        dn.chisonuoccu,
        dn.chisonuocmoi,
        (dn.chisodienmoi - dn.chisodiencu) AS tieuthu_dien,
        (dn.chisonuocmoi - dn.chisonuoccu) AS tieuthu_nuoc,
        h.tiendien,
        h.tiennuoc,
        h.tongtien,
        h.trangthai,
        dn.ngaycapnhat
    FROM diennuoc dn
    JOIN phong p ON dn.phongid = p.id
    LEFT JOIN hoadon h ON dn.id = h.diennuocid
    WHERE 1=1
";

if ($keyword != '') {
    $sql .= " AND p.tenphong LIKE '%$keyword%'";
}
if ($thang != '') {
    $sql .= " AND dn.thang = '$thang'";
}
if ($nam != '') {
    $sql .= " AND dn.nam = '$nam'";
}

$sql .= " ORDER BY dn.nam DESC, dn.thang DESC, p.tenphong ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê điện nước</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
    body {
        background: linear-gradient(to right, #e0f7fa, #80deea);
        font-family: "Segoe UI", sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        margin-top: 30px;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0,0,0,0.15);
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #007bff;
    }
    .search-bar {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 20px;
    }
    input, select {
        border-radius: 6px;
        padding: 8px;
        border: 1px solid #ccc;
    }
    .btn-search {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-search:hover {
        background-color: #0056b3;
    }
</style>
</head>
<body>
<div class="container">
    <h2>📊 Thống kê chỉ số điện nước</h2>

    <!-- Form tìm kiếm -->
    <form method="GET" class="search-bar">
        <input type="text" name="keyword" placeholder="Tìm phòng..." value="<?= htmlspecialchars($keyword) ?>">
        <select name="thang">
            <option value="">Tháng</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= ($thang == $i) ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <select name="nam">
            <option value="">Năm</option>
            <?php for ($y = 2023; $y <= date('Y'); $y++): ?>
                <option value="<?= $y ?>" <?= ($nam == $y) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn-search">🔍 Tìm kiếm</button>
    </form>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-info">
                <tr>
                    <th>Phòng</th>
                    <th>Tháng</th>
                    <th>Năm</th>
                    <th>Điện cũ</th>
                    <th>Điện mới</th>
                    <th>Tiêu thụ</th>
                    <th>Nước cũ</th>
                    <th>Nước mới</th>
                    <th>Tiêu thụ</th>
                    <th>Tiền điện (VNĐ)</th>
                    <th>Tiền nước (VNĐ)</th>
                    <th>Tổng (VNĐ)</th>
                    <th>Trạng thái</th>
                    <th>Ngày cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tenphong']) ?></td>
                            <td><?= $row['thang'] ?></td>
                            <td><?= $row['nam'] ?></td>
                            <td><?= $row['chisodiencu'] ?></td>
                            <td><?= $row['chisodienmoi'] ?></td>
                            <td><?= $row['tieuthu_dien'] ?></td>
                            <td><?= $row['chisonuoccu'] ?></td>
                            <td><?= $row['chisonuocmoi'] ?></td>
                            <td><?= $row['tieuthu_nuoc'] ?></td>
                            <td><?= number_format($row['tiendien'], 0, ',', '.') ?></td>
                            <td><?= number_format($row['tiennuoc'], 0, ',', '.') ?></td>
                            <td class="fw-bold text-primary"><?= number_format($row['tongtien'], 0, ',', '.') ?></td>
                            <td class="<?= ($row['trangthai'] == 'Đã thanh toán') ? 'text-success' : 'text-danger' ?>">
                                <?= $row['trangthai'] ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['ngaycapnhat'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14" class="text-muted">Chưa có dữ liệu điện nước nào!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
