<?php
session_start();
include 'db.php';

// Chỉ sinh viên hoặc admin có thể xem trang này — tùy nhu cầu
if (!isset($_SESSION['username'])) {
    die("❌ Bạn chưa đăng nhập.");
}
$my_role = $_SESSION['role'] ?? 'sinhvien';
if ($my_role !== 'sinhvien') {
    // Nếu muốn admin cũng xem yêu cầu cá nhân, cho phép. Nếu không, die()
    // die("❌ Chỉ sinh viên mới xem trang này.");
}

// Cần có id sinh viên trong session
$sinhvien_id = $_SESSION['id'] ?? null;
if (!$sinhvien_id) {
    die("❌ Không tìm thấy ID sinh viên trong session. Vui lòng đăng nhập lại.");
}

// Xử lý hủy (cancel) yêu cầu (POST)
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $action = $_POST['action'];
    $req_id = (int)$_POST['id'];

    if ($action === 'cancel') {
        // Chỉ cho hủy khi còn pending
        $stmt = $conn->prepare("SELECT trangthai FROM yeucau_dangky WHERE id = ? AND sinhvien_id = ?");
        $stmt->bind_param("ii", $req_id, $sinhvien_id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if (!$r) {
            $msg = "❌ Yêu cầu không tồn tại.";
        } elseif ($r['trangthai'] !== 'pending') {
            $msg = "⚠️ Chỉ yêu cầu trạng thái 'pending' mới có thể huỷ.";
        } else {
            $u = $conn->prepare("UPDATE yeucau_dangky SET trangthai='rejected', nguoi_duyet='(hủy bởi sinh viên)', ngay_duyet=NOW() WHERE id = ? AND sinhvien_id = ?");
            $u->bind_param("ii", $req_id, $sinhvien_id);
            if ($u->execute()) {
                $msg = "✅ Yêu cầu đã được hủy.";
            } else {
                $msg = "❌ Lỗi khi hủy: " . $u->error;
            }
        }
    }
}

// Lấy danh sách yêu cầu của sinh viên
$stmt = $conn->prepare("SELECT y.id, y.phong_id, p.tenphong, y.ghichu, y.ngay_gui, y.trangthai, y.nguoi_duyet, y.ngay_duyet
                        FROM yeucau_dangky y
                        JOIN phong p ON y.phong_id = p.id
                        WHERE y.sinhvien_id = ?
                        ORDER BY y.ngay_gui DESC");
$stmt->bind_param("i", $sinhvien_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="content-box">
    <h4>📌 Yêu cầu đăng ký phòng của tôi</h4>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <p>Xin chào <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> — bạn có thể xem trạng thái các yêu cầu ở đây.</p>

    <table class="table table-sm table-bordered">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Phòng</th>
                <th>Ngày gửi</th>
                <th>Ghi chú</th>
                <th>Trạng thái</th>
                <th>Người duyệt / Ngày duyệt</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($res && $res->num_rows>0): while($r=$res->fetch_assoc()): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['tenphong']) ?></td>
                <td><?= $r['ngay_gui'] ?></td>
                <td><?= htmlspecialchars($r['ghichu']) ?></td>
                <td>
                    <?php
                        if ($r['trangthai']=='pending') echo "<span class='text-warning'>Pending</span>";
                        elseif ($r['trangthai']=='approved') echo "<span class='text-success'>Approved</span>";
                        else echo "<span class='text-danger'>Rejected</span>";
                    ?>
                </td>
                <td><?= htmlspecialchars($r['nguoi_duyet'] ?? '') ?> <?= $r['ngay_duyet'] ? '<br>'. $r['ngay_duyet'] : '' ?></td>
                <td>
                    <?php if ($r['trangthai']=='pending'): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Bạn muốn hủy yêu cầu này?');">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button class="btn btn-sm btn-outline-danger">Hủy</button>
                        </form>
                    <?php else: ?>
                        <small>—</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="text-muted">Bạn chưa gửi yêu cầu nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
