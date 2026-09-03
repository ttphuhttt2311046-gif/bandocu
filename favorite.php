<?php
session_start();
include "db.php";

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $response['message'] = "Vui lòng đăng nhập để yêu thích sản phẩm";
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$maSanPham = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = $_POST['action'] ?? '';

if ($maSanPham <= 0) {
    $response['message'] = "Sản phẩm không hợp lệ";
    echo json_encode($response);
    exit;
}

// Kiểm tra đã yêu thích chưa
$stmt = $conn->prepare("
    SELECT maYeuThich
    FROM yeuthich
    WHERE maTaiKhoan = ?
    AND maSanPham = ?
");
$stmt->bind_param("ii", $user_id, $maSanPham);
$stmt->execute();
$alreadyLiked = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($action === "toggle") {

    if ($alreadyLiked) {

        // ===================== BỎ YÊU THÍCH =====================
        $del = $conn->prepare("
            DELETE FROM yeuthich
            WHERE maTaiKhoan = ?
            AND maSanPham = ?
        ");
        $del->bind_param("ii", $user_id, $maSanPham);
        $del->execute();
        $del->close();

        // Cập nhật Machine Learning
        $updateML = $conn->prepare("
            UPDATE user_interactions
            SET yeuThich = 0
            WHERE maTaiKhoan = ?
            AND maSanPham = ?
        ");
        $updateML->bind_param("ii", $user_id, $maSanPham);
        $updateML->execute();
        $updateML->close();

        $response['liked'] = false;

    } else {

        // ===================== THÊM YÊU THÍCH =====================
        $ins = $conn->prepare("
            INSERT INTO yeuthich
            (maTaiKhoan, maSanPham, ngayYeuThich)
            VALUES (?, ?, NOW())
        ");
        $ins->bind_param("ii", $user_id, $maSanPham);
        $ins->execute();
        $ins->close();

        // ===================== MACHINE LEARNING =====================
        $stmtML = $conn->prepare("
            INSERT INTO user_interactions
            (maTaiKhoan, maSanPham, soLanXem, yeuThich, lienHe, muaHang)
            VALUES (?, ?, 0, 1, 0, 0)
            ON DUPLICATE KEY UPDATE
            yeuThich = 1
        ");

        $stmtML->bind_param("ii", $user_id, $maSanPham);
        $stmtML->execute();
        $stmtML->close();

        $response['liked'] = true;
    }

    // ===================== ĐẾM LẠI LƯỢT THÍCH =====================
    $count = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM yeuthich
        WHERE maSanPham = ?
    ");

    $count->bind_param("i", $maSanPham);
    $count->execute();

    $row = $count->get_result()->fetch_assoc();

    $response['success'] = true;
    $response['total'] = $row['total'];

    $count->close();
}

echo json_encode($response);
?>