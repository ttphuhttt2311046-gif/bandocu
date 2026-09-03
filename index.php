<?php
session_start();
include "db.php";

/* ĐẾM LƯỢT TRUY CẬP */
$sessionTimeout = 1200;
if (!isset($_SESSION['last_visit']) || time() - $_SESSION['last_visit'] > $sessionTimeout) {
    $conn->query("UPDATE counter SET total = total + 1 WHERE id = 1");
}
$_SESSION['last_visit'] = time();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Đồ Cũ - Trang chủ</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/stylebannersau.css">
    <link rel="stylesheet" href="assets/css/stylehoadao.css">

</head>

<header class="topbar">
  <div class="container">

    <div class="logo-title">
      <img src="assets/img/LOGO.png" alt="Logo" class="logo">
      <h1><a href="index.php">Shop Đồ Cũ</a></h1>
    </div>

    <!-- 🔍 SEARCH + 🎤 VOICE -->
    <div class="search-bar">
      <form action="timkiem.php" method="GET" id="searchForm">
        <input type="text"
               id="query"
               name="query"
               placeholder="Tìm sản phẩm..."
               required>
        <button type="button" class="btn-mic" onclick="startVoice()">🎤</button>
       <button type="submit" class="btn-find" style="display:inline-flex; align-items:center; justify-content:center; padding:9px 8px; border:none; background:#1da1f2;">
            <img src="assets/img/bttimkim.jpg" alt="Tìm" style="height:30px; width:30; display:block;">
      </form>
    </div>

    <div class="nav">
        <?php if (isset($_SESSION['vaitro']) && $_SESSION['vaitro'] === 'buyer'): ?>
        <a href="cart.php" id="cart-icon">
            🛒 Giỏ hàng (
          <span class="cart-count">
<?php
$count = 0;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT SUM(soLuong) as total 
        FROM giohang 
        WHERE maNguoiDung = ?
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    $count = $res['total'] ?? 0;
}

echo $count;
?>
</span>
            )
        </a>

    <?php endif; ?>

</a>


      <?php if (isset($_SESSION['user_id'])): ?>

        <?php if ($_SESSION['vaitro'] === 'admin'): ?>
          <a href="admin/dashboard.php">Quản lý</a>
        <?php endif; ?>

        <?php if ($_SESSION['vaitro'] === 'seller'): ?>
          <a href="admin/index.php">Quản lí sản phẩm</a>
        <?php endif; ?>

        <!-- USER DROPDOWN -->
    <div class="user-dropdown">
        <button id="userBtn" class="user-btn">
            Xin chào, <?php echo htmlspecialchars($_SESSION['tenNguoiDung'] ?? ''); ?> <span class="caret">▾</span>
        </button>
        <div id="userMenu" class="user-menu" aria-hidden="true">
            <a href="caidat.php" class="menu-item"><span class="gear">⚙️</span> Cài đặt</a>

            <a href="admin/logout.php" class="menu-item">Đăng xuất</a>
        </div>
    </div>

      <?php else: ?>
        <a href="admin/login.php">Đăng nhập</a>
        <a href="admin/register.php">Đăng ký</a>
      <?php endif; ?>
    </div>

  </div>
</header>

<!-- 🔻 BANNER -->
<div class="banner-img1">
  <div class="banner-slider">
    <?php
    $banners = $conn->query("SELECT * FROM banner WHERE trangthai=1 ORDER BY thu_tu ASC");
    while ($b = $banners->fetch_assoc()):
    ?>
      <a href="<?= htmlspecialchars($b['link']) ?>" target="_blank">
        <img src="<?= $b['hinh'] ?>" alt="Banner">
      </a>
    <?php endwhile; ?>
  </div>
</div>
<!-- ==============================================
     AI: GỢI Ý SẢN PHẨM DÀNH RIÊNG CHO BẠN
