<?php
session_start();
include 'db.php';

// Chỉ admin được vào
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("❌ Bạn không có quyền truy cập.");
}

// Lấy danh sách yêu cầu
$sql = "SELECT y.*, p.tenphong, s.hoten AS ten_sinhvien, s.id AS sv_id
        FROM yeucau_dangky y
        JOIN phong p ON y.phong_id = p.id
        LEFT JOIN sinhvien s ON y.sinhvien_id = s.id
        ORDER BY y.trangthai ASC, y.ngay_gui DESC";
$res = $conn->query($sql);

// Xử lý hành động approve / reject qua GET (hoặc POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $admin_user = $_SESSION['username'];

    if ($action === 'approve') {
        // Lấy yêu cầu
        $q = $conn->prepare("SELECT * FROM yeucau_dangky WHERE id = ?");
        $q->bind_param("i", $id);
        $q->execute();
        $rq = $q->get_result()->fetch_assoc();
        if (!$rq) { $err = "Yêu cầu không tồn tại."; }
        else {
            // Kiểm tra phòng còn chỗ không
            $chk = $conn->prepare("SELECT songuoitoida, (SELECT COUNT(*) FROM hopdong WHERE phongid = ?) AS dang_o FROM phong WHERE id = ?");
            $chk->bind_param("ii", $rq['phong_id'], $rq['phong_id']);
            $chk->execute();
            $c = $chk->get_result()->fetch_assoc();
            $con = $c['songuoitoida'] - $c['dang_o'];
            if ($con <= 0) {
                $err = "Phòng đã đầy, không thể duyệt yêu cầu này.";
            } else {
                // Tạo hợp đồng (đơn giản: ngaybatdau = today, ngayketthuc null) — chỉnh theo yêu cầu của bạn
                $sv_id = $rq['sinhvien_id'];
                $phongid = $rq['phong_id'];
                $now = date('Y-m-d');

                // Tùy DB của bạn: nếu muốn thêm sinhvien vào table sinhvien thì đảm bảo đã có
                $ins = $conn->prepare("INSERT INTO hopdong (sinhvienid, phongid, ngaybatdau) VALUES (?, ?, ?)");
                $ins->bind_param("iis", $sv_id, $phongid, $now);
                if ($ins->execute()) {
                    // Cập nhật trạng thái yêu cầu
                    $u = $conn->prepare("UPDATE yeucau_dangky SET trangthai='approved', nguoi_duyet=?, ngay_duyet=NOW() WHERE id = ?");
                    $u->bind_param("si", $admin_user, $id);
                    $u->execute();
                    header("Location: yeucau_danhsach.php");
                    exit();
                } else {
                    $err = "Lỗi tạo hợp đồng: " . $ins->error;
                }
            }
        }
    } elseif ($action === 'reject') {
        $u2 = $conn->prepare("UPDATE yeucau_dangky SET trangthai='rejected', nguoi_duyet=?, ngay_duyet=NOW() WHERE id = ?");
        $u2->bind_param("si", $admin_user, $id);
        $u2->execute();
        header("Location: yeucau_danhsach.php");
        exit();
    }
}
?>

<div class="content-box">
    <h4>📥 Danh sách yêu cầu đăng ký phòng</h4>

    <?php if (isset($err)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>ID</th><th>Sinh viên</th><th>Phòng</th><th>Ngày gửi</th><th>Ghi chú</th><th>Trạng thái</th><th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res && $res->num_rows>0): while($r=$res->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['sinhvien_username']) ?> <?= isset($r['ten_sinhvien']) ? ' - '.htmlspecialchars($r['ten_sinhvien']) : '' ?></td>
                    <td><?= htmlspecialchars($r['tenphong']) ?></td>
                    <td><?= $r['ngay_gui'] ?></td>
                    <td><?= htmlspecialchars($r['ghichu']) ?></td>
                    <td><?= $r['trangthai'] ?></td>
                    <td>
                        <?php if ($r['trangthai'] === 'pending'): ?>
                            <a class="btn btn-success btn-sm" href="yeucau_danhsach.php?action=approve&id=<?= $r['id'] ?>" onclick="return confirm('Duyệt yêu cầu?')">Duyệt</a>
                            <a class="btn btn-danger btn-sm" href="yeucau_danhsach.php?action=reject&id=<?= $r['id'] ?>" onclick="return confirm('Từ chối yêu cầu?')">Từ chối</a>
                        <?php else: ?>
                            <small><?= htmlspecialchars($r['nguoi_duyet'] ?? '') ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-muted">Chưa có yêu cầu nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
