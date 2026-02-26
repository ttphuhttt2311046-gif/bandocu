<?php
session_start();
include "../db.php";

if (isset($_POST['status']) && isset($_SESSION['user_id'])) {
    $status = intval($_POST['status']);
    $user_id = intval($_SESSION['user_id']);

    // 1. Cập nhật Database cho tài khoản
    $stmt1 = $conn->prepare("UPDATE taikhoan SET shopThamGiaSuKien = ? WHERE maTaiKhoan = ?");
    $stmt1->bind_param("ii", $status, $user_id);
    
    // 2. Đồng bộ cho toàn bộ sản phẩm của người bán đó
    $stmt2 = $conn->prepare("UPDATE sanpham SET thamGiaSuKien = ? WHERE maNguoiBan = ?");
    $stmt2->bind_param("ii", $status, $user_id);

    if ($stmt1->execute() && $stmt2->execute()) {
        // 🔥 QUAN TRỌNG: Cập nhật lại Session để giao diện thay đổi ngay
        $_SESSION['shopThamGiaSuKien'] = $status;
        echo "OK";
    } else {
        echo "Lỗi truy vấn";
    }
}