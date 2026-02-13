<?php
if (!isset($conn)) {
    include "db.php";
}

/* KHÔNG cần session_start() ở đây */
/* Vì file này thường được include từ index.php */

/* CẤU HÌNH */
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/* FILTER */
$where_sql = " WHERE trangThai = 1 AND duyetTrangThai = 1 ";
$queryStringBase = "";

if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $catId = intval($_GET['cat']);
    $where_sql .= " AND maDanhMuc = $catId ";
    $queryStringBase .= "cat=$catId&";
}

/* TOTAL */
$total_sql = "SELECT COUNT(*) AS total FROM sanpham $where_sql";
$total_res = $conn->query($total_sql);
$total_row = $total_res->fetch_assoc();
$totalItems = intval($total_row['total']);
$totalPages = max(1, ceil($totalItems / $limit));

/* QUERY */
$sql = "SELECT maSanPham, tenSanPham, moTa, gia, giamGia,
        hinhAnh, soLuong
        FROM sanpham
        $where_sql
        ORDER BY maSanPham DESC
        LIMIT $limit OFFSET $offset";

$res = $conn->query($sql);

/* ================= GRID ================= */
echo '<div class="grid">';

if ($res && $res->num_rows > 0) {

    while ($row = $res->fetch_assoc()) {

        $img = 'assets/img/' . ($row['hinhAnh'] ?: 'placeholder.png');
        $isHetHang = ($row['soLuong'] <= 0);
        $cardClass = $isHetHang ? 'card het-hang' : 'card';

        $giaGoc = $row['gia'];
        $giam = intval($row['giamGia']);
        $giaMoi = $giaGoc * (100 - $giam) / 100;

        echo '<div class="'.$cardClass.'">';

            /* LINK CHI TIẾT */
            echo '<a class="card-link" href="product.php?id='.$row['maSanPham'].'">';

                echo '<div class="img-wrap">';
                    echo '<img src="'.$img.'" alt="'.$row['tenSanPham'].'">';
                echo '</div>';

                echo '<div class="title">'.$row['tenSanPham'].'</div>';

            echo '</a>';

            /* GIÁ */
            echo '<div class="price">';
                if ($giam > 0) {
                    echo '<span class="old-price">'.number_format($giaGoc,0,',','.').' VND</span>';
                    echo '<span class="new-price">'.number_format($giaMoi,0,',','.').' VND</span>';
                } else {
                    echo number_format($giaGoc,0,',','.').' VND';
                }
            echo '</div>';

            echo '<p class="desc">'.mb_strimwidth($row['moTa'],0,80,'...').'</p>';

            /* BUTTON */
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
    echo '<p>Chưa có sản phẩm</p>';
}

echo '</div>';

/* ================= PAGINATION ================= */
if ($totalPages > 1) {

    echo '<div class="pagination">';

    if ($page > 1) {
        echo '<a href="?'.$queryStringBase.'page='.($page-1).'"><</a>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo '<a class="'.$active.'" href="?'.$queryStringBase.'page='.$i.'">'.$i.'</a>';
    }

    if ($page < $totalPages) {
        echo '<a href="?'.$queryStringBase.'page='.($page+1).'">></a>';
    }

    echo '</div>';
}
?>

<script>
function addToCart(id) {
    fetch("cart.php?action=add&id=" + id)
    .then(res => res.text())
    .then(() => {
        alert("Đã thêm vào giỏ hàng!");
    })
    .catch(() => {
        alert("Có lỗi xảy ra!");
    });
}
</script>
