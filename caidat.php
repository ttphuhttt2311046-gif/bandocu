<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) { header('Location: admin/login.php'); exit; }
$uid = $_SESSION['user_id'];
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Cài đặt - Shop Đồ Cũ</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/caidat.css">
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
  <main>
  <nav class="settings-list" role="navigation" aria-label="Cài đặt tài khoản">
    <a class="setting-item" href="caidat/ttnguoidung.php">
      <div>
        <span class="label">Tài khoản & Bảo mật</span>
      </div>
      <span class="chev">›</span>
    </a>
    <a class="setting-item" href="caidat/diachi.php" data-key="diachi">
      <div>
        <span class="label">Địa Chỉ</span>
      </div>
      <span class="chev">›</span>
    </a>

    <div class="section-title">Hỗ trợ</div>
    <a class="setting-item" href="caidat/trungtamhotro.php" data-key="hotro">
      <div>
        <span class="label">Trung tâm hỗ trợ</span>
      </div>
      <span class="chev">›</span>
    </a>
    <a class="setting-item" href="caidat/tieuchuancd.php" data-key="tieuchuan">
      <div>
        <span class="label">Tiêu chuẩn cộng đồng</span>
      </div>
      <span class="chev">›</span>
    </a>
    <a class="setting-item" href="caidat/dieukhoan.php" data-key="dieukhoan">
      <div>
        <span class="label">Điều khoản</span>
      </div>
      <span class="chev">›</span>
    </a>
    <a class="setting-item" href="caidat/gioithieu.php" data-key="dieukhoan">
      <div>
        <span class="label">Giới thiệu</span>
      </div>
      <span class="chev">›</span>
    </a>

    <a class="setting-item" href="caidat/ycxoataikhoan.php" data-key="xoa_tk">
      <div>
        <span class="label">Yêu cầu xóa tài khoản</span>
      </div>
      <span class="chev">›</span>
    </a>
  </nav>
</main>
</body>
</html>