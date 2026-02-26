<?php
if (!isset($conn)) {
    include "db.php";
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
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
$sql = "SELECT maSanPham, tenSanPham, moTa, gia, giamGia, thamGiaSuKien, hinhAnh, soLuong
        FROM sanpham
        $where_sql
        ORDER BY maSanPham DESC
        LIMIT $limit OFFSET $offset";

$res = $conn->query($sql);

$now = date('Y-m-d H:i:s');
$event = null;

// Lấy sự kiện đang diễn ra
$eventSql = "
  SELECT * FROM sukien_khuyenmai 
  WHERE trangThai = 1 
    AND ngayBatDau <= '$now' 
    AND ngayKetThuc >= '$now' 
  LIMIT 1
";
$eventRes = $conn->query($eventSql);
if ($eventRes && $eventRes->num_rows > 0) {
    $event = $eventRes->fetch_assoc();
}

/* ================= GRID ================= */
echo '<div class="grid">';
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $img = 'assets/img/' . ($row['hinhAnh'] ?: 'placeholder.png');
        $isHetHang = ($row['soLuong'] <= 0);
        $cardClass = $isHetHang ? 'card het-hang' : 'card';
        $giaGoc = $row['gia'];

        $giamCaNhan = intval($row['giamGia']);
        
        // 2. Lấy % giảm giá từ sự kiện Admin (Chỉ khi thamGiaSuKien == 1)
        $giamSuKien = 0;
        $tenSuKienHienThi = "";
        if ($event && isset($row['thamGiaSuKien']) && $row['thamGiaSuKien'] == 1) {
        $giamSuKien = intval($event['mucGiamGia']);
        $tenSuKienHienThi = $event['tenSuKien']; // Lấy tên từ bảng sukien_khuyenmai
        }

        // 3. Chọn mức giảm cao nhất (Không xung đột)
        $giamApDung = max($giamCaNhan, $giamSuKien);

        // 4. Tính giá sau giảm
        $giaMoi = $giaGoc * (100 - $giamApDung) / 100;

        // --- HIỂN THỊ ---
        echo '<div class="'.$cardClass.'" onclick="window.location=\'product.php?id='.$row['maSanPham'].'\'">';
        echo '<div class="img-wrap">';
        echo '<img src="'.$img.'">';

        // Nhãn sự kiện Tết (Nếu muốn hiện cố định)
        if ($tenSuKienHienThi != "") {
    echo '<span class="tet-icon">🎉 ' . htmlspecialchars($tenSuKienHienThi) . '</span>';
}

        // Hiển thị Badge giảm giá
        if ($giamApDung > 0) {
            if ($giamSuKien >= $giamCaNhan && $giamSuKien > 0) {
                echo '<span class="badge-flash">FLASH SALE -'.$giamApDung.'%</span>';
            } else {
                echo '<span class="badge-sale">GIẢM GIÁ -'.$giamApDung.'%</span>';
            }
        }

        if ($isHetHang) {
            echo '<span class="badge-het-hang">TẠM HẾT HÀNG</span>';
        }
        echo '</div>'; // End img-wrap

        echo '<div class="title">'.$row['tenSanPham'].'</div>';
        echo '<div class="price">';
        if ($giamApDung > 0) {
            echo '<span class="old-price">'.number_format($giaGoc,0,',','.').' VND</span>';
            echo '<span class="new-price">'.number_format($giaMoi,0,',','.').' VND</span>';
        } else {
            echo number_format($giaGoc,0,',','.').' VND';
        }
        echo '</div>';

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
        echo '</div>'; // End card
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
