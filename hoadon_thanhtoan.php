<?php
include 'config.php';

// Xử lý cập nhật trạng thái thanh toán
if (isset($_GET['thanhtoan_id'])) {
    $id = $_GET['thanhtoan_id'];
    $update = "UPDATE hoadon SET trangthai = 'Đã thanh toán' WHERE id = $id";
    mysqli_query($conn, $update);
    header("Location: hoadon_thanhtoan.php");
    exit;
}

// Xử lý tìm kiếm
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$thang = isset($_GET['thang']) ? $_GET['thang'] : '';
$nam = isset($_GET['nam']) ? $_GET['nam'] : '';

$sql = "
    SELECT 
        h.id,
        p.tenphong,
        dn.thang,
        dn.nam,
        h.tiendien,
        h.tiennuoc,
        h.tongtien,
        h.trangthai,
        h.ngaytao
    FROM hoadon h
    JOIN diennuoc dn ON h.diennuocid = dn.id
    JOIN phong p ON dn.phongid = p.id
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
<title>Thanh toán hóa đơn điện nước</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
    body {
        background: linear-gradient(to right, #e3f2fd, #90caf9);
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
        color: #0d6efd;
        font-weight: bold;
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
        background-color: #0d6efd;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-search:hover {
        background-color: #0056b3;
    }
    .btn-pay {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 5px;
        text-decoration: none;
    }
    .btn-pay:hover {
        background-color: #218838;
    }
</style>
</head>
<body>
<div class="container">
    <h2>💰 Quản lý và thanh toán hóa đơn điện nước</h2>

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

    <!-- Bảng hóa đơn -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Phòng</th>
                    <th>Tháng</th>
                    <th>Năm</th>
                    <th>Tiền điện (VNĐ)</th>
                    <th>Tiền nước (VNĐ)</th>
                    <th>Tổng (VNĐ)</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tenphong']) ?></td>
                            <td><?= $row['thang'] ?></td>
                            <td><?= $row['nam'] ?></td>
                            <td><?= number_format($row['tiendien'], 0, ',', '.') ?></td>
                            <td><?= number_format($row['tiennuoc'], 0, ',', '.') ?></td>
                            <td class="fw-bold text-primary"><?= number_format($row['tongtien'], 0, ',', '.') ?></td>
                            <td class="<?= ($row['trangthai'] == 'Đã thanh toán') ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                                <?= $row['trangthai'] ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['ngaytao'])) ?></td>
                            <td>
                                <?php if ($row['trangthai'] == 'Chưa thanh toán'): ?>
                                    <a href="?thanhtoan_id=<?= $row['id'] ?>" class="btn-pay" onclick="return confirm('Xác nhận đã thanh toán hóa đơn này?')">💵 Thanh toán</a>
                                <?php else: ?>
                                    ✅ Đã thanh toán
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-muted">Không có hóa đơn nào trong danh sách!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
