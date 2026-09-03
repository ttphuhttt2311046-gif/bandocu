<?php

include __DIR__ . "/../ai_recommend.php";

function getRecommendationForProduct($conn, $product, $user_id = null, $limit = 12) {
    
    $current_id = $product['maSanPham'] ?? 0;
    $current_category = $product['maDanhMuc'] ?? 0;
    
    $recommendations = [];

    // ================= 1. XỬ LÝ AI GỢI Ý =================
    if ($user_id) {
        // Lấy dư ra (ví dụ 30) để bù trừ những sản phẩm bị AI ném sai danh mục
        $aiProducts = getAIRecommendations($conn, $user_id, 30); 
        
        if (!empty($aiProducts)) {
            foreach ($aiProducts as $ai) {
                // ÉP BUỘC: Chỉ giữ lại sản phẩm AI gợi ý CÙNG DANH MỤC
                // và phải khác với sản phẩm đang xem
                if (isset($ai['maDanhMuc']) && $ai['maDanhMuc'] == $current_category && $ai['maSanPham'] != $current_id) {
                    $recommendations[] = $ai;
                }
            }
            // Cắt đúng giới hạn $limit
            $recommendations = array_slice($recommendations, 0, $limit);
        }
    }

    // ================= 2. BỔ SUNG BẰNG SQL (ÉP CÙNG DANH MỤC) =================
    // Nếu AI tắt, hoặc AI lọc xong vẫn chưa đủ 12 cái -> Tìm bằng SQL
    if (count($recommendations) < $limit) {
        $need = $limit - count($recommendations);
        $search_term = trim($product['tenSanPham'] . " " . ($product['moTa'] ?? ''));
        
        // Gom các ID đã có (bao gồm sản phẩm đang xem) để không bị trùng lặp
        $existing_ids = array_column($recommendations, 'maSanPham');
        $existing_ids[] = $current_id; 
        
        $placeholders = str_repeat('?,', count($existing_ids) - 1) . '?';
        
        // QUAN TRỌNG: Đưa maDanhMuc thẳng vào WHERE
        $sql = "
            SELECT *, 
                   MATCH(tenSanPham, moTa) AGAINST(? IN NATURAL LANGUAGE MODE) as score
            FROM sanpham 
            WHERE maDanhMuc = ? 
              AND maSanPham NOT IN ($placeholders)
            ORDER BY score DESC, luotXem DESC
            LIMIT ?
        ";
        
        $stmt = $conn->prepare($sql);
        
        // Cấu hình chuỗi Types: s (string), i (int)
        $types = "si" . str_repeat('i', count($existing_ids)) . "i";
        $params = array_merge([$search_term, $current_category], $existing_ids, [$need]);
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $sql_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $recommendations = array_merge($recommendations, $sql_results);
    }

    // ================= 3. FALLBACK CUỐI CÙNG (NẾU WEB QUÁ ÍT SẢN PHẨM) =================
    // Ví dụ danh mục 'Máy ảnh' của bạn chỉ có tổng cộng 5 món, mà limit là 12.
    // Lấy thêm hàng Top Trending của hệ thống đắp vào cho đủ.
    if (count($recommendations) < $limit) {
        $need = $limit - count($recommendations);
        
        $existing_ids = array_column($recommendations, 'maSanPham');
        $existing_ids[] = $current_id;
        
        $placeholders = str_repeat('?,', count($existing_ids) - 1) . '?';
        
        $sql2 = "SELECT * FROM sanpham 
                 WHERE maSanPham NOT IN ($placeholders) 
                 ORDER BY luotXem DESC LIMIT ?";
        
        $stmt2 = $conn->prepare($sql2);
        $types = str_repeat('i', count($existing_ids)) . 'i';
        $params = array_merge($existing_ids, [$need]);
        $stmt2->bind_param($types, ...$params);
        $stmt2->execute();
        $extra = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $recommendations = array_merge($recommendations, $extra);
    }

    return $recommendations;
}
?>