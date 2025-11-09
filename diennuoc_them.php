<?php
include 'config.php';

// Lấy danh sách phòng
$phong_query = "SELECT id, tenphong FROM phong";
$phong_result = mysqli_query($conn, $phong_query);

// Khi nhấn Lưu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phongid = $_POST['phongid'];
    $thang = $_POST['thang'];
    $nam = $_POST['nam'];
    $chisodiencu = $_POST['chisodiencu'];
    $chisodienmoi = $_POST['chisodienmoi'];
    $chisonuoccu = $_POST['chisonuoccu'];
    $chisonuocmoi = $_POST['chisonuocmoi'];
    $ghichu = $_POST['ghichu'];

    $tieuthu_dien = $chisodienmoi - $chisodiencu;
    $tieuthu_nuoc = $chisonuocmoi - $chisonuoccu;

    // Lấy giá mới nhất
    $gia_query = "SELECT gia_dien, gia_nuoc FROM giadichvu ORDER BY ngayapdung DESC LIMIT 1";
    $gia_result = mysqli_query($conn, $gia_query);
    $gia = mysqli_fetch_assoc($gia_result);

    $tiendien = $tieuthu_dien * $gia['gia_dien'];
    $tiennuoc = $tieuthu_nuoc * $gia['gia_nuoc'];
    $tongtien = $tiendien + $tiennuoc;

    // Lưu vào bảng diennuoc
    $sql_diennuoc = "INSERT INTO diennuoc (phongid, thang, nam, chisodiencu, chisodienmoi, chisonuoccu, chisonuocmoi, ngaycapnhat, ghichu)
                     VALUES ('$phongid', '$thang', '$nam', '$chisodiencu', '$chisodienmoi', '$chisonuoccu', '$chisonuocmoi', NOW(), '$ghichu')";
    mysqli_query($conn, $sql_diennuoc);

    // Lấy id diennuoc vừa tạo
    $diennuoc_id = mysqli_insert_id($conn);

    // Lưu vào bảng hoadon
    $sql_hoadon = "INSERT INTO hoadon (diennuocid, phongid, thang, nam, tiendien, tiennuoc, tongtien, trangthai)
                   VALUES ('$diennuoc_id', '$phongid', '$thang', '$nam', '$tiendien', '$tiennuoc', '$tongtien', 'Chưa thanh toán')";
    mysqli_query($conn, $sql_hoadon);

    echo "<script>alert('Đã lưu thành công chỉ số điện nước!'); window.location='diennuoc_them.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhập chỉ số điện nước</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #89f7fe, #66a6ff);
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 700px;
        margin: 50px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    label {
        font-weight: bold;
        margin-bottom: 5px;
    }
    input, select, textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
    }
    button {
        background: #007bff;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        background: #0056b3;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Nhập chỉ số điện nước</h2>
    <form method="POST">
        <label>Phòng:</label>
        <select name="phongid" required>
            <option value="">-- Chọn phòng --</option>
            <?php while($row = mysqli_fetch_assoc($phong_result)) { ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['tenphong']) ?></option>
            <?php } ?>
        </select>

        <label>Tháng:</label>
        <input type="number" name="thang" min="1" max="12" required>

        <label>Năm:</label>
        <input type="number" name="nam" value="<?= date('Y') ?>" required>

        <label>Chỉ số điện cũ:</label>
        <input type="number" name="chisodiencu" required>

        <label>Chỉ số điện mới:</label>
        <input type="number" name="chisodienmoi" required>

        <label>Chỉ số nước cũ:</label>
        <input type="number" name="chisonuoccu" required>

        <label>Chỉ số nước mới:</label>
        <input type="number" name="chisonuocmoi" required>

        <label>Ghi chú:</label>
        <textarea name="ghichu" rows="3"></textarea>

        <button type="submit">💾 Lưu chỉ số</button>
    </form>
</div>
</body>
</html>
