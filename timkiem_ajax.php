<?php
include "db.php";

// Kiểm tra và nhúng file validation an toàn
if (file_exists('admin/validation.php')) {
    require_once 'admin/validation.php';
}

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

$query = clean($_GET['query'] ?? '');
$price = clean($_GET['price'] ?? '');
$star = clean($_GET['star'] ?? '');

if (!validLength($query, 0, 100)) {
    exit("Query không hợp lệ");
}

$allowPrice = ['', 'duoi50', '50-100', '100-200', 'tren200'];
if (!in_array($price, $allowPrice)) {
    exit("Price không hợp lệ");
}

$allowStar = ['', '3', '4', '5'];
if (!in_array($star, $allowStar)) {
    exit("Star không hợp lệ");
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$where = " WHERE sp.trangThai = 1 ";
$params = [];
$types = "";

if ($query != "") {
    $where .= " AND (sp.tenSanPham LIKE ? OR sp.moTa LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
    $types .= "ss";
}

if ($price == "duoi50") {
    $where .= " AND sp.gia < 50000";
} elseif ($price == "50-100") {
    $where .= " AND sp.gia BETWEEN 50000 AND 100000";
} elseif ($price == "100-200") {
    $where .= " AND sp.gia BETWEEN 100000 AND 200000";
} elseif ($price == "tren200") {
    $where .= " AND sp.gia > 200000";
}

$sql = "
SELECT sp.*, IFNULL(AVG(dg.soSao), 0) AS saoTB
FROM sanpham sp
LEFT JOIN danhgia dg ON sp.maSanPham = dg.maSanPham
$where
GROUP BY sp.maSanPham
";

if ($star != "") {
    $sql .= " HAVING saoTB >= " . intval($star);
}

$sql .= " ORDER BY sp.maSanPham DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

// IN KẾT QUẢ RENDER
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $img = 'assets/img/' . ($row['hinhAnh'] ?: 'placeholder.png');
        ?>
        <div class="card" onclick="window.location.href='product.php?id=<?= $row['maSanPham'] ?>'">
            <div class="thumb">
                <img src="<?= htmlspecialchars($img ?? '') ?>">
            </div>
            <div class="meta">
                <div class="title"><?= htmlspecialchars($row['tenSanPham'] ?? '') ?></div>
                <div class="price"><?= number_format($row['gia'], 0, ',', '.') ?> VND</div>
                <div>⭐ <?= round($row['saoTB'], 1) ?></div>
            </div>
            <div class="card-actions">
                <a class="btn btn-outline" href="cart.php?action=add&id=<?= $row['maSanPham'] ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:6px 8px;">
                    <img src="assets/img/addcart.png" style="height:20px;width:auto;">
                </a>
            </div>
        </div>
        <?php
    }
} else {
    echo '<p style="color:white; width: 100%; padding: 20px 0; grid-column: 1 / -1;">Không tìm thấy sản phẩm phù hợp với bộ lọc.</p>';
}
?>