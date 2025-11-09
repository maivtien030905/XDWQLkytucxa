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
    <title>Dashboard Quản lý Ký túc xá</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background: url('uploads/background.png') no-repeat center center fixed;
            background-size: cover;
        }

        .main-layout {
            display: flex;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(3px);
        }

        .sidebar {
            width: 250px;
            background-color: #004080;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
        }

        .sidebar h4 {
            color: #fff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
        }

        .sidebar .logout {
            margin-top: auto;
            background-color: #dc3545;
            text-align: center;
        }

        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        #content-area {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            min-height: 85vh;
        }
    </style>
</head>
<body>

<div class="main-layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>🏠 Ký túc xá</h4>
        <a href="index_content.php" class="active">📋 Danh sách phòng</a>
        <a href="phong_them.php">➕ Thêm phòng</a>
        <a href="hopdong_them.php">👨‍🎓 Thêm sinh viên</a>
        <a href="hopdong_danhsach.php">📑 Danh sách hợp đồng</a>
        <a href="lichsu_doi_phong.php">🔄 Lịch sử đổi phòng</a>
        <li><a href="thongke.php">📊 Thống kê điện nước</a></li>

        <a href="logout.php" class="logout">🚪 Đăng xuất</a>
    </div>

    <!-- Nội dung -->
    <div class="content">
        <div id="content-area">
            <!-- Nội dung các trang sẽ load ở đây -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contentArea = document.getElementById('content-area');
    const links = document.querySelectorAll('.sidebar a:not(.logout)');

    function loadPage(url) {
        fetch(url)
            .then(res => res.text())
            .then(html => {
                contentArea.innerHTML = html;
                window.history.pushState({ path: url }, '', url);
            });
    }

    // Gắn sự kiện click cho các link
    links.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            links.forEach(a => a.classList.remove('active'));
            link.classList.add('active');
            loadPage(link.getAttribute('href'));
        });
    });

    // Load trang mặc định khi mở dashboard
    loadPage('index_content.php');
});
</script>

</body>
</html>
