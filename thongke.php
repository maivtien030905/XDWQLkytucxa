<?php
include 'db.php';
include 'layout.php';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">📊 Thống kê điện, nước và thanh toán</h2>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Phòng</th>
                        <th>Tháng</th>
                        <th>Điện (kWh)</th>
                        <th>Nước (m³)</th>
                        <th>Tổng tiền (VNĐ)</th>
                        <th>Trạng thái thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Giả sử có bảng diennuoc trong DB
                    $sql = "SELECT phong, thang, sodien, sonuoc, tongtien, trangthai FROM diennuoc ORDER BY thang DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['phong']}</td>
                                    <td>{$row['thang']}</td>
                                    <td>{$row['sodien']}</td>
                                    <td>{$row['sonuoc']}</td>
                                    <td>" . number_format($row['tongtien'], 0, ',', '.') . "</td>
                                    <td>" . ($row['trangthai'] == 'Đã thanh toán' ? '✅ Đã thanh toán' : '❌ Chưa thanh toán') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Chưa có dữ liệu</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
