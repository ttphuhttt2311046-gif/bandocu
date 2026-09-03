<?php
session_start();
include "db.php";

/*CẤU HÌNH VNPAY (SANDBOX) */
$vnp_TmnCode = "39RNCVZM"; // mã demo
$vnp_HashSecret = "Y7GFSRHIF6NMF42LOAI116GNW6ZTXQ0R"; // mã bí mật demo
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost/ban_do_cu2/bandocu/vnpay_return.php";

/*KIỂM TRA ĐƠN HÀNG */
if (!isset($_GET['order_id'])) {
    die("Thiếu mã đơn hàng");
}

$maDonHang = intval($_GET['order_id']);

/*LẤY TỔNG TIỀN ĐƠN HÀNG */
$stmt = $conn->prepare("SELECT tongTien FROM donhang WHERE maDonHang = ?");
$stmt->bind_param("i", $maDonHang);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Đơn hàng không tồn tại");
}

$amount = $order['tongTien'] * 100; // VNPay yêu cầu *100

/* TẠO DỮ LIỆU THANH TOÁN*/
$vnp_TxnRef = $maDonHang;
$vnp_OrderInfo = "Thanh toán đơn hàng #".$maDonHang;
$vnp_OrderType = "billpayment";
$vnp_Locale = "vn";
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef
);

/* TẠO CHỮ KÝ */
ksort($inputData);
$hashdata = "";
$query = "";

foreach ($inputData as $key => $value) {
    // HASH: KHÔNG urlencode
    $hashdata .= ($hashdata ? '&' : '') . $key . "=" . $value;

    // URL: CÓ urlencode
    $query .= urlencode($key) . "=" . urlencode($value) . "&";
}

$vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$vnp_Url = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;


/* REDIRECT SANG VNPAY */
header("Location: " . $vnp_Url);
exit;