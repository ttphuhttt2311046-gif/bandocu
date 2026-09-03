<?php
session_start();
include "db.php";

// Kiểm tra xem file validation có tồn tại không trước khi require
if (file_exists('admin/validation.php')) {
    require_once 'admin/validation.php';
}

/* HÀM CHUẨN HÓA DỮ LIỆU ĐẦU VÀO */
if (!function_exists('clean')) {
    function clean($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validLength')) {
    function validLength($str, $min, $max) {
        $len = mb_strlen($str, 'UTF-8');
        return $len >= $min && $len <= $max;
    }
}

/* HÀM BỎ DẤU TIẾNG VIỆT */
function removeVietnameseAccents($str) {
    $accents = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'đ'=>'d','Đ'=>'D'
    ];
    return strtr($str, $accents);
}

/* LẤY & CHUẨN HÓA TỪ KHÓA */
$queryRaw = clean($_GET['query'] ?? '');

if ($queryRaw === '') {
    header("Location: index.php");
    exit;
}

if (!validLength($queryRaw, 1, 1000)) {
    exit("Từ khóa tìm kiếm không hợp lệ");
}

$likeRaw = "%$queryRaw%";
$queryNoAccent = strtolower(removeVietnameseAccents($queryRaw));
$likeNoAccent = "%$queryNoAccent%";

/* SQL: TÌM KIẾM SẢN PHẨM */
$sql = "
SELECT * FROM sanpham
WHERE trangThai = 1
AND (
    tenSanPham LIKE ?
    OR moTa LIKE ?
    OR LOWER(tenSanPham) LIKE ?
)
ORDER BY maSanPham DESC
";

$result = false;
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("sss", $likeRaw, $likeRaw, $likeNoAccent);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Trường hợp câu SQL lỗi, log lỗi hoặc fallback
    error_log("SQL Error (timkiem.php): " . $conn->error);
}

/* TÍNH GIẢM GIÁ SỰ KIỆN TỔNG THỂ (NẾU CÓ) */
$giamSuKienChung = 0;
$now = date('Y-m-d H:i:s');
$eventSql = "SELECT phanTramGiam FROM sukien_giamgia WHERE trangThai = 1 AND batDau <= ? AND ketThuc >= ? LIMIT 1";
$eventStmt = $conn->prepare($eventSql);

