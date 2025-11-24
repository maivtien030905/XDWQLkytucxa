<?php
session_start();
include 'db.php';

// Nếu chưa đăng nhập → quay lại login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Lấy role người dùng
$role = $_SESSION['role'] ?? 'sinhvien';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống quản lý ký túc xá DNU</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reset mặc định */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%; width: 100%; font-family: 'Inter', sans-serif;
            background: url('uploads/background.png') no-repeat center center fixed;
            background-size: cover;
            overflow: hidden;
        }
        body::before {
            content: "";
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255,255,255,0.75);
            z-index: -1;
        }

        .layout { display: flex; height: 100%; width: 100%; }

        /* Sidebar */
        .sidebar {
            width: 240px; background-color: #0056A1; color: white;
            display: flex; flex-direction: column; justify-content: space-between;
            position: fixed; top: 0; left: 0; bottom: 0;
            box-shadow: 2px 0 8px rgba(0,0,0,0.15); padding-top: 20px;
        }
        .sidebar h3 { text-align: center; font-weight: 700; margin-bottom: 25px; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li { margin-bottom: 8px; }
        .sidebar ul li a {
            display: block; color: white; text-decoration: none;
            padding: 12px 20px; border-radius: 6px; font-weight: 500;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #004080; }
        .sidebar .logout { background-color: #d9534f; margin: 20px; text-align: center; border-radius: 6px; }
        .sidebar .logout:hover { background-color: #c9302c; }

        /* Nội dung */
        .content {
            flex: 1; margin-left: 240px; padding: 30px;
            height: 100vh; overflow-y: auto;
        }
        .welcome {
            background: rgba(255,255,255,0.9); border-radius: 12px; padding: 40px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1); text-align: center; margin-top: 100px;
        }
        .loading { text-align: center; font-size: 18px; color: #333; padding: 50px 0; }
    </style>
</head>

<body>
<div class="layout">

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h3>🏫 KTX DNU</h3>
            <ul>

                <!-- Chung cho mọi quyền -->
                <li><a href="#" onclick="loadPage('phong.php')" class="active">📋 Danh sách phòng</a></li>

                <!-- Chỉ ADMIN được thấy -->
<?php if ($role == 'admin'): ?>
    <li><a href="#" onclick="loadPage('hopdong_them.php')">🧍 Thêm sinh viên</a></li>
    <li><a href="#" onclick="loadPage('hopdong_danhsach.php')">🧾 Danh sách hợp đồng</a></li>
    <li><a href="#" onclick="loadPage('phong_them.php')">➕ Thêm phòng</a></li>
<?php endif; ?>

<!-- Cả admin và sinhvien đều thấy -->
<li><a href="#" onclick="loadPage('lichsu_doi_phong.php')">📜 Lịch sử đổi phòng</a></li>
<li><a href="#" onclick="loadPage('thongke.php')">📊 Thống kê điện nước</a></li>
<!-- Cho sinh viên -->
<?php if ($role == 'sinhvien'): ?>
<li><a href="#" onclick="loadPage('yeucau_cuatoi.php')">📥 Yêu cầu của tôi</a></li>
<li><a href="#" onclick="loadPage('dangky_phong.php')">📝 Đăng ký phòng</a></li>
<?php endif; ?>

<!-- Cho admin -->
<?php if ($_SESSION['role']=='admin'): ?>
<li><a href="#" onclick="loadPage('yeucau_danhsach.php')">📋 Quản lý yêu cầu</a></li>
<?php endif; ?>
            </ul>
        </div>

        <a href="logout.php" class="logout">🚪 Đăng xuất</a>
    </div>

    <div class="content" id="content-area">
        <div class="welcome">
            <h2>🎓 Chào mừng <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Quyền hiện tại: <b><?= $role ?></b></p>
        </div>
    </div>

</div>

<!-- Load nội dung -->
<script>
function loadPage(page) {
    const area = document.getElementById('content-area');
    area.innerHTML = '<div class="loading">⏳ Đang tải...</div>';
    fetch(page)
        .then(res => res.text())
        .then(data => {
            area.innerHTML = data;
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.querySelector(`.sidebar a[onclick="loadPage('${page}')"]`)?.classList.add('active');
        })
        .catch(err => {
            area.innerHTML = "<p class='text-danger'>⚠️ Lỗi khi tải trang!</p>";
            console.error(err);
        });
}
</script>

</body>
</html>