============================================== -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php
        // Gọi file AI
        include_once "ai_recommend.php";
        
        // Lấy 8 sản phẩm gợi ý tốt nhất cho User này
        $ai_limit = 8; 
        $ai_suggests = getAIRecommendations($conn, $_SESSION['user_id'], $ai_limit);
    ?>
    
    <?php if (!empty($ai_suggests)): ?>
        <!-- BỌC TRONG CONTAINER ĐỂ THẲNG HÀNG VỚI WEB -->
        <div class="container">
            <div class="ai-recommendations" style="margin: 20px 0 30px 0;">
                
                <h2 style="color: #e53935; font-size: 22px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; font-weight: bold;">
                    ✨ Gợi ý dành riêng cho bạn
                </h2>
                
                <!-- SỬ DỤNG CLASS CỦA PHÂN TRANG -->
                <div class="grid"> 
                    <?php foreach ($ai_suggests as $sp): ?>
                        <?php
                            $img = 'assets/img/' . (!empty($sp['hinhAnh']) ? $sp['hinhAnh'] : 'placeholder.png');
                            $isHetHang = (isset($sp['soLuong']) && $sp['soLuong'] <= 0);
                            $cardClass = $isHetHang ? 'card het-hang' : 'card';
                            
                            $giaGoc = $sp['gia'] ?? 0;
                            $giam = isset($sp['giamGia']) ? intval($sp['giamGia']) : 0;
                            $giaMoi = $giaGoc * (100 - $giam) / 100;
                            
                            $saoTB = isset($sp['saoTB']) ? round($sp['saoTB'], 1) : 5.0; 
                        ?>
                        
                        <div class="<?= $cardClass ?>">
                            <a class="card-link" href="product.php?id=<?= $sp['maSanPham'] ?>">
                                <div class="img-wrap">
                                    <?php if ($giam > 0): ?>
                                        <div class="sale-badge">-<?= $giam ?>%</div>
                                    <?php endif; ?>
                                    
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($sp['tenSanPham']) ?>">
                                    
                                    <?php if ($isHetHang): ?>
                                        <div class="overlay-out">HẾT HÀNG</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="title"><?= htmlspecialchars($sp['tenSanPham']) ?></div>
                            </a>
                            
                            <div class="price">
                                <?php if ($giam > 0): ?>
                                    <span class="old-price"><?= number_format($giaGoc, 0, ',', '.') ?> VND</span>
                                    <span class="new-price"><?= number_format($giaMoi, 0, ',', '.') ?> VND</span>
                                <?php else: ?>
                                    <?= number_format($giaGoc, 0, ',', '.') ?> VND
                                <?php endif; ?>
                            </div>
                            
                            <div class="rating">⭐ <?= $saoTB ?></div>
                            
                            <p class="desc"><?= mb_strimwidth($sp['moTa'] ?? '', 0, 80, '...') ?></p>
                            
                            <div class="card-actions">
                                <?php if ($isHetHang): ?>
                                    <span class="btn-disabled">Hết hàng</span>
                                <?php else: ?>
                                    <button type="button" class="btn-add-cart" onclick="addToCart(<?= $sp['maSanPham'] ?>)">🛒</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div> <!-- Kết thúc thẻ .grid -->
                
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<!-- ================= KẾT THÚC AI ================= -->

<main class="container">

<!-- 🔹 LỌC DANH MỤC + GIÁ -->
<div class="category-bar">
    <div class="filter-bar">
        <!-- DANH MỤC -->
        <select id="cat-filter">
            <option value="">Tất cả danh mục</option>
            <?php
            $cats = $conn->query("SELECT maDanhMuc, tenDanhMuc FROM danhmuc WHERE trangThai=0 ORDER BY thu_tu ASC");
            while($c = $cats->fetch_assoc()){
                echo "<option value='{$c['maDanhMuc']}'>{$c['tenDanhMuc']}</option>";
            }
            ?>
        </select>

        <!-- GIÁ -->
        <select id="price-filter">
            <option value="">Tất cả giá</option>
            <option value="1">Dưới 50.000</option>
            <option value="2">50.000 - 100.000</option>
            <option value="3">100.000 - 200.000</option>
            <option value="4">Trên 200.000</option>
        </select>

        <!-- SAO -->
        <select id="star-filter">
            <option value="">Tất cả sao</option>
            <option value="5">5⭐</option>
            <option value="4">4⭐ trở lên</option>
            <option value="3">3⭐ trở lên</option>
            <option value="2">2⭐ trở lên</option>
        </select>
    </div>
</div>

<!-- ===== SCROLL BUTTONS ===== -->
<div class="scroll-buttons">
    <button id="btn-scroll-top" title="Lên đầu trang">⬆</button>
    <button id="btn-scroll-bottom" title="Xuống cuối trang">⬇</button>
