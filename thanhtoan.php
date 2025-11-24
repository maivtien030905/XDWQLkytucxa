<?php
include 'db.php';

$id = $_GET['id'];
$thang = $_GET['thang'];
$nam = $_GET['nam'];

$sql = "UPDATE diennuoc SET trangthai='Đã thanh toán' 
        WHERE phongid='$id' AND thang='$thang' AND nam='$nam'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('💰 Đã đánh dấu thanh toán thành công!');
          window.location.href='thongke.php?thang=$thang&nam=$nam';</script>";
} else {
    echo "<script>alert('❌ Lỗi khi cập nhật trạng thái!');</script>";
}
?>
