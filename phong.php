<?php
include 'db.php';

// Xử lý tìm kiếm
$where = [];
if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where[] = "phong.tenphong LIKE '%$search%'";
}
if (!empty($_GET['min_price'])) {
    $min_price = (int)$_GET['min_price'];
    $where[] = "phong.giathue >= $min_price";
}
if (!empty($_GET['max_price'])) {
    $max_price = (int)$_GET['max_price'];
    $where[] = "phong.giathue <= $max_price";
}
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// Lấy danh sách phòng
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
    $where_sql
    GROUP BY phong.id, phong.tenphong, phong.songuoitoida, phong.giathue
";
$result = $conn->query($sql);
?>

<div class="content-box">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">📋 Danh sách phòng</h4>
    </div>

    <!-- Form tìm kiếm -->
    <form method="GET" class="row g-3 mb-3" onsubmit="event.preventDefault(); loadSearch(this);">
        <div class="col-md-3">
            <label class="form-label">Tên phòng</label>
            <input type="text" name="search" class="form-control" placeholder="Nhập tên phòng...">
        </div>
        <div class="col-md-3">
            <label class="form-label">Giá thuê từ (VNĐ)</label>
            <input type="number" name="min_price" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Đến (VNĐ)</label>
            <input type="number" name="max_price" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">🔍 Tìm kiếm</button>
            <button type="button" class="btn btn-outline-secondary" onclick="loadPage('phong.php')">🧹 Làm mới</button>
        </div>
    </form>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
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
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
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
                                <a href="phong_chitiet.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">👁️</a>
                                <a href="phong_sua.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                                <a href="phong_xoa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa phòng này?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-muted py-3">🚪 Không tìm thấy phòng phù hợp!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function loadSearch(form) {
    const params = new URLSearchParams(new FormData(form)).toString();
    loadPage('phong.php?' + params);
}
</script>
