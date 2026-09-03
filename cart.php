<?php 
ini_set('session.cookie_path', '/');
session_start();
include "db.php";

$action = $_GET['action'] ?? '';

if ($action === 'add') {

    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập'); window.location='admin/login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $id  = intval($_GET['id'] ?? 0);
    $qty = isset($_GET['qty']) ? max(1, intval($_GET['qty'])) : 1;

    // kiểm tra sản phẩm
    $stmt = $conn->prepare("SELECT maSanPham FROM sanpham WHERE maSanPham=?");
    if (!$stmt) die($conn->error);

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res->fetch_assoc()) {
        echo "ERROR";
        exit;
    }

    // thêm / cộng dồn
    $stmt = $conn->prepare("
        INSERT INTO giohang(maNguoiDung, maSanPham, soLuong)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE soLuong = soLuong + VALUES(soLuong)
    ");
    $stmt->bind_param("iii", $user_id, $id, $qty);
    $stmt->execute();

    echo "OK";
    exit;
}


if ($action === 'update-ajax' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    $id = intval($_POST['id']);
    $qty = max(1, intval($_POST['qty']));

    $stmt = $conn->prepare("
        UPDATE giohang 
        SET soLuong=? 
        WHERE maNguoiDung=? AND maSanPham=?
    ");
    $stmt->bind_param("iii", $qty, $user_id, $id);
    $stmt->execute();

    exit;
}


if ($action === 'remove') {
    $user_id = $_SESSION['user_id'];
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("
        DELETE FROM giohang 
        WHERE maNguoiDung=? AND maSanPham=?
    ");
    $stmt->bind_param("ii", $user_id, $id);
    $stmt->execute();

    // Thêm ?t=time() để ép trình duyệt tải lại dữ liệu mới nhất, không dùng cache
    header("Location: cart.php?t=" . time());
    exit;
}


if ($action === 'checkout-selected' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $ids = json_decode($_POST['ids'], true);
    $_SESSION['cart_temp'] = $ids;

    echo "OK";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giỏ hàng</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <header class="topbar">
    <div class="container">
      <div class="logo-title">
  <h1><a href="index.php">Shop Đồ Cũ</a></h1>
</div>
      <div class="nav">
        <?php

?>
<a href="orders.php">🧾 Lịch sử đơn hàng</a>
<a href="hoadon.php">Hóa Đơn</a>
 <?php 
        if (isset($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
            $res = $conn->query("SELECT SUM(soLuong) as total FROM giohang WHERE maNguoiDung = $uid");
            $row = $res->fetch_assoc();
            $count = $row['total'] ?? 0;
        } else $count = 0;
        ?>
        <a href="cart.php">🛒Giỏ hàng (<?php echo $count; ?>)</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['vaitro']) && ($_SESSION['vaitro'] === 'seller' || $_SESSION['vaitro'] === 'admin')): ?>
                <a href="admin/index.php">Quản lý sản phẩm</a>

            <?php endif; ?>
<span style="color:#fff;">
  Xin chào, <?php echo htmlspecialchars($_SESSION['tenNguoiDung'] ?? ''); ?>
</span>
            <a href="admin/logout.php">Đăng xuất</a>

        <?php else: ?>
            <a href="admin/login.php">Đăng nhập</a>
        <?php endif; ?>
      </div>
    </div>
    
  </header>

<main class="container">
<h2>Giỏ hàng của bạn</h2>

<?php
if (!isset($_SESSION['user_id'])) {
    echo "<p>Vui lòng đăng nhập</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT g.maSanPham, g.soLuong, s.tenSanPham, s.gia, s.hinhAnh
FROM giohang g
JOIN sanpham s ON g.maSanPham = s.maSanPham
WHERE g.maNguoiDung = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) die("SQL lỗi: " . $conn->error);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
?>

<table class="cart-table">
<thead>
<tr>
    <th>Chọn</th>
    <th>Ảnh</th>
    <th>Sản phẩm</th>
    <th>Giá</th>
    <th>Số lượng</th>
    <th>Tạm tính</th>
    <th></th>
</tr>
</thead>

<tbody>

<?php while ($item = $result->fetch_assoc()): 
$sub = $item['gia'] * $item['soLuong'];
?>

<tr class="cart-row">

<td><input type="checkbox" class="select-item" value="<?php echo $item['maSanPham']; ?>"></td>

<td><img src="assets/img/<?php echo htmlspecialchars($item['hinhAnh']); ?>" width="80"></td>

<td><?php echo htmlspecialchars($item['tenSanPham']); ?></td>

<td class="price"><?php echo number_format($item['gia'],0,',','.'); ?> VND</td>

<td>
<input type="number"
       class="qty-input"
       data-id="<?php echo $item['maSanPham']; ?>"
       value="<?php echo $item['soLuong']; ?>"
       min="1"
       style="width:70px;">
</td>

<td class="sub-total"><?php echo number_format($sub,0,',','.'); ?> VND</td>

<td>
<a class="btn btn-delete" href="#" onclick="return confirmDelete(<?php echo $item['maSanPham']; ?>)">Xóa</a>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<div class="cart-summary">
<strong>Tổng cộng: 0 VND</strong>
</div>

<div class="cart-actions">
<button class="btn btn-checkout" onclick="checkoutSelected()">Thanh toán</button>
</div>

<?php else: ?>
<p>Giỏ hàng trống</p>
<?php endif; ?>

</main>

<!-- ================= FOOTER CHUYÊN NGHIỆP ================= -->
<footer class="footer">
  <div class="container footer-main">
    <div class="footer-col footer-brand">
      <div class="footer-logo">
        <!-- Thay LOGO.png thành logo thực tế của bạn -->
        <img src="assets/img/LOGO.png" alt="Logo" onerror="this.style.display='none'">
        <h3>Shop Đồ Cũ</h3>
      </div>
      <p class="footer-desc">
        Nền tảng mua bán, trao đổi đồ cũ uy tín và tiết kiệm. Hướng tới tiêu dùng bền vững và tiện lợi.
      </p>
      <ul class="footer-contact">
        <li>📍 <strong>Địa chỉ:</strong> Cần Thơ, Việt Nam</li>
        <li>📞 <strong>Hotline:</strong> 1900 xxxx</li>
        <li>✉️ <strong>Email:</strong> hotro@shopdocu.vn</li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Về Chúng Tôi</h4>
      <ul class="footer-links">
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="#">Giới thiệu</a></li>
        <li><a href="#">Sản phẩm mới</a></li>
        <li><a href="#">Liên hệ hỗ trợ</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Chính Sách</h4>
      <ul class="footer-links">
        <li><a href="#">Hướng dẫn mua hàng</a></li>
        <li><a href="#">Chính sách đổi trả</a></li>
        <li><a href="#">Chính sách bảo mật</a></li>
        <li><a href="#">Điều khoản dịch vụ</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Thanh Toán & Kết Nối</h4>
      <div class="payment-tags">
        <span class="pay-badge">💵 Tiền mặt</span>
        <span class="pay-badge">🏦 Chuyển khoản</span>
      </div>
      <div class="social-links" style="margin-top:15px;">
        <a href="#" class="social-btn">Facebook</a>
        <a href="#" class="social-btn">Zalo</a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">

<script>

/* UPDATE QTY */
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('input', function(){

        let id = this.dataset.id;
        let qty = parseInt(this.value);
        if (!qty || qty < 1) qty = 1;

        fetch('cart.php?action=update-ajax', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&qty=' + qty
        });

        let tr = this.closest('.cart-row');
        let price = parseInt(tr.querySelector('.price').innerText.replace(/\D/g,''));
        let sub = price * qty;

        tr.querySelector('.sub-total').innerText = sub.toLocaleString('vi-VN') + ' VND';

        updateTotalSelected();
    });
});