if ($eventStmt) {
    $eventStmt->bind_param("ss", $now, $now);
    $eventStmt->execute();
    $eventRes = $eventStmt->get_result();
    if ($eventRes && $rowEv = $eventRes->fetch_assoc()) {
        $giamSuKienChung = intval($rowEv['phanTramGiam']);
    }
    $eventStmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kết quả tìm kiếm - <?php echo htmlspecialchars($queryRaw); ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/stylebannersau.css">
  
  <style>
    /* CSS Bổ sung cho Footer chuyên nghiệp */
    .footer { background-color: #1e293b; color: #cbd5e1; font-size: 14px; line-height: 1.6; margin-top: 50px; border-top: 4px solid #1da1f2; }
    .footer-main { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px; padding: 40px 15px 30px 15px; }
    .footer-col h4 { color: #ffffff; font-size: 16px; font-weight: 700; margin-bottom: 18px; position: relative; padding-bottom: 8px; text-transform: uppercase; }
    .footer-col h4::after { content: ''; position: absolute; left: 0; bottom: 0; width: 35px; height: 2px; background-color: #1da1f2; }
    .footer-brand .footer-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .footer-brand .footer-logo img { height: 38px; width: auto; }
    .footer-brand .footer-logo h3 { color: #ffffff; font-size: 20px; margin: 0; }
    .footer-desc { color: #94a3b8; margin-bottom: 15px; font-size: 13px; }
    .footer-contact, .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-contact li, .footer-links li { margin-bottom: 10px; }
    .footer-links a { color: #cbd5e1; text-decoration: none; transition: all 0.2s; display: inline-block; }
    .footer-links a:hover { color: #38bdf8; transform: translateX(5px); }
    .payment-tags, .social-links { display: flex; flex-wrap: wrap; gap: 6px; }
    .pay-badge, .social-btn { background: #334155; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 12px; text-decoration: none; }
    .social-btn:hover { background: #1da1f2; }
    .footer-bottom { background-color: #0f172a; padding: 15px 0; border-top: 1px solid #334155; font-size: 13px; }
    .footer-bottom-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .visit-count-badge { background: #1e293b; border: 1px solid #334155; padding: 4px 12px; border-radius: 20px; color: #94a3b8; }
    .visit-count-badge span { color: #38bdf8; font-weight: bold; }
  </style>
</head>
<body>

<header class="topbar">
  <div class="container">
    <h1 style="margin-right:25px; white-space:nowrap; font-size:28px; display:flex; align-items:center;">
      <a href="index.php" style="text-decoration:none; color:white;">Shop Đồ Cũ</a>
    </h1>

    <!-- 🔍 SEARCH + 🎤 VOICE -->
    <div class="search-bar">
      <form action="timkiem.php" method="GET" id="searchForm">
        <input type="text"
               id="query"
               name="query"
               value="<?php echo htmlspecialchars($queryRaw); ?>"
               placeholder="Tìm sản phẩm..."
               required>
        <button type="button" class="btn-mic" onclick="startVoice()">🎤</button>
        <button type="submit" class="btn-find" style="display:inline-flex; align-items:center; justify-content:center; padding:9px 8px; border:none; background:#1da1f2;">
            <img src="assets/img/bttimkim.jpg" alt="Tìm" style="height:30px; width:auto; display:block;">
        </button>
      </form>
    </div>

    <div class="nav">
      <a href="cart.php">
        🛒 Giỏ hàng (<?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0; ?>)
      </a>

      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="admin/ttnguoidung.php" style="color:white; font-size:16px;">
          Xin chào, <?php echo htmlspecialchars($_SESSION['tenNguoiDung'] ?? ''); ?>
        </a>
        <a href="admin/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="admin/login.php">Đăng nhập</a>
        <a href="admin/register.php">Đăng ký</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container">
  <h2 style="color: white; margin-top: 20px;">
    Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($queryRaw); ?>"
  </h2>

  <!-- Bộ lọc bổ sung -->
  <div style="margin:15px 0; display:flex; gap:10px; flex-wrap:wrap;">
    <select name="price" id="filter-price">
      <option value="">Tất cả giá</option>
      <option value="duoi50">Dưới 50.000</option>
      <option value="50-100">50.000 - 100.000</option>
      <option value="100-200">100.000 - 200.000</option>
      <option value="tren200">Trên 200.000</option>
    </select>

    <select name="star" id="filter-star">
      <option value="">Tất cả đánh giá</option>
      <option value="5">5⭐</option>
      <option value="4">4⭐ trở lên</option>
      <option value="3">3⭐ trở lên</option>
    </select>
  </div>

  <div id="product-list" class="grid">
  <?php
  // Render dữ liệu ban đầu làm dự phòng (AJAX sẽ tải đè lên khi có mạng/JS)
  if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $img = 'assets/img/' . (!empty($row['hinhAnh']) ? $row['hinhAnh'] : 'placeholder.png');
          
          // TÍNH GIẢM GIÁ
          $giaGoc = $row['gia'];
          $giamCaNhan = intval($row['giamGia'] ?? 0);
          $giamSuKien = (isset($row['tuChoiSuKien']) && $row['tuChoiSuKien'] == 0) ? $giamSuKienChung : 0;
          
          $giamApDung = max($giamCaNhan, $giamSuKien);
          $giaMoi = $giaGoc * (100 - $giamApDung) / 100;
          ?>
          <div class="card" onclick="window.location.href='product.php?id=<?= $row['maSanPham'] ?>'">
              <div class="thumb">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['tenSanPham']) ?>">
              </div>
              <div class="meta">
                  <div class="title"><?= htmlspecialchars($row['tenSanPham']) ?></div>
                  
                  <?php if ($giamApDung > 0): ?>
                      <div class="price">
                          <span style="text-decoration:line-through; color:#999; font-size: 13px;"><?= number_format($giaGoc,0,',','.') ?> VND</span><br>
                          <span style="color:red; font-weight:bold;"><?= number_format($giaMoi,0,',','.') ?> VND</span>
                      </div>
                  <?php else: ?>
                      <div class="price"><?= number_format($giaGoc,0,',','.') ?> VND</div>
                  <?php endif; ?>
              </div>

              <p class="desc"><?= htmlspecialchars(mb_strimwidth($row['moTa'] ?? '', 0, 80, '...')) ?></p>
              
              <div class="card-actions">
                  <a class="btn btn-outline" href="cart.php?action=add&id=<?= $row['maSanPham'] ?>" style="display:inline-flex; align-items:center; justify-content:center; padding:6px 8px;">
                      <img src="assets/img/addcart.png" alt="Thêm vào giỏ" style="height:20px; width:auto; display:block;">
                  </a>
              </div>
          </div>
          <?php
      }
  } else {
      echo '<p style="color:white; width: 100%;">Không tìm thấy sản phẩm phù hợp.</p>';
  }
  ?>
  </div>
</main>

<!-- ================= FOOTER CHUYÊN NGHIỆP ================= -->
<footer class="footer">
  <div class="container footer-main">
    <div class="footer-col footer-brand">
      <div class="footer-logo">
        <img src="assets/img/LOGO.png" alt="Logo">
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
    <div class="container footer-bottom-flex">
      <p>© <?php echo date("Y"); ?> <strong>Shop Đồ Cũ</strong>. Tất cả quyền được bảo lưu.</p>
      <?php
        $res_counter = $conn->query("SELECT total FROM counter WHERE id = 1");
        $total_visit = ($res_counter && $row_c = $res_counter->fetch_assoc()) ? $row_c['total'] : 0;
      ?>
      <div class="visit-count-badge">
        👁️ Tổng lượt truy cập: <span><?= number_format($total_visit, 0, ',', '.') ?></span>
      </div>
    </div>
  </div>
</footer>

<!-- SCRIPT ============================================ -->
<script>
// 🎤 VOICE SEARCH
function startVoice() {
    if (!('webkitSpeechRecognition' in window)) {
        alert("Trình duyệt không hỗ trợ tìm kiếm bằng giọng nói");
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.lang = "vi-VN";
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onresult = function(event) {
        let text = event.results[0][0].transcript;

        text = text
            .toLowerCase()
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()?"']/g, '')
            .replace(/\s{2,}/g, ' ')
            .trim();

        if (!text) {
            alert("Không nhận diện được từ khóa");
            return;
        }

        document.getElementById("query").value = text;
        document.getElementById("searchForm").submit();
    };

    recognition.onerror = function () {
        alert("Không nhận được giọng nói");
    };

    recognition.start();
}

// 🔍 TẢI DỮ LIỆU QUA AJAX KHI LỌC GIÁ HOẶC SAO
function loadProducts(page = 1) {
    let query = document.getElementById("query").value;
    let price = document.getElementById("filter-price").value;
    let star = document.getElementById("filter-star").value;

    fetch(`timkiem_ajax.php?query=${encodeURIComponent(query)}&price=${price}&star=${star}&page=${page}`)
    .then(res => res.text())
    .then(data => {
        // Cập nhật ngay giao diện kết quả từ timkiem_ajax.php
        document.getElementById("product-list").innerHTML = data;
    })
    .catch(err => console.error("Lỗi AJAX lọc sản phẩm:", err));
}
// Gọi AJAX lần đầu ngay khi mở trang để đồng bộ HTML của `timkiem_ajax.php`
window.addEventListener('DOMContentLoaded', (event) => {
    loadProducts(1);
});

// Bắt sự kiện khi người dùng thay đổi lựa chọn trong thẻ select
document.getElementById("filter-price").addEventListener("change", () => loadProducts(1));
document.getElementById("filter-star").addEventListener("change", () => loadProducts(1));
</script>

</body>
</html>