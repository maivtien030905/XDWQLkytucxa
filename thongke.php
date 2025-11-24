<?php
include 'db.php';

// 🗓️ Lấy tháng, năm
$thang = isset($_GET['thang']) ? (int)$_GET['thang'] : date('m');
$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : date('Y');

// ⚡ Lấy giá điện nước mới nhất
$sqlGia = "SELECT giadien, gianuoc FROM giadichvu ORDER BY ngayapdung DESC LIMIT 1";
$resultGia = mysqli_query($conn, $sqlGia);
$giadichvu = mysqli_fetch_assoc($resultGia);
$giadien = $giadichvu['giadien'] ?? 0;
$gianuoc = $giadichvu['gianuoc'] ?? 0;

// 🧾 Thêm chỉ số điện nước
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_diennuoc'])) {
    $phongid = $_POST['phongid'];
    $chisodiencu = $_POST['chisodiencu'];
    $chisodienmoi = $_POST['chisodienmoi'];
    $chisonuoccu = $_POST['chisonuoccu'];
    $chisonuocmoi = $_POST['chisonuocmoi'];
    $ngaycapnhat = date('Y-m-d');

    $sqlInsert = "INSERT INTO diennuoc (phongid, thang, nam, chisodiencu, chisodienmoi, chisonuoccu, chisonuocmoi, ngaycapnhat, trangthai)
                  VALUES ('$phongid', '$thang', '$nam', '$chisodiencu', '$chisodienmoi', '$chisonuoccu', '$chisonuocmoi', '$ngaycapnhat', 'Chưa thanh toán')";

    if (mysqli_query($conn, $sqlInsert)) {
        echo "<script>alert('✅ Thêm chỉ số điện nước thành công!'); window.location='thongke.php?thang=$thang&nam=$nam';</script>";
    } else {
        echo "<div style='color:red;padding:10px;'>❌ Lỗi SQL: " . mysqli_error($conn) . "</div>";
    }
}

// 💸 Thanh toán
if (isset($_GET['thanhtoan'])) {
    $phongid = $_GET['thanhtoan'];
    $sqlThanhToan = "UPDATE diennuoc SET trangthai='Đã thanh toán' WHERE phongid='$phongid' AND thang='$thang' AND nam='$nam'";
    mysqli_query($conn, $sqlThanhToan);
    echo "<script>alert('💰 Thanh toán thành công!'); window.location='thongke.php?thang=$thang&nam=$nam';</script>";
}

// 📊 Dữ liệu hiển thị
$sql = "SELECT phongid, chisodiencu, chisodienmoi, chisonuoccu, chisonuocmoi, trangthai
        FROM diennuoc WHERE thang=$thang AND nam=$nam";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>📊 Thống kê điện nước</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="content container mt-4">
<h2 class="text-center text-primary mb-4">📊 Thống kê điện nước tháng <?= $thang ?>/<?= $nam ?></h2>

<!-- Form thêm -->
<div class="text-end mb-3">
<a href="hoadon_thanhtoan.php" class="btn btn-success">💰 Thanh toán hóa đơn</a>
</div>

<div id="formThemDienNuoc" class="collapse mb-4">
    <form method="POST" class="border p-4 rounded bg-light">
        <h5 class="text-center text-success mb-3">Thêm chỉ số điện nước</h5>
        <div class="row g-3">
            <div class="col-md-2"><input type="text" name="phongid" class="form-control" placeholder="Phòng ID" required></div>
            <div class="col-md-2"><input type="number" name="chisodiencu" class="form-control" placeholder="Điện cũ" required></div>
            <div class="col-md-2"><input type="number" name="chisodienmoi" class="form-control" placeholder="Điện mới" required></div>
            <div class="col-md-2"><input type="number" name="chisonuoccu" class="form-control" placeholder="Nước cũ" required></div>
            <div class="col-md-2"><input type="number" name="chisonuocmoi" class="form-control" placeholder="Nước mới" required></div>
            <div class="col-md-2"><button type="submit" name="them_diennuoc" class="btn btn-success w-100">Thêm</button></div>
        </div>
    </form>
</div>

<!-- Bảng -->
<div class="table-responsive">
<table class="table table-bordered table-hover text-center align-middle">
<thead class="table-primary">
<tr>
<th>Phòng</th><th>Điện tiêu thụ</th><th>Tiền điện</th><th>Nước tiêu thụ</th><th>Tiền nước</th><th>Tổng</th><th>Trạng thái</th><th>Hành động</th>
</tr>
</thead>
<tbody>
<?php
$tongtien=0;
if($result && mysqli_num_rows($result)>0){
    while($row=mysqli_fetch_assoc($result)){
        $dien=$row['chisodienmoi']-$row['chisodiencu'];
        $nuoc=$row['chisonuocmoi']-$row['chisonuoccu'];
        $tiendien=$dien*$giadien;
        $tiennuoc=$nuoc*$gianuoc;
        $tong=$tiendien+$tiennuoc;
        $tongtien+=$tong;
        $trangthai=$row['trangthai']??'Chưa thanh toán';

        echo "<tr>
            <td>{$row['phongid']}</td>
            <td>{$dien}</td>
            <td>".number_format($tiendien,0,',','.')."</td>
            <td>{$nuoc}</td>
            <td>".number_format($tiennuoc,0,',','.')."</td>
            <td><b>".number_format($tong,0,',','.')."</b></td>
            <td>$trangthai</td>
            <td>";
        if($trangthai=='Chưa thanh toán'){
            echo "<a href='?thanhtoan={$row['phongid']}&thang=$thang&nam=$nam' class='btn btn-sm btn-success'>💰 Thanh toán</a>";
        }else{
            echo "<span class='text-success'>✅ Đã TT</span>";
        }
        echo "</td></tr>";
    }
}else{
    echo "<tr><td colspan='8' class='text-muted'>Không có dữ liệu</td></tr>";
}
?>
</tbody>
<tfoot><tr><td colspan="6" class="text-end fw-bold">Tổng:</td><td colspan="2"><?=number_format($tongtien,0,',','.')?> VNĐ</td></tr></tfoot>
</table>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
