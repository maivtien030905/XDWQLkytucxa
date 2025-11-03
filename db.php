<?php
// ====== CẤU HÌNH KẾT NỐI DATABASE ======
$servername = "localhost";   // Máy chủ cục bộ
$username   = "root";        // Tài khoản mặc định của XAMPP
$password   = "Mot23456?";   // Mật khẩu MySQL của Nini
$dbname     = "ktx_db";      // Tên database thật
$port       = 3309;          // Cổng MySQL của máy Nini

// ====== TẠO KẾT NỐI ======
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// ====== KIỂM TRA KẾT NỐI ======
if (!$conn) {
    die("❌ Kết nối database thất bại: " . mysqli_connect_error());
}

// ====== THIẾT LẬP BẢNG MÃ ======
mysqli_set_charset($conn, "utf8");

// (✅) Nếu muốn test nhanh, có thể bỏ comment dòng dưới
// echo "✅ Kết nối thành công!";
?>
