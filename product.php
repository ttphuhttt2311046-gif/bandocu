
<?php
session_start();
include "db.php";
include "goiy/goiysp.php";

/*LẤY ID */
$id = $_GET['id'] ?? 0;

/* TĂNG LƯỢT XEM */
if($id > 0){
    // Cố gắng tăng lượt xem - nếu cột không tồn tại, bỏ qua lỗi
    $updateView = $conn->prepare("
        UPDATE sanpham
        SET luotXem = COALESCE(luotXem, 0) + 1
        WHERE maSanPham = ?
    ");

    if($updateView){
        $updateView->bind_param("i", $id);
        $updateView->execute();
        $updateView->close();
    }
}

/*ĐÁNH GIÁ*/
$sql = "
  SELECT COUNT(*) AS tongDanhGia, AVG(soSao) AS saoTB
  FROM danhgia WHERE maSanPham=?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$tongDanhGia = $r['tongDanhGia'] ?? 0;
$saoTrungBinh = $r['saoTB'] ? number_format($r['saoTB'],1) : 0;
$stmt->close();

/* ĐÃ BÁN*/
$sql = "
 SELECT COALESCE(SUM(ct.soLuong),0) daBan
 FROM chitietdonhang ct
 JOIN donhang dh ON ct.maDonHang=dh.maDonHang
 WHERE ct.maSanPham=? AND dh.trangThai IS NOT NULL
";
$s = $conn->prepare($sql);
$s->bind_param("i",$id);
$s->execute();
$daBan = $s->get_result()->fetch_assoc()['daBan'] ?? 0;
$s->close();

/*SẢN PHẨM */
$stmt = $conn->prepare("SELECT * FROM sanpham WHERE maSanPham=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();


/* ====================== YÊU THÍCH ====================== */

$daYeuThich = false;
$totalFavorite = 0;

if($product){
  // Tổng số lượt thích của sản phẩm
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM yeuthich WHERE maSanPham = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $totalFavorite = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
  $stmt->close();
}
 if(isset($_SESSION['user_id'])){

   $stmt = $conn->prepare("SELECT maYeuThich FROM yeuthich WHERE maTaiKhoan = ? AND maSanPham = ?");

  $stmt->bind_param("ii", $_SESSION['user_id'], $id);

   $stmt->execute();

    $daYeuThich = $stmt->get_result()->num_rows > 0;

     $stmt->close();

 }


/* NGƯỜI BÁN */
$sellerName = $sellerPhone = '';
if($product && $product['maNguoiBan']){
  $s = $conn->prepare(
  "SELECT tenNguoiDung,tenDangNhap,soDienThoai, avatar
   FROM taikhoan WHERE maTaiKhoan=?"
);
  $s->bind_param("i",$product['maNguoiBan']);
  $s->execute();
  if($u=$s->get_result()->fetch_assoc()){
    $sellerName = $u['tenNguoiDung'] ?: $u['tenDangNhap'];
    $sellerPhone = $u['soDienThoai'] ?? '';
    $sellerAvatar = $u['avatar'] ?? null;
  }
  $s->close();
}

/*ĐÃ MUA? */
$daMua=false;
if(isset($_SESSION['user_id'])){
  $uid=$_SESSION['user_id'];
  $c=$conn->prepare("
    SELECT 1 FROM donhang dh
    JOIN chitietdonhang ct ON dh.maDonHang=ct.maDonHang
    WHERE dh.maNguoiMua=? AND ct.maSanPham=?
      AND dh.trangThai IS NOT NULL LIMIT 1
  ");
  $c->bind_param("ii",$uid,$id);
  $c->execute();
  $c->store_result();
  $daMua=$c->num_rows>0;
  $c->close();
}

/*CẬP NHẬT TÌNH TRẠNG*/
if($product){
  $new = $product['soLuong']>0?'Còn hàng':'Hết hàng';
  if($product['tinhTrang']!==$new){
    $u=$conn->prepare("UPDATE sanpham SET tinhTrang=? WHERE maSanPham=?");
    $u->bind_param("si",$new,$id);
    $u->execute();
    $u->close();
    $product['tinhTrang']=$new;
  }
}

/*GỢI Ý */
$resultSuggest = [];
if($product){
  $resultSuggest = getRecommendationForProduct(
    $conn,
    $product,
    $_SESSION['user_id'] ?? null,
    12
  );
}

/* PHẦN ĐÁNH GIÁ */

/* MỖI NGƯỜI 1 REVIEW*/
$daDanhGia = false;
if(isset($_SESSION['user_id'])){
  $check = $conn->prepare("SELECT id FROM danhgia WHERE maSanPham=? AND maNguoiDung=?");
  $check->bind_param("ii",$id,$_SESSION['user_id']);
  $check->execute();
  $check->store_result();
  $daDanhGia = $check->num_rows > 0;
  $check->close();
}

/*GỬI ĐÁNH GIÁ */
if(isset($_POST['guiDanhGia']) && $daMua && !$daDanhGia){
  $soSao = intval($_POST['soSao']);
  $binhLuan = trim($_POST['binhLuan']);
  $uid = $_SESSION['user_id'];

  $stmt = $conn->prepare("
    INSERT INTO danhgia(maSanPham,maNguoiDung,soSao,binhLuan,ngayDanhGia)
    VALUES(?,?,?,?,NOW())
  ");
  $stmt->bind_param("iiis",$id,$uid,$soSao,$binhLuan);
  $stmt->execute();
  $stmt->close();

  header("Location: product.php?id=".$id."#reviews");
  exit;
}

/*XÓA*/
if(isset($_GET['xoa']) && isset($_SESSION['user_id'])){
  $rid=intval($_GET['xoa']);
  $uid=$_SESSION['user_id'];

  $del=$conn->prepare("DELETE FROM danhgia WHERE id=? AND maNguoiDung=?");
  $del->bind_param("ii",$rid,$uid);
  $del->execute();
  $del->close();

  header("Location: product.php?id=".$id."#reviews");
  exit;
}

/*  SỬA*/
if(isset($_POST['suaDanhGia'])){
  $rid=intval($_POST['review_id']);
  $soSao=intval($_POST['soSao']);
  $binhLuan=trim($_POST['binhLuan']);
  $uid=$_SESSION['user_id'];

  $up=$conn->prepare("
    UPDATE danhgia
    SET soSao=?,binhLuan=?,ngayDanhGia=NOW()
    WHERE id=? AND maNguoiDung=?
  ");
  $up->bind_param("isii",$soSao,$binhLuan,$rid,$uid);
  $up->execute();
  $up->close();

  header("Location: product.php?id=".$id."#reviews");
  exit;
}

/*THỐNG KÊ SAO */
$starStats=[];
$totalStars=0;

$stat=$conn->prepare("SELECT soSao,COUNT(*) total FROM danhgia WHERE maSanPham=? GROUP BY soSao");
$stat->bind_param("i",$id);
$stat->execute();
$res=$stat->get_result();

while($row=$res->fetch_assoc()){
  $starStats[$row['soSao']]=$row['total'];
  $totalStars+=$row['total'];
}
$stat->close();

/* DANH SÁCH REVIEW*/
$listReview=$conn->prepare("
  SELECT dg.*,tk.tenNguoiDung
  FROM danhgia dg
  JOIN taikhoan tk ON dg.maNguoiDung=tk.maTaiKhoan
  WHERE dg.maSanPham=?
  ORDER BY dg.ngayDanhGia DESC
");
$listReview->bind_param("i",$id);
$listReview->execute();
$resultReview=$listReview->get_result();

/*TRẢ LỜI ĐÁNH GIÁ */


if(isset($_POST['traLoiDanhGia'])){

    if($_SESSION['user_id'] != $product['maNguoiBan']){
        die("Bạn không có quyền trả lời đánh giá này");
    }

    $rid = intval($_POST['review_id']);
    $traLoi = trim($_POST['traLoi']);

    $stmt = $conn->prepare("
        UPDATE danhgia
        SET traLoiNguoiBan=?, ngayTraLoi=NOW()
        WHERE id=?
    ");

    $stmt->bind_param("si",$traLoi,$rid);
    $stmt->execute();
    $stmt->close();

    header("Location: product.php?id=".$id."#reviews");
    exit;
}
/*  SỬA TRẢ LỜI  */
if(isset($_POST['suaTraLoi'])){

    if($_SESSION['user_id'] != $product['maNguoiBan']){
        die("Bạn không có quyền sửa trả lời này");
    }

    $rid = intval($_POST['review_id']);
    $traLoi = trim($_POST['traLoi']);

    $stmt = $conn->prepare("
        UPDATE danhgia
        SET traLoiNguoiBan=?, ngayTraLoi=NOW()
        WHERE id=?
    ");

    $stmt->bind_param("si",$traLoi,$rid);
    $stmt->execute();
    $stmt->close();

    header("Location: product.php?id=".$id."#reviews");
    exit;
}
/* TĂNG LƯỢT XEM */
if ($id > 0) {

    // Tăng lượt xem sản phẩm tổng thể
    $updateView = $conn->prepare("
        UPDATE sanpham
        SET luotXem = COALESCE(luotXem, 0) + 1
        WHERE maSanPham = ?
    ");

    if ($updateView) {
        $updateView->bind_param("i", $id);
        $updateView->execute();
        $updateView->close();
    }

    // Nếu người dùng đã đăng nhập thì lưu/cập nhật lịch sử xem phục vụ AI/Machine Learning
    if (isset($_SESSION['user_id'])) {

        $userId = $_SESSION['user_id'];

        // Kiểm tra đã có lịch sử tương tác giữa User này và Sản phẩm này chưa
        $check = $conn->prepare("
            SELECT maInteraction
            FROM user_interactions
            WHERE maTaiKhoan = ?
            AND maSanPham = ?
        ");

        $check->bind_param("ii", $userId, $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Đã tồn tại -> cộng dồn số lần xem (+1)
            $update = $conn->prepare("
                UPDATE user_interactions
                SET soLanXem = soLanXem + 1
                WHERE maTaiKhoan = ?
                AND maSanPham = ?
            ");

            $update->bind_param("ii", $userId, $id);
            $update->execute();
            $update->close();

        } else {
            // Chưa có lịch sử -> Tạo mới dòng tương tác với soLanXem mặc định = 1
            $insert = $conn->prepare("
                INSERT INTO user_interactions
                (maTaiKhoan, maSanPham, soLanXem, yeuThich, lienHe, muaHang)
                VALUES
                (?, ?, 1, 0, 0, 0)
            ");

            $insert->bind_param("ii", $userId, $id);
            $insert->execute();
            $insert->close();
        }

        $check->close();
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Chi tiết sản phẩm</title>
<link rel="stylesheet" href="assets/product.css">
</head>

<body>

<header class="topbar">
  <div class="container topbar-inner">
    <h1><a href="index.php">NYP SHOP</a></h1>
    <nav>
      
    <a href="index.php">Trang chủ</a>
<a href="cart.php">
    🛒 Giỏ hàng (
<span class="cart-count">
<?php echo isset($_SESSION['cart']) 
    ? array_sum(array_column($_SESSION['cart'], 'qty')) 
    : 0; ?>
</span>
)
    
</a>      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Xin chào, <?= htmlspecialchars($_SESSION['tenNguoiDung']) ?></span>
        <a href="admin/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="admin/login.php">Đăng nhập</a>
        <a href="admin/register.php">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
<?php if($product): ?>

<section class="product-box">

<!-- BÊN TRÁI - GALLERY (ĐÃ SỬA - HỖ TRỢ ZOOM ẢNH) -->
<div class="gallery">

    <!-- Ảnh/Video chính -->
    <?php if(!empty($product['video'])): ?>
        <video id="mainVideo"
               class="main-media"
               controls
               muted
               loop
               style="width: 100%; max-height: 520px; border-radius: 8px; background: #000;">
            <source src="assets/video/<?= htmlspecialchars($product['video']) ?>" 
                    type="video/mp4">
        </video>

        <img id="mainImage"
             class="main-media"
             src="assets/img/<?= htmlspecialchars($product['hinhAnh']) ?>"
             style="display:none; width: 100%; max-height: 520px; border-radius: 8px; cursor: zoom-in;"
             onclick="zoomImage(this)">
    <?php else: ?>
        <img id="mainImage"
             class="main-media"
             src="assets/img/<?= htmlspecialchars($product['hinhAnh']) ?>"
             style="width: 100%; max-height: 520px; border-radius: 8px; cursor: zoom-in;"
             onclick="zoomImage(this)">
    <?php endif; ?>

    <!-- Thumbnails -->
    <div class="thumbs1" style="margin-top: 15px; display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">

        <!-- Ảnh chính -->
        <?php if(!empty($product['hinhAnh'])): ?>
            <img class="thumb-img" 
                 src="assets/img/<?= htmlspecialchars($product['hinhAnh']) ?>"
                 onclick="changeMainImage(this)">
        <?php endif; ?>

        <!-- Ảnh phụ -->
        <?php for($i=1; $i<=7; $i++):
            $f = 'hinhAnh'.$i;
            if(!empty($product[$f])): ?>
                <img class="thumb-img" 
                     src="assets/img_phu/<?= htmlspecialchars($product[$f]) ?>"
                     onclick="changeMainImage(this)">
            <?php endif; 
        endfor; ?>

        <!-- Video thumbnail -->
        <?php if(!empty($product['video'])): ?>
            <img class="thumb-video" 
                 src="assets/img/play.png" 
                 alt="Video"
                 onclick="playMainVideo()">
        <?php endif; ?>
    </div>
</div>


<!--PHẢI -->
<div class="info-box">

<h1 style="font-size: 26px; margin: 15px 0 10px 0; line-height: 1.3; color: #333;">
        <?= htmlspecialchars($product['tenSanPham'] ?? 'Không có tên sản phẩm') ?>
</h1>

<div class="price">
<?= number_format($product['gia'],0,',','.') ?> ₫
</div>

<!-- ĐOẠN CODE NÚT YÊU THÍCH VỪA ĐƯỢC DÁN VÀO ĐÂY -->
<div class="favorite-box">
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="javascript:void(0)"
           onclick="toggleFavorite(<?= $product['maSanPham'] ?>, this)"
           class="favorite-btn <?= $daYeuThich ? 'liked' : '' ?>"
           id="fav-btn">
            <span class="heart"><?= $daYeuThich ? '❤️' : '🤍' ?></span>
            <span id="favorite-count"><?= number_format($totalFavorite) ?></span>
        </a>
    <?php else: ?>
        <span class="favorite-btn">
            🤍 <span><?= number_format($totalFavorite) ?></span>
        </span>
    <?php endif; ?>
</div>




<!--thêm lượt xem -->
<div class="view-count">
👁 <?= number_format($product['luotXem'] ?? 0) ?> lượt xem
</div>

<div class="rating-box">
<strong><?= $saoTrungBinh ?> ⭐</strong>
<a href="#reviews">(<?= $tongDanhGia ?> đánh giá)</a>
</div>

<p>📦 <?= $product['tinhTrang'] ?> |
Đã bán <?= $daBan ?> |
Còn <?= $product['soLuong'] ?></p>

<?php if($sellerName): ?>
<?php
$shopId = $product['maNguoiBan'] ?? 0;

// avatar từ DB nếu có
$sellerAvatar = $u['avatar'] ?? null;

// nếu DB không có, thử theo quy ước assets/img/avatars/user_<id>.<ext>
$exts = ['png','jpg','jpeg','webp','gif'];

if (empty($sellerAvatar)) {
    foreach ($exts as $e) {
        $cand = "assets/img/avatars/user_{$shopId}." . $e;
        if (is_file(__DIR__ . '/' . $cand)) {
            $sellerAvatar = $cand;
            break;
        }
    }
}

// nếu vẫn không có, thử folder assets/img/shops/<id>.<ext>
if (empty($sellerAvatar)) {
    foreach ($exts as $e) {
        $cand = "assets/img/shops/{$shopId}." . $e;
        if (is_file(__DIR__ . '/' . $cand)) {
            $sellerAvatar = $cand;
            break;
        }
    }
}

// fallback placeholder
if (empty($sellerAvatar) || !is_file(__DIR__ . '/' . $sellerAvatar)) {
    $sellerAvatar = "assets/img/shop-placeholder.png";
}

// cache-buster để trình duyệt thấy ảnh mới ngay
$cacheBuster = is_file(__DIR__ . '/' . $sellerAvatar) ? '?v=' . filemtime(__DIR__ . '/' . $sellerAvatar) : '';
?>
<div class="seller-box">
  <img src="<?= htmlspecialchars($sellerAvatar . $cacheBuster) ?>" alt="<?= htmlspecialchars($sellerName) ?>" class="seller-avatar">
  <div class="seller-info">
    <a class="seller-name" href="shop.php?id=<?= intval($shopId) ?>">
      <?= htmlspecialchars($sellerName) ?>
    </a>
    <?php if($sellerPhone): ?>
      <div class="seller-phone">📞 <a href="tel:<?= htmlspecialchars($sellerPhone) ?>"><?= htmlspecialchars($sellerPhone) ?></a></div>
    <?php endif; ?>
  </div>
  <a class="btn-view-shop" href="shop.php?id=<?= intval($shopId) ?>">Xem shop</a>
</div>
<?php endif; ?>
</p>
<div class="desc">
<?= nl2br(htmlspecialchars($product['moTa'])) ?>
</div>

<!-- ==================== NÚT MUA HÀNG - THÊM VÀO ĐÂY ==================== -->
    <div style="margin: 25px 0 30px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
            <label><strong>Số lượng:</strong></label>
            <input type="number" id="qty" value="1" min="1" 
                   style="width: 80px; padding: 8px; font-size: 16px;">
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <!-- Thêm vào giỏ hàng -->
            <button onclick="addToCart(<?= $product['maSanPham'] ?>)" 
                    style="padding: 14px 28px; background: #ff9900; color: white; border: none; border-radius: 6px; font-size: 17px; font-weight: bold; cursor: pointer; flex: 1;">
                🛒 Thêm vào giỏ hàng
            </button>
            <a href="caidat/baocaosp.php?id=<?= $product['maSanPham'] ?>"
            style="padding: 14px 28px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-size: 17px; font-weight: bold; text-align: center;">
                ⚠ Báo cáo sản phẩm
            </a>
        </div>
    </div>
    <!-- ==================== HẾT PHẦN NÚT ==================== -->

</div>

</div>
</section>
<?php if(!empty($resultSuggest)): ?>

<h2 style="margin-top:30px;">Sản phẩm gợi ý</h2>

<div class="suggest-wrapper">
  <button class="nav prev" aria-label="Trước">‹</button>
  
  <div class="suggest-container">
    <div class="product-list" id="suggestList">
      <?php foreach($resultSuggest as $sp): ?>
        <div class="item">
          <a href="product.php?id=<?= $sp['maSanPham'] ?>">
            <img src="assets/img/<?= htmlspecialchars($sp['hinhAnh']) ?>" alt="<?= htmlspecialchars($sp['tenSanPham']) ?>">
            <p><?= htmlspecialchars($sp['tenSanPham']) ?></p>
            <span><?= number_format($sp['gia'],0,',','.') ?>₫</span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <button class="nav next" aria-label="Sau">›</button>
</div>

<?php endif; ?>

<?php endif; ?>

</main>

<script>
const mainImg = document.getElementById("mainImage");
const mainVideo = document.getElementById("mainVideo");

document.querySelectorAll(".thumb-img").forEach(img => {

    img.addEventListener("click", function(){

        if(mainVideo){
            mainVideo.pause();
            mainVideo.style.display = "none";
        }

        mainImg.src = this.src;
        mainImg.style.display = "block";
    });

});

document.querySelector(".thumb-video")?.addEventListener("click", () => {

    if(mainVideo){

        mainImg.style.display = "none";

        mainVideo.style.display = "block";

        mainVideo.play();

    }

});
</script>

<!-- REVIEW SECTION -->
<section id="reviews" class="review-box">

<h2>Đánh giá sản phẩm</h2>

<!-- THỐNG KÊ -->
<?php for($i=5;$i>=1;$i--):
$count=$starStats[$i]??0;
$percent=$totalStars?round(($count/$totalStars)*100):0;
?>
<div class="rating-row">
  <span class="star-label"><?= $i ?>⭐</span>

  <div class="rating-bar">
    <div class="rating-fill" style="width:<?= $percent ?>%"></div>
  </div>

  <span class="percent"><?= $percent ?>%</span>
</div>
<?php endfor; ?>

<hr>

<!-- FORM ĐÁNH GIÁ -->
<?php if($daMua && !$daDanhGia): ?>
<form method="POST">

<div class="star-select">
<?php for($i=5;$i>=1;$i--): ?>
  <input type="radio" name="soSao" value="<?= $i ?>" id="star<?= $i ?>">
  <label for="star<?= $i ?>">★</label>
<?php endfor; ?>
</div>

<textarea name="binhLuan" required placeholder="Nhập nhận xét..."></textarea>
<button name="guiDanhGia">Gửi đánh giá</button>

</form>
<?php elseif(isset($_SESSION['user_id']) && !$daMua): ?>
<p>Bạn cần mua sản phẩm để đánh giá.</p>
<?php endif; ?>

<hr>

<!-- DANH SÁCH REVIEW -->
<?php while($rv=$resultReview->fetch_assoc()): ?>
<div class="review-item">

<strong><?= htmlspecialchars($rv['tenNguoiDung']) ?></strong>
<?= str_repeat("⭐",$rv['soSao']) ?>

<p><?= nl2br(htmlspecialchars($rv['binhLuan'])) ?></p>
<small><?= $rv['ngayDanhGia'] ?></small>

<!-- HIỂN THỊ TRẢ LỜI -->
<?php if(!empty($rv['traLoiNguoiBan'])): ?>
<div class="seller-reply">

<strong><?= htmlspecialchars($sellerName) ?>:</strong>
<p><?= nl2br(htmlspecialchars($rv['traLoiNguoiBan'])) ?></p>
<small><?= $rv['ngayTraLoi'] ?></small>

<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']==$product['maNguoiBan']): ?>

<a href="?id=<?= $id ?>&editReply=<?= $rv['id'] ?>#reviews">✏ Sửa trả lời</a>

<?php if(isset($_GET['editReply']) && $_GET['editReply']==$rv['id']): ?>

<form method="POST">

<input type="hidden" name="review_id" value="<?= $rv['id'] ?>">

<textarea name="traLoi" required><?= htmlspecialchars($rv['traLoiNguoiBan']) ?></textarea>

<button name="suaTraLoi">Cập nhật</button>

</form>

<?php endif; ?>

<?php endif; ?>

</div>
<?php endif; ?>

<?php if(
isset($_SESSION['user_id']) 
&& $_SESSION['user_id']==$product['maNguoiBan']
&& empty($rv['traLoiNguoiBan'])
): ?>

<form method="POST">
<input type="hidden" name="review_id" value="<?= $rv['id'] ?>">

<textarea name="traLoi" placeholder="Trả lời đánh giá..." required></textarea>

<button name="traLoiDanhGia">Trả lời</button>

</form>

<?php endif; ?>


<!-- NGƯỜI MUA ĐƯỢC SỬA / XÓA REVIEW -->
<?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']==$rv['maNguoiDung']): ?>

<div class="review-actions">
<a href="?id=<?= $id ?>&xoa=<?= $rv['id'] ?>" onclick="return confirm('Xóa đánh giá?')">🗑 Xóa</a>
</div>

<form method="POST">
<input type="hidden" name="review_id" value="<?= $rv['id'] ?>">

<select name="soSao">
<?php for($i=5;$i>=1;$i--): ?>
<option value="<?= $i ?>" <?= $rv['soSao']==$i?'selected':'' ?>><?= $i ?>⭐</option>
<?php endfor; ?>
</select>

<textarea name="binhLuan"><?= htmlspecialchars($rv['binhLuan']) ?></textarea>

<button name="suaDanhGia">Cập nhật</button>

</form>

<?php endif; ?>

</div>
<?php endwhile; ?>
</section>
<script>
function addToCart(id) {

    let qty = document.getElementById("qty").value;

    fetch("cart.php?action=add&id=" + id + "&qty=" + qty)
    .then(res => res.text())
    .then(data => {

        if (data.trim() === "OK") {

            alert("✅ Đã thêm vào giỏ hàng!");

            // cập nhật số trên header
            updateCartCount();

        } else {
            alert("❌ Lỗi thêm giỏ hàng");
        }

    })
    .catch(() => {
        alert("❌ Không kết nối được server");
    });
}

function updateCartCount() {
    fetch("cart_count.php")
    .then(res => res.text())
    .then(count => {
        document.querySelector(".cart-count").innerText = count;
    });
}
</script>

<script>
// Đổi ảnh thumbnail
function changeMainImage(thumb) {
    const mainImg = document.getElementById("mainImage");
    const mainVideo = document.getElementById("mainVideo");

    if (mainVideo) {
        mainVideo.style.display = "none";
        mainVideo.pause();
    }
    if (mainImg) {
        mainImg.src = thumb.src;
        mainImg.style.display = "block";
    }
}

// Play video
function playMainVideo() {
    const mainImg = document.getElementById("mainImage");
    const mainVideo = document.getElementById("mainVideo");
    if (mainVideo) {
        mainImg.style.display = "none";
        mainVideo.style.display = "block";
        mainVideo.play();
    }
}

// ====================== ZOOM BẰNG LĂN CHUỘT ======================
function zoomImage(img) {
    if (!img || !img.src) return;

    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.95); display: flex; align-items: center;
        justify-content: center; z-index: 10000; cursor: zoom-out; overflow: hidden;
    `;

    let scale = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;

    const zoomedImg = document.createElement('img');
    zoomedImg.src = img.src;
    zoomedImg.style.cssText = `
        max-width: 90%; max-height: 90%; border-radius: 10px;
        transition: transform 0.1s; cursor: grab; user-select: none;
    `;

    modal.appendChild(zoomedImg);
    document.body.appendChild(modal);

    // Lăn chuột để zoom
    modal.addEventListener('wheel', function(e) {
        e.preventDefault();
        
        const delta = e.deltaY < 0 ? 1.1 : 0.9;  // Lăn lên = zoom in, lăn xuống = zoom out
        const oldScale = scale;
        scale *= delta;
        scale = Math.max(0.5, Math.min(scale, 5)); // Giới hạn zoom từ 0.5x đến 5x

        const rect = zoomedImg.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        translateX += mouseX * (1 - scale/oldScale);
        translateY += mouseY * (1 - scale/oldScale);

        zoomedImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    });

    // Kéo thả ảnh khi zoom lớn
    zoomedImg.addEventListener('mousedown', (e) => {
        if (scale > 1) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            zoomedImg.style.cursor = 'grabbing';
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            zoomedImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        zoomedImg.style.cursor = 'grab';
    });

    // Click ra ngoài hoặc ESC để đóng
    modal.onclick = function(e) {
        if (e.target === modal) modal.remove();
    };

    document.addEventListener('keydown', function handler(e) {
        if (e.key === "Escape") {
            modal.remove();
            document.removeEventListener('keydown', handler);
        }
    });
}
</script>
<script>

// Toggle Yêu thích

function toggleFavorite(productId, element) {
    fetch('favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${productId}&action=toggle`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const heart = element.querySelector('.heart');
            const countEl = document.getElementById('favorite-count');
            
            // Đổi màu và icon
            if(data.liked) {
                element.classList.add('liked');
                heart.textContent = '❤️';
            } else {
                element.classList.remove('liked');
                heart.textContent = '🤍';
            }
            
            // Cập nhật số lượt
            countEl.textContent = data.total.toLocaleString('vi-VN');
        } else {
            alert(data.message || "Có lỗi xảy ra");
        }
    })
    .catch(() => alert("Không kết nối được server"));
}
</script>

<!-- Các script khác của bạn -->

    <!-- Carousel Sản phẩm gợi ý -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('suggestList');
        const prevBtn = document.querySelector('.nav.prev');
        const nextBtn = document.querySelector('.nav.next');

        if (!track || !prevBtn || !nextBtn) {
            console.log("Không tìm thấy carousel");
            return;
        }

        let scrollPosition = 0;
        const cardWidth = 250;        // Chiều rộng mỗi item + khoảng cách
        const moveItems = 3;          // Di chuyển 3 sp mỗi lần bấm

        nextBtn.addEventListener('click', () => {
            const maxScroll = track.scrollWidth - track.parentElement.offsetWidth;
            scrollPosition += cardWidth * moveItems;
            
            if (scrollPosition > maxScroll) {
                scrollPosition = maxScroll;
            }
            
            track.style.transform = `translateX(-${scrollPosition}px)`;
        });

        prevBtn.addEventListener('click', () => {
            scrollPosition -= cardWidth * moveItems;
            if (scrollPosition < 0) scrollPosition = 0;
            track.style.transform = `translateX(-${scrollPosition}px)`;
        });
    });
    </script>
</body>
</html>
