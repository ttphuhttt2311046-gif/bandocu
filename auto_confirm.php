<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

/*
    BƯỚC 1:
    Tự động chuyển đơn đủ 1 ngày
    từ trạng thái 1 → 2
*/

$sqlAuto = "UPDATE donhang 
            SET trangThai = 2
            WHERE trangThai = 1
            AND ngayDat <= NOW() - INTERVAL 1 DAY";

$conn->query($sqlAuto);


/*
    BƯỚC 2:
    Tạo hóa đơn cho đơn đã hoàn thành (trangThai = 2)
    nhưng chưa có hóa đơn
*/

$sql = "SELECT d.*
        FROM donhang d
        WHERE d.trangThai = 2
        AND d.maDonHang NOT IN (SELECT maDonHang FROM donhang_shop)";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $maDonHang = $row['maDonHang'];
    $maNguoiMua = $row['maNguoiMua'];
    $tongTien = $row['tongTien'];

    // Lấy người bán
    $sqlBan = "SELECT sp.maNguoiBan
               FROM chitietdonhang ct
               JOIN sanpham sp ON ct.maSanPham = sp.maSanPham
               WHERE ct.maDonHang = ?
               LIMIT 1";

    $stmtBan = $conn->prepare($sqlBan);
    $stmtBan->bind_param("i", $maDonHang);
    $stmtBan->execute();
    $resultBan = $stmtBan->get_result();
    $ban = $resultBan->fetch_assoc();

    if(!$ban) continue;

    $maNguoiBan = $ban['maNguoiBan'];

    // Tạo hóa đơn
    $stmt = $conn->prepare("
        INSERT INTO donhang_shop (maDonHang, maNguoiMua, maNguoiBan, tongTien)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("iiid", $maDonHang, $maNguoiMua, $maNguoiBan, $tongTien);
    $stmt->execute();
}
?>