<?php

function getAIRecommendations($conn, $userId, $limit = 8)
{
    $recommendations = [];
    $python = "python"; 
    
    $script = __DIR__ . "/machine_learning/predict.py";
    $command = "\"$python\" \"$script\" $userId";

    $output = shell_exec($command);

    if ($output) {
        $data = json_decode($output, true);

        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $maSanPham = intval($item['maSanPham']);

                // 🔹 ĐÃ THÊM: AND soLuong > 0 để chặn sản phẩm hết hàng từ AI
                $stmt = $conn->prepare("
                    SELECT *
                    FROM sanpham
                    WHERE maSanPham = ?
                      AND trangThai = 1
                      AND duyetTrangThai = 1
                      AND soLuong > 0
                ");

                $stmt->bind_param("i", $maSanPham);
                $stmt->execute();

                $sp = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($sp) {
                    $sp['ai_score'] = $item['score'];
                    $recommendations[] = $sp;
                }

                if (count($recommendations) >= $limit) {
                    break;
                }
            }
        }
    }

    if (count($recommendations) < $limit) {
        $need = $limit - count($recommendations);
        $existing_ids = array_column($recommendations, 'maSanPham');
        $not_in_sql = "";
        
        if (!empty($existing_ids)) {
            $ids_string = implode(",", $existing_ids);
            $not_in_sql = "AND sp.maSanPham NOT IN ($ids_string)";
        }

        // 🔹 ĐÃ THÊM: AND sp.soLuong > 0 để chặn sản phẩm hết hàng từ Fallback đắp thêm
        $sql_fallback = "
            SELECT sp.*, IFNULL(AVG(dg.soSao), 5) AS saoTB
            FROM sanpham sp
            LEFT JOIN danhgia dg ON sp.maSanPham = dg.maSanPham
            WHERE sp.trangThai = 1 
              AND sp.duyetTrangThai = 1 
              AND sp.soLuong > 0
              $not_in_sql
            GROUP BY sp.maSanPham
            ORDER BY sp.maSanPham DESC
            LIMIT $need
        ";
        
        $res = $conn->query($sql_fallback);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['ai_score'] = 0;
                $recommendations[] = $row;
            }
        }
    }

    return $recommendations;
}
?>