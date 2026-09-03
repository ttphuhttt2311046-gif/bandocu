<?php
session_start();

include "ai_recommend.php";

function getRecommendations($conn, $current_id, $limit = 8){

    // lấy sản phẩm hiện tại
    $stmt = $conn->prepare("
        SELECT tenSanPham, moTa, maDanhMuc
        FROM sanpham
        WHERE maSanPham = ?
    ");
    $stmt->bind_param("i",$current_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$product){

    $stmt = $conn->prepare("
        SELECT *
        FROM sanpham
        WHERE maSanPham != ?
        AND trangThai = 1
        AND duyetTrangThai = 1
        ORDER BY luotXem DESC
        LIMIT ?
    ");

    $stmt->bind_param("ii",$current_id,$limit);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $result;
}

    $search_term = $product['tenSanPham'] . " " . ($product['moTa'] ?? '');

    $current_category = (int)$product['maDanhMuc'];

    $recommendations = [];

    //================ AI ==================
    if(isset($_SESSION['user_id'])){

        $aiProducts = getAIRecommendations(
            $conn,
            $_SESSION['user_id'],
            $limit
        );

        if(is_array($aiProducts) && count($aiProducts) > 0){
             return $aiProducts;
        }
    }
    //======================================


    // ===== BƯỚC 1: LẤY SẢN PHẨM CÙNG DANH MỤC =====
    if ($current_category > 0) {
        $sql1 = "
            SELECT *, 
                   MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE) AS relevance_score
            FROM sanpham 
            WHERE maSanPham != ?
              AND maDanhMuc = ?
              AND trangThai = 1
              AND duyetTrangThai = 1
              AND (MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE) OR 1=1)
            ORDER BY 
                MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE) DESC,
                luotXem DESC,
                maSanPham DESC
            LIMIT ?
        ";
        
        $stmt1 = $conn->prepare($sql1);
        if ($stmt1) {
            $stmt1->bind_param("siiisi", $search_term, $current_id, $current_category, $search_term, $search_term, $limit);
            if ($stmt1->execute()) {
                $recommendations = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt1->close();
        }
    }

    // ===== BƯỚC 2: NẾU CHƯA ĐỦ, LẤY TỪ DANH MỤC KHÁC NHƯNG PHẢI MATCH NỘI DUNG =====
    if (count($recommendations) < $limit) {
        $need = $limit - count($recommendations);
        $existing_ids = array_column($recommendations, 'maSanPham');
        $existing_ids[] = $current_id;
        
        $placeholders = implode(',', array_fill(0, count($existing_ids), '?'));
        
        $sql2 = "
            SELECT *, 
                   MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE) AS relevance_score
            FROM sanpham 
            WHERE maSanPham NOT IN ($placeholders)
              AND MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE)
              AND trangThai = 1
              AND duyetTrangThai = 1
            ORDER BY 
                MATCH(tenSanPham, moTa) AGAINST(? IN BOOLEAN MODE) DESC,
                luotXem DESC,
                maSanPham DESC
            LIMIT ?
        ";
        
        $stmt2 = $conn->prepare($sql2);
        if ($stmt2) {
            $types = str_repeat('i', count($existing_ids)) . 'sssi';
            $params = array_merge($existing_ids, [$search_term, $search_term, $search_term, $need]);
            $stmt2->bind_param($types, ...$params);
            if ($stmt2->execute()) {
                $extra = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                $recommendations = array_merge($recommendations, $extra);
            }
            $stmt2->close();
        }
    }

    // ===== BƯỚC 3: NẾU VẪN CHƯA ĐỦ, LẤY SẢN PHẨM CÓ LƯỢT XEM CAO =====
    if (count($recommendations) < $limit) {
        $need = $limit - count($recommendations);
        $existing_ids = array_column($recommendations, 'maSanPham');
        $existing_ids[] = $current_id;
        
        $placeholders = implode(',', array_fill(0, count($existing_ids), '?'));
        
        $sql3 = "
            SELECT * FROM sanpham 
            WHERE maSanPham NOT IN ($placeholders)
              AND trangThai = 1
              AND duyetTrangThai = 1
            ORDER BY luotXem DESC, maSanPham DESC 
            LIMIT ?
        ";
        
        $stmt3 = $conn->prepare($sql3);
        if ($stmt3) {
            $types = str_repeat('i', count($existing_ids)) . 'i';
            $params = array_merge($existing_ids, [$need]);
            $stmt3->bind_param($types, ...$params);
            if ($stmt3->execute()) {
                $extra = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
                $recommendations = array_merge($recommendations, $extra);
            }
            $stmt3->close();
        }
    }
    
    return $recommendations;
}
?>