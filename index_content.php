<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM phong");
?>

<div class="container mt-4">
    <h2 class="text-primary mb-3">📋 Danh sách phòng</h2>

    <table class="table table-bordered table-striped">
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
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['tenphong'] ?></td>
                <td><?= $row['songuoitoida'] ?></td>
                <td><?= number_format($row['giathue']) ?></td>
                <td><?= $row['songuoio'] ?></td>
                <td><?= $row['songuoitoida'] - $row['songuoio'] ?></td>
                <td>
                    <a href="phong_chitiet.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Xem</a>
                    <a href="phong_sua.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="phong_xoa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
