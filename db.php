<?php
// ====== CẤU HÌNH KẾT NỐI DATABASE ======
$servername = "localhost";   
$username   = "root";        
$password   = "Mot23456?";   
$dbname     = "ktx_db";      
$port       = 3309;         


$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// ====== KIỂM TRA KẾT NỐI ======
if (!$conn) {
    die("❌ Kết nối database thất bại: " . mysqli_connect_error());
}

// ====== THIẾT LẬP BẢNG MÃ ======
mysqli_set_charset($conn, "utf8");


?>
