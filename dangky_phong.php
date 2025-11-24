<?php
session_start();
include 'db.php';

// Chỉ sinh viên mới truy cập
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'sinhvien') {
    die("❌ Bạn không có quyền truy cập trang này.");
}

// Lấy thông tin sinhvien (nếu bạn lưu id trong session)
$sinhvien_username = $_SESSION['username'];
// Nếu bạn cũng lưu id: $_SESSION['id']
$sinhvien_id = $_SESSION['id'] ?? null;

// Lấy các phòng còn chỗ trống
$sql = "
    SELECT p.id, p.tenphong, p.songuoitoida, COUNT(h.id) AS so_dang_o
    FROM phong p
    LEFT JOIN hopdong h ON p.id = h.phongid
    GROUP BY p.id, p.tenphong, p.songuoitoida
    HAVING p.songuoitoida - COUNT(h.id) > 0
    ORDER BY p.tenphong
";
$res = $conn->query($sql);

// Xử lý submit
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phong_id = (int)$_POST['phong_id'];
    $ghichu = trim($_POST['ghichu'] ?? '');

    if (!$sinhvien_id) {
        $msg = "❌ Không tìm thấy ID sinh viên trong session.";
    } else {
        // Kiểm tra đã có yêu cầu đang chờ cho sinh viên này không
        $chk = $conn->prepare("SELECT COUNT(*) AS cnt FROM yeucau_dangky WHERE sinhvien_id = ? AND trangthai = 'pending'");
        $chk->bind_param("i", $sinhvien_id);
        $chk->execute();
        $cnt = $chk->get_result()->fetch_assoc()['cnt'] ?? 0;
        if ($cnt > 0) {
            $msg = "⚠️ Bạn đã có 1 yêu cầu đang chờ. Vui lòng đợi admin duyệt.";
        } else {
            // Chèn yêu cầu
            $ins = $conn->prepare("INSERT INTO yeucau_dangky (sinhvien_id, sinhvien_username, phong_id, ghichu) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isis", $sinhvien_id, $sinhvien_username, $phong_id, $ghichu);
            if ($ins->execute()) {
                $msg = "✅ Gửi yêu cầu thành công. Vui lòng chờ admin duyệt.";
            } else {
                $msg = "❌ Lỗi khi gửi yêu cầu: " . $ins->error;
            }
        }
    }
}
?>

<div class="content-box">
    <h4>📝 Đăng ký phòng</h4>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:700px;">
        <div class="mb-3">
            <label class="form-label">Chọn phòng (chỉ hiện phòng còn trống)</label>
            <select name="phong_id" class="form-select" required>
                <option value="">-- Chọn phòng --</option>
                <?php while ($p = $res->fetch_assoc()): 
                    $con_trong = $p['songuoitoida'] - $p['so_dang_o'];
                ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['tenphong']) ?> — còn <?= $con_trong ?> chỗ
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú (lý do đăng ký / thông tin bổ sung)</label>
            <textarea name="ghichu" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-primary">Gửi yêu cầu</button>
    </form>

    <hr>
    <h5>📌 Lịch sử yêu cầu của bạn</h5>
    <?php
    $hist = $conn->prepare("SELECT y.id, p.tenphong, y.ngay_gui, y.trangthai, y.ghichu, y.nguoi_duyet, y.ngay_duyet FROM yeucau_dangky y JOIN phong p ON y.phong_id = p.id WHERE y.sinhvien_id = ? ORDER BY y.ngay_gui DESC");
    $hist->bind_param("i", $sinhvien_id);
    $hist->execute();
    $histRes = $hist->get_result();
    ?>
    <table class="table table-sm table-bordered mt-3">
        <thead><tr><th>Phòng</th><th>Ngày gửi</th><th>Trạng thái</th><th>Người duyệt</th><th>Ngày duyệt</th><th>Ghi chú</th></tr></thead>
        <tbody>
            <?php if ($histRes && $histRes->num_rows>0): ?>
                <?php while($h=$histRes->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['tenphong']) ?></td>
                        <td><?= $h['ngay_gui'] ?></td>
                        <td><?= $h['trangthai'] ?></td>
                        <td><?= htmlspecialchars($h['nguoi_duyet'] ?? '') ?></td>
                        <td><?= $h['ngay_duyet'] ?? '' ?></td>
                        <td><?= htmlspecialchars($h['ghichu']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-muted">Chưa có yêu cầu nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
