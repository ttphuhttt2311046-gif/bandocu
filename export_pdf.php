<?php
session_start();
require_once __DIR__ . "/dompdf/autoload.inc.php";
require_once __DIR__ . "/db.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Ép kiểu dữ liệu để đảm bảo an toàn
$id = intval($_GET['id']);
// 2. Dùng Prepared Statement cho câu lệnh JOIN (Tối ưu + Bảo mật)
$stmt = $conn->prepare("
    SELECT dh.*, tk.tenNguoiDung, tk.diaChi
    FROM donhang dh
    JOIN taikhoan tk ON dh.maNguoiMua = tk.maTaiKhoan
    WHERE dh.maDonHang = ?
");
// 3. Truyền tham số và thực thi
$stmt->bind_param("i", $id);
$stmt->execute();
// 4. Lấy kết quả
$result = $stmt->get_result();
$order = $result->fetch_assoc();


if (!$order) {
    die("Không tìm thấy đơn!");
}

/* ===== LẤY SẢN PHẨM ===== */
$stmt = $conn->prepare("
SELECT ct.*, sp.tenSanPham
FROM chitietdonhang ct
JOIN sanpham sp ON ct.maSanPham = sp.maSanPham
WHERE ct.maDonHang = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result();


/* ===== HTML PDF ===== */
ob_start();
?>

<meta charset="UTF-8">

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 14px;
    color: #333;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    border-bottom: 2px solid #ff5722;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.shop-name {
    font-size: 22px;
    font-weight: bold;
    color: #ff5722;
}

.invoice-title {
    text-align: right;
}

/* INFO */
.info p {
    margin: 3px 0;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #ff5722;
    color: #fff;
    padding: 10px;
    text-align: left;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

/* TOTAL */
.total {
    text-align: right;
    margin-top: 20px;
}

.total h2 {
    color: #ff5722;
}
</style>

<div class="header">

<div class="shop-name">
<?php
$logoPath = __DIR__ . '/assets/img/LOGO.png';
$type = pathinfo($logoPath, PATHINFO_EXTENSION);
$data = file_get_contents($logoPath);

$logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<img src="<?= $logo ?>" width="40" style="vertical-align:middle;">
Shop Đồ Cũ
</div>

<div class="invoice-title">
    <b>HÓA ĐƠN</b><br>
    #<?= $order['maDonHang'] ?>
</div>

</div>

<div class="info">
    <p><b>Khách:</b> <?= htmlspecialchars($order['tenNguoiDung'] ?? '') ?></p>
    <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['diaChi'] ?? '') ?></p>
<p>
    <b>Ngày:</b>
    <?= date("d/m/Y H:i", strtotime($order['ngayDat'])) ?>
</p></div>

<table>
<tr>
<th>Sản phẩm</th>
<th>SL</th>
<th>Giá</th>
<th>Thành tiền</th>
</tr>

<?php while ($i = $items->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($i['tenSanPham'] ?? '') ?></td>
<td><?= htmlspecialchars($i['soLuong'] ?? '') ?></td>
<td><?= number_format($i['donGia'],0,',','.') ?> VND</td>
<td><?= number_format($i['thanhTien'],0,',','.') ?> VND</td>
</tr>
<?php endwhile; ?>
</table>

<div class="total">
    <h2>Tổng: <?= number_format($order['tongTien'],0,',','.') ?> VND</h2>
</div>

<?php
$html = ob_get_clean();



/* ===== DOMPDF ===== */
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* ===== LƯU FILE ===== */
$dir = __DIR__ . "/uploads/invoices/";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$fileName = "donhang_shop_" . $id . ".pdf";
$filePath = $dir . $fileName;

file_put_contents($filePath, $dompdf->output());

/* ===== LINK ===== */
$link = "uploads/invoices/" . $fileName;

/* ===== GỬI TIN NHẮN (KHÔNG LƯU HTML) ===== */
$nguoiGui = $_SESSION['user_id'];
$nguoiNhan = $order['maNguoiMua'];

$noiDung = "📄 Hóa đơn #$id";

$sql = "INSERT INTO nhantin (noiDung, maNguoiGui, maNguoiNhan)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL lỗi: " . $conn->error);
}

$stmt->bind_param("sii", $noiDung, $nguoiGui, $nguoiNhan);

if (!$stmt->execute()) {
    die("Execute lỗi: " . $stmt->error);
}

/* ===== CHUYỂN TRANG ===== */
header("Location: inhoadon.php?id=$id&sent=1");
exit;