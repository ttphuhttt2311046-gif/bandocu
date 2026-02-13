<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ VN cho PHP
// 1. Kiểm tra session và kết nối
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra lại đường dẫn file db.php của bạn (quan trọng)
include "../db.php"; 
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    die("Từ chối truy cập! Bạn không phải Admin.");
}

// 2. Xử lý Thêm sự kiện khi bấm nút submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $ten = $_POST['tenSuKien'];
    $phanTram = intval($_POST['mucGiamGia']);
    $start = $_POST['ngayBatDau'];
    $end = $_POST['ngayKetThuc'];

    // Câu lệnh INSERT - Kiểm tra kỹ tên cột khớp với DB ở trên
    $sql = "INSERT INTO sukien_khuyenmai (tenSuKien, mucGiamGia, ngayBatDau, ngayKetThuc, trangThai) VALUES (?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Lỗi SQL: " . $conn->error); // Hiện lỗi nếu sai tên cột hoặc bảng
    }
    
    $stmt->bind_param("siss", $ten, $phanTram, $start, $end);
    
    if ($stmt->execute()) {
        echo "<script>alert('Đã tạo sự kiện: $ten thành công!'); window.location.href='dashboard.php?page=manage_events.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi thực thi: " . $stmt->error . "</div>";
    }
}

// 3. Lấy danh sách sự kiện để hiển thị
$events = $conn->query("SELECT * FROM sukien_khuyenmai ORDER BY id DESC");
?>

<div class="container-fluid">
    <h3 class="h3 mb-4 text-gray-800">Quản lý Sự kiện Giảm giá</h3>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tạo sự kiện mới</h6>
        </div>
        <div class="card-body">
            <form action="manage_events.php" method="POST" id="formAddEvent">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label>Tên sự kiện</label>
                        <input type="text" name="tenSuKien" class="form-control" placeholder="VD: Tết 2026" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>% Giảm</label>
                        <input type="number" name="mucGiamGia" class="form-control" min="1" max="100" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Bắt đầu</label>
                        <input type="datetime-local" name="ngayBatDau" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Kết thúc</label>
                        <input type="datetime-local" name="ngayKetThuc" class="form-control" required>
                    </div>
                    <div class="col-md-1 mb-2">
                        <label>&nbsp;</label>
                        <button type="submit" name="add_event" class="btn btn-primary btn-block">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Tên sự kiện</th>
                    <th>Mức giảm</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($events && $events->num_rows > 0): ?>
                    <?php while($row = $events->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['tenSuKien']) ?></strong></td>
                        <td><span class="text-danger">-<?= $row['mucGiamGia'] ?>%</span></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['ngayBatDau'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['ngayKetThuc'])) ?></td>
                        <td>
                            <?php 
                            $now = time();
                            $start_ts = strtotime($row['ngayBatDau']);
                            $end_ts = strtotime($row['ngayKetThuc']);
                            if ($now < $start_ts) echo '<span class="badge badge-warning">Chờ tới giờ</span>';
                            elseif ($now > $end_ts) echo '<span class="badge badge-secondary">Đã kết thúc</span>';
                            else echo '<span class="badge badge-success">Đang diễn ra</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Chưa có sự kiện nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    $("#formAddEvent").on("submit", function(e) {
        e.preventDefault(); // Chặn load lại trang
        
        $.ajax({
            url: "manage_events.php",
            type: "POST",
            data: $(this).serialize() + "&add_event=1",
            success: function(response) {
                alert("Thao tác thành công!");
                // Load lại chính trang này vào vùng hiển thị
                $(".load-page[data-page='manage_events.php']").click();
            },
            error: function() {
                alert("Có lỗi xảy ra khi gửi dữ liệu!");
            }
        });
    });
});
</script>