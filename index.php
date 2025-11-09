<?php
session_start();
include 'db.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống quản lý ký túc xá DNU</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="layout">

    <!-- 🌐 Sidebar cố định -->
    <div class="sidebar">
        <h3>🏫 KTX DNU</h3>
        <ul>
            <li><a href="#" onclick="loadPage('phong.php')" class="active">📋 Danh sách phòng</a></li>
            <li><a href="#" onclick="loadPage('hopdong_them.php')">🧍 Thêm sinh viên</a></li>
            <li><a href="#" onclick="loadPage('hopdong_danhsach.php')">🧾 Danh sách hợp đồng</a></li>
            <li><a href="#" onclick="loadPage('lichsu_doi_phong.php')">📜 Lịch sử đổi phòng</a></li>
            <li><a href="#" onclick="loadPage('phong_them.php')">➕ Thêm phòng</a></li>
            <li><a href="thongke.php">📊 Thống kê điện nước</a></li>
            <li><a href="logout.php" class="logout">🚪 Đăng xuất</a></li>
        </ul>
    </div>

    <!-- 🧱 Khu vực nội dung thay đổi -->
    <div class="content" id="content-area">
        <div class="welcome">
            <h2>🎓 Chào mừng <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Hệ thống quản lý ký túc xá DNU</p>
        </div>
    </div>

</div>

<!-- ⚙️ JavaScript để load nội dung động -->
<script>
function loadPage(page) {
    const area = document.getElementById('content-area');
    area.innerHTML = '<div class="loading">⏳ Đang tải...</div>';
    fetch(page)
        .then(res => res.text())
        .then(data => {
            area.innerHTML = data;
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.querySelector(`.sidebar a[onclick="loadPage('${page}')"]`).classList.add('active');
        })
        .catch(err => {
            area.innerHTML = "<p class='text-danger'>⚠️ Lỗi khi tải trang!</p>";
            console.error(err);
        });
}
</script>

</body>
</html>