</div>

<div id="product-list">
    <?php include "phantrang.php"; ?>
</div>

</main>
<!-- ================= FOOTER CHUYÊN NGHIỆP ================= -->
<footer class="footer">
  <div class="container footer-main">
    
    <!-- Cột 1: Giới thiệu & Liên hệ -->
    <div class="footer-col footer-brand">
      <div class="footer-logo">
        <img src="assets/img/LOGO.png" alt="Shop Đồ Cũ Logo">
        <h3>Shop Đồ Cũ</h3>
      </div>
      <p class="footer-desc">
        Nền tảng mua bán, trao đổi đồ cũ uy tín và tiết kiệm. Cùng nhau tái sử dụng đồ dùng, giảm thiểu rác thải và hướng tới tiêu dùng bền vững.
      </p>
      <ul class="footer-contact">
        <li>📍 <strong>Địa chỉ:</strong> Cần Thơ, Việt Nam</li>
        <li>📞 <strong>Hotline:</strong> 1900 xxxx (8:00 - 21:00)</li>
        <li>✉️ <strong>Email:</strong> hotro@shopdocu.vn</li>
      </ul>
    </div>

    <!-- Cột 2: Liên kết nhanh -->
    <div class="footer-col">
      <h4>Về Chúng Tôi</h4>
      <ul class="footer-links">
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="#">Giới thiệu</a></li>
        <li><a href="#">Sản phẩm mới</a></li>
        <li><a href="#"f>Bảng tin & Mẹo mua sắm</a></li>
        <li><a href="#">Liên hệ hỗ trợ</a></li>
      </ul>
    </div>

    <!-- Cột 3: Hỗ trợ khách hàng -->
    <div class="footer-col">
      <h4>Chính Sách & Hỗ Trợ</h4>
      <ul class="footer-links">
        <li><a href="#">Hướng dẫn mua hàng</a></li>
        <li><a href="#">Hướng dẫn đăng tin bán</a></li>
        <li><a href="#">Chính sách đổi trả & Hoàn tiền</a></li>
        <li><a href="#">Chính sách bảo mật</a></li>
        <li><a href="#">Quy định & Điều khoản</a></li>
      </ul>
    </div>

    <!-- Cột 4: Thanh toán & Mạng xã hội -->
    <div class="footer-col">
      <h4>Thanh Toán & Kết Nối</h4>
      <p class="sub-title">Chấp nhận thanh toán:</p>
      <div class="payment-tags">
        <span class="pay-badge">💵 Tiền mặt</span>
        <span class="pay-badge">🏦 Chuyển khoản</span>
        <span class="pay-badge">💳 Ví điện tử</span>
      </div>

      <p class="sub-title" style="margin-top: 15px;">Theo dõi chúng tôi:</p>
      <div class="social-links">
        <a href="#" class="social-btn">Facebook</a>
        <a href="#" class="social-btn">Zalo</a>
        <a href="#" class="social-btn">YouTube</a>
      </div>
    </div>

  </div>

  <!-- Băng copyright & Lượt truy cập bên dưới -->
  <div class="footer-bottom">
    <div class="container footer-bottom-flex">
      <p>© <?php echo date("Y"); ?> <strong>Shop Đồ Cũ</strong>. Tất cả quyền được bảo lưu.</p>
      
      <!-- Hiển thị tổng số lượt truy cập từ DB -->
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

