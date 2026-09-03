<?php
session_start();
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* KIỂM TRA DỮ LIỆU VNPay*/
if (!isset($_GET['vnp_ResponseCode']) || !isset($_GET['vnp_TxnRef'])) {
    die("Dữ liệu VNPay không hợp lệ");
}

$maDonHang    = intval($_GET['vnp_TxnRef']);
$responseCode = $_GET['vnp_ResponseCode'];
$maNguoiDung  = $_SESSION['user_id'] ?? null;

/*THANH TOÁN THẤT BẠI*/
if ($responseCode !== '00') {

    $conn->begin_transaction();

    try {
        // Xóa thanh toán
        $stmt = $conn->prepare("DELETE FROM thanhtoan WHERE maDonHang = ?");
        $stmt->bind_param("i", $maDonHang);
        $stmt->execute();
        $stmt->close();

        // Xóa chi tiết đơn hàng
        $stmt = $conn->prepare("DELETE FROM chitietdonhang WHERE maDonHang = ?");
        $stmt->bind_param("i", $maDonHang);
        $stmt->execute();
        $stmt->close();

        // Xóa đơn hàng
        $stmt = $conn->prepare("DELETE FROM donhang WHERE maDonHang = ?");
        $stmt->bind_param("i", $maDonHang);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
    }

    echo "<script>
        alert('❌ Thanh toán thất bại');
        window.location='cart.php';
    </script>";
    exit;
}

/* THANH TOÁN THÀNH CÔNG */
$conn->begin_transaction();

try {

    // 1. Cập nhật đơn hàng
    $stmt = $conn->prepare("
        UPDATE donhang 
        SET trangThai = 'Đã thanh toán'
        WHERE maDonHang = ?
    ");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    // 2. Cập nhật bảng thanh toán
    $stmt = $conn->prepare("
        UPDATE thanhtoan
        SET trangThai = 'success'
        WHERE maDonHang = ?
    ");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    // ================= MACHINE LEARNING =================

// Lấy các sản phẩm trong đơn hàng
$stmtSP = $conn->prepare("
    SELECT maSanPham
    FROM chitietdonhang 
    WHERE maDonHang = ?
");
$stmtSP->bind_param("i", $maDonHang);
$stmtSP->execute();

$resultSP = $stmtSP->get_result();

while($row = $resultSP->fetch_assoc()){

    $maSanPham = $row['maSanPham'];

    $stmtML = $conn->prepare("
        INSERT INTO user_interactions
        (maTaiKhoan, maSanPham, soLanXem, yeuThich, lienHe, muaHang)
        VALUES (?, ?, 0, 0, 0, 1)
        ON DUPLICATE KEY UPDATE
            muaHang = 1
    ");

    $stmtML->bind_param(
        "ii",
        $maNguoiDung,
        $maSanPham
    );

    $stmtML->execute();
    $stmtML->close();
}

$stmtSP->close();

    /* XÓA CHỈ SẢN PHẨM ĐÃ CHỌN*/
    if ($maNguoiDung !== null && isset($_SESSION['cart_temp'])) {

        $selected = $_SESSION['cart_temp'];

        if (!empty($selected)) {

            $placeholders = implode(',', array_fill(0, count($selected), '?'));
            $types = str_repeat('i', count($selected) + 1);

            $sql = "DELETE FROM giohang 
                    WHERE maNguoiDung = ? 
                    AND maSanPham IN ($placeholders)";

            $stmtDel = $conn->prepare($sql);

            $params = array_merge([$maNguoiDung], $selected);
            $stmtDel->bind_param($types, ...$params);

            $stmtDel->execute();
            $stmtDel->close();
        }
    }

    /* XÓA SESSION TẠM */
    unset($_SESSION['cart_temp']);
    unset($_SESSION['maDonHang']);

    $conn->commit();

    header("Location: order_success.php?maDonHang=" . $maDonHang);
    exit;

} catch (Exception $e) {

    $conn->rollback();
    die("Lỗi hệ thống: " . $e->getMessage());
}
?>