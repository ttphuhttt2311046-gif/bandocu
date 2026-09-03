<?php
if (!isset($conn)) {
    include "db.php";
}

/*CẤU HÌNH  */
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/*FILTER*/

$where_sql = " WHERE sp.trangThai = 1 AND sp.duyetTrangThai = 1 ";
$queryStringBase = "";

/*TÌM KIẾM */

if (isset($_GET['query']) && $_GET['query'] != '') {

    $query = $conn->real_escape_string($_GET['query']);

    $where_sql .= " AND sp.tenSanPham LIKE '%$query%' ";

    $queryStringBase .= "query=$query&";
}

/*LỌC DANH MỤC*/

if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {

    $cat = intval($_GET['cat']);

    $where_sql .= " AND sp.maDanhMuc = $cat ";

    $queryStringBase .= "cat=$cat&";
}

/*LỌC GIÁ*/

if (isset($_GET['price']) && is_numeric($_GET['price'])) {

    $price = intval($_GET['price']);

    $queryStringBase .= "price=$price&";

    if ($price == 1) {
        $where_sql .= " AND sp.gia < 50000 ";
    }
    elseif ($price == 2) {
        $where_sql .= " AND sp.gia BETWEEN 50000 AND 100000 ";
    }
    elseif ($price == 3) {
        $where_sql .= " AND sp.gia BETWEEN 100000 AND 200000 ";
    }
    elseif ($price == 4) {
        $where_sql .= " AND sp.gia > 200000 ";
    }
}

/*LỌC SAO*/

$star_filter = "";

if (isset($_GET['star']) && is_numeric($_GET['star'])) {

    $star = intval($_GET['star']);

    $queryStringBase .= "star=$star&";

    $star_filter = " HAVING AVG(dg.soSao) >= $star ";
}

/*TOTAL*/

$total_sql = "
SELECT COUNT(DISTINCT sp.maSanPham) AS total
FROM sanpham sp
LEFT JOIN danhgia dg ON sp.maSanPham = dg.maSanPham
$where_sql
";

$total_res = $conn->query($total_sql);

if (!$total_res) {
    die("SQL ERROR: " . $conn->error);
}

$total_row = $total_res->fetch_assoc();

$totalItems = intval($total_row['total']);

$totalPages = max(1, ceil($totalItems / $limit));

/* QUERY  */

$sql = "
SELECT sp.maSanPham,
       sp.tenSanPham,
       sp.moTa,
       sp.gia,
       sp.giamGia,
       sp.hinhAnh,
       sp.soLuong,
       IFNULL(AVG(dg.soSao),0) AS saoTB
FROM sanpham sp
LEFT JOIN danhgia dg ON sp.maSanPham = dg.maSanPham
$where_sql
GROUP BY sp.maSanPham
$star_filter
ORDER BY sp.maSanPham DESC
LIMIT $limit OFFSET $offset
";

$res = $conn->query($sql);

if (!$res) {
    die("SQL ERROR: " . $conn->error);
}
?>

<style>

.img-wrap{
position: relative;
}

.overlay-out{
position:absolute;
top:50%;
left:0;
width:100%;
transform:translateY(-50%);
background:rgba(0,0,0,0.75);
color:#fff;
padding:12px 0;
font-weight:bold;
font-size:18px;
text-align:center;
letter-spacing:2px;
z-index:2;
}

.het-hang img{
opacity:0.5;
}

.rating{
color:#ff9900;
margin-top:4px;
font-size:14px;
}

</style>

<?php

echo '<div class="grid">';

if ($res->num_rows > 0) {

while ($row = $res->fetch_assoc()) {

$img = 'assets/img/' . ($row['hinhAnh'] ?: 'placeholder.png');

$isHetHang = ($row['soLuong'] <= 0);

$cardClass = $isHetHang ? 'card het-hang' : 'card';

$giaGoc = $row['gia'];

$giam = intval($row['giamGia']);

$giaMoi = $giaGoc * (100 - $giam) / 100;

echo '<div class="'.$cardClass.'">';

echo '<a class="card-link" href="product.php?id='.$row['maSanPham'].'">';

echo '<div class="img-wrap">';

if($giam > 0){
echo '<div class="sale-badge">-'.$giam.'%</div>';
}

echo '<img src="'.$img.'" alt="'.$row['tenSanPham'].'">';

if ($isHetHang) {
echo '<div class="overlay-out">HẾT HÀNG</div>';
}

echo '</div>';

echo '<div class="title">'.$row['tenSanPham'].'</div>';

echo '</a>';

echo '<div class="price">';

if ($giam > 0) {

echo '<span class="old-price">'.number_format($giaGoc,0,',','.').' VND</span>';

echo '<span class="new-price">'.number_format($giaMoi,0,',','.').' VND</span>';

} else {

echo number_format($giaGoc,0,',','.').' VND';

}

echo '</div>';

echo '<div class="rating">⭐ '.round($row['saoTB'],1).'</div>';

echo '<p class="desc">'.mb_strimwidth($row['moTa'],0,80,'...').'</p>';

echo '<div class="card-actions">';

if ($isHetHang) {

echo '<span class="btn-disabled">Hết hàng</span>';

} else {

echo '<button type="button"
class="btn-add-cart"
onclick="addToCart('.$row['maSanPham'].')">
🛒
</button>';

}

echo '</div>';

echo '</div>';

}

} else {

echo '<p>Không tìm thấy sản phẩm</p>';

}

echo '</div>';

/*PAGINATION*/

if ($totalPages > 1) {

echo '<div class="pagination">';

if ($page > 1) {

echo '<a onclick="loadProducts('.($page-1).')"><</a>';

}

for ($i = 1; $i <= $totalPages; $i++) {

$active = ($i == $page) ? 'active' : '';

echo '<a class="'.$active.'" onclick="loadProducts('.$i.')">'.$i.'</a>';

}

if ($page < $totalPages) {

echo '<a onclick="loadProducts('.($page+1).')">></a>';

}

echo '</div>';

}
?>