<!-- 🎤 VOICE SEARCH (ĐÃ FIX LỖI DẤU . , KÝ TỰ LẠ) -->
<script>
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

        // 🔥 LÀM SẠCH CHUỖI GIỌNG NÓI
        text = text
            .toLowerCase()
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()?"']/g, '')
            .replace(/\s{2,}/g, ' ')
            .trim();

        if (text.length === 0) {
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
</script>
  <?php if (isset($_SESSION['user_id'])): ?>
<div id="chat-toggle" onclick="toggleChat()">💬</div>
<div id="chat-popup">
    <div id="chat-header">
        <span>💬 Trò chuyện</span>
        <button onclick="toggleChat()">✖</button>
    </div>
    <div id="chat-body">
        <select id="chat-user">
            <option value="">-- Chọn người để chat --</option>
                <option value="9999">🤖 AI Hỗ Trợ</option>
            <?php
           $me = $_SESSION['user_id'];
$role = $_SESSION['vaitro'];

if ($role == 'buyer') {
    // buyer chỉ thấy seller
    $sql = "SELECT maTaiKhoan, tenDangNhap, vaitro 
            FROM taikhoan 
            WHERE vaitro = 'seller' 
            AND maTaiKhoan <> $me";

} elseif ($role == 'seller') {
    // seller chỉ thấy buyer
    $sql = "SELECT maTaiKhoan, tenDangNhap, vaitro 
            FROM taikhoan 
            WHERE vaitro = 'buyer' 
            AND maTaiKhoan <> $me";

} else {
    // admin thấy tất
    $sql = "SELECT maTaiKhoan, tenDangNhap, vaitro 
            FROM taikhoan 
            WHERE maTaiKhoan <> $me";
}

$users = $conn->query($sql . " ORDER BY tenDangNhap ASC");
            
            
            
            
            while ($u = $users->fetch_assoc()) {
                echo "<option value='{$u['maTaiKhoan']}'>{$u['tenDangNhap']} ({$u['vaitro']})</option>";
            }
            ?>
        </select>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="chat-text" placeholder="Nhập tin nhắn...">
            <button onclick="sendMsg()">Gửi</button>
        </div>
    </div>
</div>
<script>
let chatBox = document.getElementById("chat-popup");
let currentUser = null;

function toggleChat(){
    chatBox.style.display = chatBox.style.display === "block" ? "none" : "block";
}

document.getElementById("chat-user").addEventListener("change", function(){
    currentUser = this.value;
    if (currentUser) loadMessages();
});

function loadMessages(){
    if (!currentUser) return;

    let cm = document.getElementById("chat-messages");

    // 👉 kiểm tra có đang ở gần cuối không
    let isNearBottom = cm.scrollHeight - cm.scrollTop <= cm.clientHeight + 50;

    fetch("msg_load.php?to=" + currentUser)
    .then(r => r.text())
    .then(html => {
        cm.innerHTML = html;

        // 👉 chỉ auto scroll nếu đang ở gần cuối
        if (isNearBottom) {
            cm.scrollTop = cm.scrollHeight;
        }
    });
}

function sendMsg(){
    if (!currentUser) return alert("Chọn người cần chat!");
    let msg = document.getElementById("chat-text").value.trim();
    if (msg === "") return;

    fetch("msg_send.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: "receiver_id=" + encodeURIComponent(currentUser) + "&message=" + encodeURIComponent(msg)
    })
    .then(r=>r.json())
    .then(j=>{
        if (j.success){
            document.getElementById("chat-text").value="";

            loadMessages();

            setTimeout(() => {
                let cm = document.getElementById("chat-messages");
                cm.scrollTop = cm.scrollHeight;
            }, 100);

        } else alert("Lỗi: "+j.error);
    });
}

setInterval(() => { if (currentUser) loadMessages(); }, 3000);
setInterval(checkNewMessages, 3000);

function checkNewMessages(){
    fetch("check_new_messages.php")
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        
        // Xóa chấm cũ
        document.querySelectorAll("#chat-user option").forEach(o => {
            o.textContent = o.textContent.replace(" 🔴", "");
        });

        // Đánh dấu người gửi có tin mới
        data.unread.forEach(uid => {
            let opt = document.querySelector(`#chat-user option[value='${uid}']`);
            if (opt) opt.textContent = opt.textContent.replace(" 🔴", "") + " 🔴";
        });
        // 🧩 Đưa người có tin nhắn mới lên đầu danh sách
        const select = document.getElementById("chat-user");
        data.unread.slice().reverse().forEach(uid => {
        const opt = select.querySelector(`option[value='${uid}']`);
        if (opt) {
        // Di chuyển option này lên ngay sau option đầu tiên (mục "-- Chọn người để chat --")
        select.insertBefore(opt, select.options[1]);
    }
});
        // Hiện chấm đỏ ở biểu tượng 💬 nếu có tin mới
        const dotId = "chat-dot";
        let dot = document.getElementById(dotId);
        if (data.unread.length > 0 && chatBox.style.display !== "block") {
            if (!dot) {
                dot = document.createElement("div");
                dot.id = dotId;
                dot.style.position = "absolute";
                dot.style.top = "6px";
                dot.style.right = "6px";
                dot.style.width = "10px";
                dot.style.height = "10px";
                dot.style.background = "red";
                dot.style.borderRadius = "50%";
                dot.style.border = "2px solid white";
                document.getElementById("chat-toggle").appendChild(dot);
            }
        } else if (dot) {
            dot.remove();
        }
    });
}
</script>
<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const btnTop = document.getElementById("btn-scroll-top");
  const btnBottom = document.getElementById("btn-scroll-bottom");

  if (!btnTop || !btnBottom) return;

  btnTop.onclick = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  btnBottom.onclick = () => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
  };

  window.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;
    const maxScroll = document.body.scrollHeight - window.innerHeight;

    btnTop.style.display = scrollTop > 300 ? "flex" : "none";
    btnBottom.style.display = scrollTop < maxScroll - 300 ? "flex" : "none";
  });
});

