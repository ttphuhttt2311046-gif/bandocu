<?php

function taoHoaDon($conn, $maDonHang, $maNguoiBan)
{
    // 1. Lấy thông tin đơn hàng gốc
    $stmt = $conn->prepare("SELECT maNguoiMua, tongTien FROM donhang WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->bind_result($maNguoiMua, $tongTien);
    $stmt->fetch();
    $stmt->close();

    if (!$maNguoiMua) {
        throw new Exception("Không tìm thấy đơn hàng gốc");
    }

    // 2. Tạo hóa đơn riêng cho shop (bảng donhang_shop)
    $stmtHD = $conn->prepare("
        INSERT INTO donhang_shop (maDonHang, maNguoiMua, maNguoiBan, tongTien, ngayTao)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    if (!$stmtHD) {
        throw new Exception("Lỗi SQL tạo đơn hàng shop: " . $conn->error);
    }

    $stmtHD->bind_param("iiid", $maDonHang, $maNguoiMua, $maNguoiBan, $tongTien);
    $stmtHD->execute();
    $maHoaDon = $stmtHD->insert_id; // Lấy ID (maDonHang_Shop) vừa được tạo
    $stmtHD->close();

    // 3. CẬP NHẬT mã đơn hàng shop vào bảng chi tiết
    // (Gắn kết các sản phẩm của người bán này với hóa đơn shop vừa tạo)
    $stmtCTHD = $conn->prepare("
        UPDATE chitietdonhang
        SET maDonHang_Shop = ?
        WHERE maDonHang = ? AND maNguoiBan = ?
    ");

    if (!$stmtCTHD) {
        throw new Exception("Lỗi SQL cập nhật chi tiết: " . $conn->error);
    }

    // Bind 3 tham số: ID hóa đơn shop, ID đơn hàng gốc, và ID người bán
    $stmtCTHD->bind_param("iii", $maHoaDon, $maDonHang, $maNguoiBan);
    
    if (!$stmtCTHD->execute()) {
        throw new Exception("Lỗi update chi tiết hóa đơn: " . $stmtCTHD->error);
    }
    
    $stmtCTHD->close();
}