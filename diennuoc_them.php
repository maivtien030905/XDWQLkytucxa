<?php
include 'db.php';

// Lấy danh sách phòng
$phong_result = $conn->query("SELECT id, tenphong FROM phong");

// Lấy giá điện nước mới nhất
$gia_query = "SELECT giadien, gianuoc FROM giadichvu ORDER BY ngayapdung DESC LIMIT 1";
$gia_result = $conn->query($gia_query);
$gia = $gia_result->fetch_assoc();
$giadien = $gia ? $gia['giadien'] : 0;
$gianuoc = $gia ? $gia['gianuoc'] : 0;

$success = $error = "";

// Xử lý form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phongid = $_POST['phongid'] ?? null;
    $thang = $_POST['thang'] ?? null;
    $nam = $_POST['nam'] ?? null;
    $chisodiencu = $_POST['chisodiencu'] ?? null;
    $chisodienmoi = $_POST['chisodienmoi'] ?? null;
    $chisonuoccu = $_POST['chisonuoccu'] ?? null;
    $chisonuocmoi = $_POST['chisonuocmoi'] ?? null;
    $ngaycapnhat = date('Y-m-d');

    if ($phongid && $chisodienmoi >= $chisodiencu && $chisonuocmoi >= $chisonuoccu) {
        $sql = "INSERT INTO diennuoc (phongid, thang, nam, chisodiencu, chisodienmoi, chisonuoccu, chisonuocmoi, ngaycapnhat)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iiiiiiis", $phongid, $thang, $nam, $chisodiencu, $chisodienmoi, $chisonuoccu, $chisonuocmoi, $ngaycapnhat);
            if ($stmt->execute()) {
                $success = "✅ Thêm chỉ số điện nước thành công!";
            } else {
                $error = "❌ Lỗi khi thêm dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "❌ Lỗi prepare: " . $conn->error;
        }
    } else {
        $error = "⚠️ Vui lòng nhập dữ liệu hợp lệ (chỉ số mới ≥ chỉ số cũ)!";
    }
}
?>

<div class="content-box">
    <h4 class="fw-bold text-primary mb-3">⚡ Thêm chỉ số điện nước</h4>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Phòng</label>
            <select name="phongid" class="form-select" required>
                <option value="">-- Chọn phòng --</option>
                <?php while ($row = $phong_result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['tenphong']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Tháng</label>
            <input type="number" name="thang" min="1" max="12" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Năm</label>
            <input type="number" name="nam" min="2000" value="<?= date('Y') ?>" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Chỉ số điện cũ</label>
            <input type="number" name="chisodiencu" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Chỉ số điện mới</label>
            <input type="number" name="chisodienmoi" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Chỉ số nước cũ</label>
            <input type="number" name="chisonuoccu" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Chỉ số nước mới</label>
            <input type="number" name="chisonuocmoi" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Giá điện hiện tại (VNĐ/kWh)</label>
            <input type="text" class="form-control" value="<?= number_format($giadien, 0, ',', '.') ?>" readonly>
        </div>

        <div class="col-md-6">
            <label class="form-label">Giá nước hiện tại (VNĐ/m³)</label>
            <input type="text" class="form-control" value="<?= number_format($gianuoc, 0, ',', '.') ?>" readonly>
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">💾 Thêm mới</button>
            <button type="button" class="btn btn-secondary" onclick="loadPage('diennuoc_danhsach.php')">↩️ Quay lại</button>
        </div>
    </form>
</div>