</script>
<!-- Script hoa đào -->
<div class="sakura-falling"></div>

<script>
function createSakura() {
    const sakura = document.createElement("div");
    sakura.classList.add("sakura");

    // Vị trí rơi
    sakura.style.left = Math.random() * window.innerWidth + "px";

    // Kích thước NGẪU NHIÊN lớn hơn
    const size = 30 + Math.random() * 20;  // 30–50px
    sakura.style.width = size + "px";
    sakura.style.height = size + "px";

    // Rơi chậm hơn
    sakura.style.animationDuration = 12 + Math.random() * 12 + "s";  
    // từ 12s → 24s

    document.querySelector(".sakura-falling").appendChild(sakura);

    // 🌸 GIỮ BÔNG LÂU (2 phút rưỡi rồi mới xoá)
    setTimeout(() => sakura.remove(), 160000);
}

// 🌸 THƯA HƠN: mỗi 1000ms (1 giây) mới tạo 1 bông
setInterval(createSakura, 1000);
</script>
<script>
function addToCart(id) {
    fetch("cart.php?action=add&id=" + id)
    .then(response => response.text())
    .then(data => {

        updateCartCount();
        showToast("Đã thêm vào giỏ hàng!");

        // Hiệu ứng rung icon giỏ
        const cartIcon = document.getElementById("cart-icon");
        cartIcon.classList.add("shake");
        setTimeout(() => cartIcon.classList.remove("shake"), 500);
    })
    .catch(() => {
        showToast("Có lỗi xảy ra!");
    });
}

function updateCartCount() {
    fetch("cart_count.php")
    .then(res => res.text())
    .then(count => {
        document.querySelector(".cart-count").innerText = count;
    });
}

function showToast(message) {
    let toast = document.createElement("div");
    toast.className = "toast";
    toast.innerText = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 10);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
</script>
<script>

function loadProducts(page = 1){

    let cat = document.getElementById("cat-filter").value;
    let price = document.getElementById("price-filter").value;
    let star = document.getElementById("star-filter").value;

    let url = "phantrang.php?page=" + page;

    if(cat !== ""){
        url += "&cat=" + cat;
    }

    if(price !== ""){
        url += "&price=" + price;
    }

    if(star !== ""){
        url += "&star=" + star;
    }

    fetch(url)
    .then(res => res.text())
    .then(data => {
        document.getElementById("product-list").innerHTML = data;
    });

}

/* lọc danh mục */
document.getElementById("cat-filter").addEventListener("change", function(){
    loadProducts(1);
});

/* lọc giá */
document.getElementById("price-filter").addEventListener("change", function(){
    loadProducts(1);
});
document.getElementById("star-filter").addEventListener("change", function(){
    loadProducts(1);
});
document.addEventListener('click', function(e){
  const btn = document.getElementById('userBtn');
  const menu = document.getElementById('userMenu');
  if (!btn || !menu) return;

  if (btn.contains(e.target)) {
    menu.classList.toggle('show');
    menu.setAttribute('aria-hidden', menu.classList.contains('show') ? 'false' : 'true');
  } else {
    menu.classList.remove('show');
    menu.setAttribute('aria-hidden','true');
  }
});

// optional: close on ESC
document.addEventListener('keydown', function(e){
  if (e.key === 'Escape') {
    const menu = document.getElementById('userMenu');
    if (menu) { menu.classList.remove('show'); menu.setAttribute('aria-hidden','true'); }
  }
});
</script>
</body>
</html>