/* TÍNH TỔNG */
document.querySelectorAll('.select-item').forEach(cb => {
    cb.addEventListener('change', updateTotalSelected);
});

function updateTotalSelected() {
    let total = 0;

    document.querySelectorAll('.cart-row').forEach(tr => {
        let cb = tr.querySelector('.select-item');
        if (cb.checked) {
            let sub = tr.querySelector('.sub-total').innerText.replace(/\D/g,'');
            total += parseInt(sub || 0);
        }
    });

    document.querySelector('.cart-summary strong').innerText =
        "Tổng cộng: " + total.toLocaleString('vi-VN') + " VND";
}

/* CHECKOUT */
function checkoutSelected() {

    let selected = [];

    document.querySelectorAll('.select-item:checked').forEach(cb => {
        selected.push(cb.value);
    });

    if (selected.length === 0) {
        alert("Vui lòng chọn sản phẩm!");
        return;
    }

    fetch('cart.php?action=checkout-selected', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'ids=' + JSON.stringify(selected)
    })
    .then(() => window.location.href = 'checkout.php');
}

/* DELETE */
/* DELETE */
function confirmDelete(id) {
    // Kiểm tra xem thư viện Swal đã tải được chưa
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Xóa sản phẩm khỏi giỏ hàng?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((r) => {
            if (r.isConfirmed) {
                window.location.href = 'cart.php?action=remove&id=' + id;
            }
        });
    } else {
        // Nếu không có mạng hoặc lỗi thư viện, dùng hộp thoại mặc định của trình duyệt
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            window.location.href = 'cart.php?action=remove&id=' + id;
        }
    }
    return false;
}
</script>

</body>
</html>