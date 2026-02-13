<?php
session_start();
include "db.php";

/* ===== LẤY ID ===== */
$id = $_GET['id'] ?? 0;

/* ===== ĐÁNH GIÁ ===== */
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

/* ===== ĐÃ BÁN ===== */
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

/* ===== SẢN PHẨM ===== */
$stmt = $conn->prepare("SELECT * FROM sanpham WHERE maSanPham=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===== NGƯỜI BÁN ===== */
$sellerName = $sellerPhone = '';
if($product && $product['maNguoiBan']){
  $s = $conn->prepare(
    "SELECT tenNguoiDung,tenDangNhap,soDienThoai 
     FROM taikhoan WHERE maTaiKhoan=?"
  );
  $s->bind_param("i",$product['maNguoiBan']);
  $s->execute();
  if($u=$s->get_result()->fetch_assoc()){
    $sellerName = $u['tenNguoiDung'] ?: $u['tenDangNhap'];
    $sellerPhone = $u['soDienThoai'] ?? '';
  }
  $s->close();
}

/* ===== ĐÃ MUA? ===== */
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

/* ===== CẬP NHẬT TÌNH TRẠNG ===== */
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

/* ===== GỢI Ý ===== */
$resultSuggest=null;
if($product){
  $gia=$product['gia'];
  $min=$gia*0.4; $max=$gia*1.8;
  $s=$conn->prepare("
    SELECT sp.maSanPham,sp.tenSanPham,sp.gia,sp.hinhAnh,
           IFNULL(AVG(dg.soSao),0) saoTB
    FROM sanpham sp
    LEFT JOIN danhgia dg ON sp.maSanPham=dg.maSanPham
    WHERE sp.maDanhMuc=? AND sp.maSanPham!=?
      AND sp.gia BETWEEN ? AND ?
    GROUP BY sp.maSanPham
    ORDER BY saoTB DESC,RAND()
    LIMIT 12
  ");
  $s->bind_param("iidd",
    $product['maDanhMuc'],$id,$min,$max
  );
  $s->execute();
  $resultSuggest=$s->get_result();
}
/* =========================================================
   =================== PHẦN ĐÁNH GIÁ =======================
========================================================= */

/* ===== MỖI NGƯỜI 1 REVIEW ===== */
$daDanhGia = false;
if(isset($_SESSION['user_id'])){
  $check = $conn->prepare("SELECT id FROM danhgia WHERE maSanPham=? AND maNguoiDung=?");
  $check->bind_param("ii",$id,$_SESSION['user_id']);
  $check->execute();
  $check->store_result();
  $daDanhGia = $check->num_rows > 0;
  $check->close();
}

/* ===== GỬI ĐÁNH GIÁ ===== */
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

/* ===== XÓA ===== */
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

/* ===== SỬA ===== */
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

/* ===== THỐNG KÊ SAO ===== */
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

/* ===== DANH SÁCH REVIEW ===== */
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
    <h1><a href="index.php">Shop Đồ Cũ</a></h1>
    <nav>
      <a href="cart.php">🛒Giỏ hàng</a>
      <?php if (isset($_SESSION['user_id'])): ?>
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

<!-- ===== TRÁI ===== -->
<div class="gallery">

<?php if(!empty($product['video'])): ?>
  <video id="mainVideo"
         class="main-media active"
         muted autoplay loop playsinline>
    <source src="assets/video/<?= htmlspecialchars($product['video']) ?>"
            type="video/mp4">
  </video>
<?php endif; ?>

<img id="mainImage"
     class="main-media <?= !empty($product['video']) ? '' : 'active' ?>"
     src="assets/img/<?= htmlspecialchars($product['hinhAnh']) ?>">

<div class="thumbs1">
<?php for($i=1;$i<=7;$i++):
$f='hinhAnh'.$i;
if(!empty($product[$f])): ?>
  <img class="thumb-img"
       src="assets/img_phu/<?= htmlspecialchars($product[$f]) ?>">
<?php endif; endfor; ?>

<?php if(!empty($product['video'])): ?>
  <img class="thumb-video" src="assets/img/play.png">
<?php endif; ?>
</div>

<div class="left-action">
<h3><?= htmlspecialchars($product['tenSanPham']) ?></h3>
<form action="cart.php" method="get">
<input type="hidden" name="action" value="add">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="number" name="qty" value="1" min="1">
<button>Thêm vào giỏ</button>
</form>
</div>

</div>

<!-- ===== PHẢI ===== -->
<div class="info-box">

<div class="price">
<?= number_format($product['gia'],0,',','.') ?> ₫
</div>

<div class="rating-box">
<strong><?= $saoTrungBinh ?> ⭐</strong>
<a href="#reviews">(<?= $tongDanhGia ?> đánh giá)</a>
</div>

<p>📦 <?= $product['tinhTrang'] ?> |
Đã bán <?= $daBan ?> |
Còn <?= $product['soLuong'] ?></p>

<?php if($sellerName): ?>
<p>👤 <?= htmlspecialchars($sellerName) ?>
<?php if($sellerPhone): ?>
 | 📞 <a href="tel:<?= $sellerPhone ?>"><?= $sellerPhone ?></a>
<?php endif; ?>
</p>
<?php endif; ?>

<div class="desc">
<?= nl2br(htmlspecialchars($product['moTa'])) ?>
</div>

</div>
</section>

<?php endif; ?>
</main>

<script>
const mainImg = document.getElementById('mainImage');
const mainVideo = document.getElementById('mainVideo');

if(mainImg){

    // 👉 Lưu ảnh chính ban đầu
    const originalImage = mainImg.src;

    document.querySelectorAll('.thumb-img').forEach(i=>{
        i.addEventListener('click', function(){

            if(mainVideo){
                mainVideo.pause();
                mainVideo.classList.remove('active');
            }

            // Nếu click lại chính ảnh đang hiển thị → quay về ảnh gốc
            if(mainImg.src === this.src){
                mainImg.src = originalImage;
            } else {
                mainImg.src = this.src;
            }

            mainImg.classList.add('active');
        });
    });

    // Nếu có video
    document.querySelector('.thumb-video')?.addEventListener('click',()=>{
        mainImg.classList.remove('active');
        mainVideo.classList.add('active');
        mainVideo.play();
    });
}
</script>

<!-- =================== REVIEW SECTION =================== -->
<section id="reviews" class="review-box">

<h2>Đánh giá sản phẩm</h2>

<!-- THỐNG KÊ -->
<?php for($i=5;$i>=1;$i--):
$count=$starStats[$i]??0;
$percent=$totalStars?round(($count/$totalStars)*100):0;
?>
<div class="rating-row">
  <span><?= $i ?>⭐</span>
  <div class="rating-bar">
    <div class="rating-fill" style="width:<?= $percent ?>%"></div>
  </div>
  <span><?= $percent ?>%</span>
</div>
<?php endfor; ?>

<hr>

<!-- FORM ĐÁNH GIÁ -->
<?php if($daMua && !$daDanhGia): ?>
<form method="POST">

<div class="star-select">
<?php for($i=5;$i>=1;$i--): ?>
<input type="radio" name="soSao" value="<?= $i ?>" id="star<?= $i ?>" required>
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

</body>
</html>
