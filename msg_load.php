<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) exit;

$me = (int)$_SESSION['user_id'];
$to = (int)($_GET['to'] ?? 0);
if ($to <= 0) exit;

/* LẤY TIN NHẮN */
$stmt = $conn->prepare("
    SELECT * FROM nhantin
    WHERE
      (maNguoiGui = ? AND maNguoiNhan = ?)
      OR
      (maNguoiGui = ? AND maNguoiNhan = ?)
    ORDER BY ngayGui ASC
");
$stmt->bind_param("iiii", $me, $to, $to, $me);
$stmt->execute();
$res = $stmt->get_result();

/* HIỂN THỊ */
while ($m = $res->fetch_assoc()) {

    $isMe = $m['maNguoiGui'] == $me;
    $cls  = $isMe ? 'msg-me' : 'msg-other';
    $msg  = $m['noiDung'];

    echo "<div class='$cls'>";

    /* ===== NẾU LÀ HÓA ĐƠN ===== */
    // if (strpos($msg, 'Hóa đơn #') !== false) {

    //     // lấy id đơn
    //     preg_match('/#(\d+)/', $msg, $match);
    //     $idDon = $match[1] ?? 0;

    //     $link = "uploads/invoices/hoadon_$idDon.pdf";

    //     echo "
    //     <div style='background:#fff3e0;padding:10px;border-radius:10px;'>
    //         📄 <b>Hóa đơn #$idDon</b><br>
    //         <a href='$link' target='_blank' style='color:#ff5722;font-weight:bold;'>
    //             📥 Tải PDF
    //         </a>
    //     </div>
    //     ";
 if (strpos($msg, 'Hóa đơn #') !== false) {

        // lấy id đơn
        preg_match('/#(\d+)/', $msg, $match);
        $idDon = intval($match[1] ?? 0);
        $link = htmlspecialchars("uploads/invoices/hoadon_$idDon.pdf", ENT_QUOTES, 'UTF-8');
        $idDonSafe = htmlspecialchars($idDon, ENT_QUOTES, 'UTF-8');

        echo "
        <div style='background:#fff3e0;padding:10px;border-radius:10px;'>
            📄 <b>Hóa đơn #{$idDonSafe}</b><br>
            <a href='{$link}' target='_blank' style='color:#ff5722;font-weight:bold;'>
                📥 Tải PDF
            </a>
        </div>
        ";

    } else {
        /* ===== TIN NHẮN THƯỜNG ===== */
$msg = htmlspecialchars($msg);

$msg = preg_replace(
    '/(https?:\/\/[^\s]+)/',
    '<a href="$1" target="_blank">$1</a>',
    $msg
);

echo nl2br($msg);}
    /* ===== THỜI GIAN ===== */
    echo "<span class='time'>".date("H:i", strtotime($m['ngayGui']))."</span>";

    echo "</div>";
}

/* ===== ĐÁNH DẤU ĐÃ XEM ===== */
$conn->query("
    UPDATE nhantin
    SET trangThai='da_xem'
    WHERE maNguoiNhan=$me AND maNguoiGui=$to
");