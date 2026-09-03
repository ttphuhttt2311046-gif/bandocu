<?php
session_start();
include "db.php";
include "taohoadon.php";

if (!isset($_SESSION['user_id']) || $_SESSION['vaitro'] !== 'seller') {
    die("Không có quyền");
}

if (!isset($_GET['id'], $_GET['status'])) {
    die("Thiếu tham số");
}

$maDonHang = (int)$_GET['id'];
$status    = (int)$_GET['status'];

if ($status < 0 || $status > 3) {
    die("Trạng thái không hợp lệ");
}

$conn->begin_transaction();

try {

    /* UPDATE TRẠNG THÁI ĐƠN*/
    $stmt = $conn->prepare("
        UPDATE donhang 
        SET trangThai = ?
        WHERE maDonHang = ?
    ");

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("ii", $status, $maDonHang);
    $stmt->execute();
    $stmt->close();


    /*  NẾU ĐANG GIAO (1) → TẠO HÓA ĐƠN*/
    if ($status == 1) {

        // Kiểm tra đã có hóa đơn chưa
        $check = $conn->prepare("SELECT maDonHang_Shop FROM donhang_shop WHERE maDonHang = ?");
        if (!$check) {
            throw new Exception($conn->error);
        }

        $check->bind_param("i", $maDonHang);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            // Chưa có → tạo mới
// Thêm $_SESSION['user_id'] làm tham số thứ 3 để đại diện cho người bán
taoHoaDon($conn, $maDonHang, $_SESSION['user_id']);        }

        $check->close();
    }

    $conn->commit();

    header("Location: seller_orders.php");
    exit;

} catch (Exception $e) {

    $conn->rollback();
    die("Lỗi hệ thống: " . $e->getMessage());
}