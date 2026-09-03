<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user_id'])) { header('Location: ../admin/login.php'); exit; }
$uid = $_SESSION['user_id'];
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tiêu chuẩn cộng đồng - Shop Đồ Cũ</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/caidat.css">
  <style>
    .content{max-width:760px;margin:12px auto;padding:0 12px 60px;}
    .doc-card{background:#fff;border-radius:8px;padding:16px;margin-top:12px;border:1px solid #f0f0f0}
    .doc-card h2{margin:0 0 8px;font-size:18px}
    .doc-card p{margin:8px 0;color:#333;line-height:1.6}
    .doc-list{margin:8px 0 0;padding-left:18px}
    .doc-list li{margin:8px 0}
    .note{font-size:13px;color:#666;margin-top:12px}
  </style>
</head>
<body>
    <header class="topbar">
    <div class="container">
      <div class="logo-title">
  <h1><a href="../index.php">Shop Đồ Cũ</a></h1>
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
  <div class="container content">
    <div class="doc-card" role="article" aria-labelledby="tieu-de">
      <h2 id="tieu-de">Tiêu chuẩn cộng đồng</h2>

      <p><strong>Mục đích và phạm vi:</strong></p>
      <p>Nền tảng đặt ra các nguyên tắc ứng xử chung để duy trì môi trường mua bán, trao đổi đồ cũ an toàn, tôn trọng và hợp pháp cho tất cả người dùng.</p>

      <p><strong>Quy định về sản phẩm đăng bán:</strong></p>
      <ul class="doc-list">
        <li>Cấm tuyệt đối các mặt hàng thuộc danh mục pháp luật Việt Nam cấm: vũ khí, chất ma tuý, hàng giả, động vật hoang dã.</li>
        <li>Cấm đăng bán sản phẩm không rõ nguồn gốc hoặc vi phạm quyền sở hữu trí tuệ.</li>
        <li>Người bán phải mô tả trung thực tình trạng, nguồn gốc và thông tin liên quan của sản phẩm.</li>
      </ul>

      <p><strong>Hành vi bị nghiêm cấm:</strong></p>
      <ul class="doc-list">
        <li>Đăng tin sai sự thật, lợi dụng, lừa đảo chiếm đoạt tài sản của người mua hoặc người bán.</li>
        <li>Sử dụng ngôn ngữ thô tục, lăng mạ, quấy rối hoặc có hành vi phân biệt đối xử trong bình luận/đánh giá.</li>
        <li>Thu thập, phát tán thông tin cá nhân của người khác trái phép hoặc xâm phạm quyền riêng tư.</li>
        <li>Đăng nội dung gây kích động bạo lực, kích động vi phạm pháp luật hoặc vi phạm chuẩn mực xã hội.</li>
      </ul>

      <p><strong>Quyền hạn của quản trị viên:</strong></p>
      <ul class="doc-list">
        <li>Quản trị viên có quyền ẩn, gỡ bỏ bài đăng vi phạm mà không cần báo trước khi phát hiện các hành vi trái quy định.</li>
        <li>Hệ thống có quyền khóa tạm thời hoặc khóa vĩnh viễn tài khoản khi phát hiện vi phạm nghiêm trọng hoặc theo yêu cầu của cơ quan chức năng.</li>
        <li>Trong trường hợp cần thiết, quản trị viên có thể cung cấp thông tin cho cơ quan chức năng theo quy định pháp luật.</li>
      </ul>
    </div>
  </div>
</body>
</html>