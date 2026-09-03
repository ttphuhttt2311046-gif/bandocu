<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bandocu";
//$port = 3306; // Đã đổi từ 3307 về 3306

header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "img-src 'self' data: blob:; "
    . "media-src 'self' data: blob:; "
    . "script-src 'self' 'unsafe-inline'; "
    . "style-src 'self' 'unsafe-inline';"
);
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Buộc hủy session seller ngay khi tài khoản đã bị Admin khóa.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    isset($_SESSION['user_id'])
    && ($_SESSION['vaitro'] ?? '') !== 'admin'
) {
    $sessionUserId = (int)$_SESSION['user_id'];
    $statusStmt = $conn->prepare(
        "SELECT trangThai FROM taikhoan WHERE maTaiKhoan = ? LIMIT 1"
    );

    if ($statusStmt) {
        $statusStmt->bind_param("i", $sessionUserId);
        $statusStmt->execute();
        $statusRow = $statusStmt->get_result()->fetch_assoc();
        $statusStmt->close();

        if (!$statusRow || (int)$statusRow['trangThai'] === 0) {
            $_SESSION = [];
            session_destroy();
            header("Location: /bandocu2/admin/login.php?locked=1");
            exit;
        }
    }
}
?>