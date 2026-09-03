<?php
session_start();
include "../db.php"; // Kết nối cơ sở dữ liệu giống login.php và register.php

// BƯỚC 1: ĐỊNH NGHĨA HÀM GỬI SMS MOCK (GHI LOG)
function sendSMSMock($phoneNumber, $message) {
    // Ghi nội dung vào file sms_log.txt trong cùng thư mục dự án để giả lập SMS
    $logEntry = "[" . date('Y-m-d H:i:s') . "] To: $phoneNumber | Msg: $message\n";
    file_put_contents('sms_log.txt', $logEntry, FILE_APPEND);
}

// BƯỚC 2: XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT GỬI YÊU CẦU
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $soDienThoai = trim($_POST['soDienThoai']);

    if (empty($email) || empty($soDienThoai)) {
        $error = "Vui lòng nhập đầy đủ Email và Số điện thoại!";
    } else {
        // Kiểm tra xem Email (tenDangNhap) và Số điện thoại có khớp với tài khoản nào không
        $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE tenDangNhap = ? AND soDienThoai = ?");
        $stmt->bind_param("ss", $email, $soDienThoai);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $taikhoan = $result->fetch_assoc();
            $maTaiKhoan = $taikhoan['maTaiKhoan'];

            // 1. Tạo mật khẩu tạm thời ngẫu nhiên (8 ký tự)
            $tempPassword = bin2hex(random_bytes(4)); 
            
            // 2. Mã hóa mật khẩu theo chuẩn BCRYPT (đồng bộ với hệ thống )
            $newPass = password_hash($tempPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            // 3. Cập nhật mật khẩu mới đã mã hóa vào Cơ sở dữ liệu
            $updateStmt = $conn->prepare("UPDATE taikhoan SET matKhau = ? WHERE maTaiKhoan = ?");
            $updateStmt->bind_param("si", $newPass, $maTaiKhoan);
            
            if ($updateStmt->execute()) {
                // 4. Gọi hàm giả lập gửi SMS nội dung mật khẩu chưa mã hóa để user biết
                $smsMessage = "Mat khau tam thoi cua ban la: " . $tempPassword;
                sendSMSMock($soDienThoai, $smsMessage);

                $success = "Đặt lại mật khẩu thành công! Kiểm tra file 'sms_log.txt'.";
            } else {
                $error = "Có lỗi xảy ra trong quá trình cập nhật mật khẩu!";
            }
            $updateStmt->close();
        } else {
            $error = "Thông tin Email hoặc Số điện thoại không chính xác!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../assets/css/login.css"> 
</head>
<body>
    <div class="cakhoi">
        <div class="h2">QUÊN MẬT KHẨU</div>
        <form method="post" action="forgot_password.php">
            
            <div class="mk">
                <label>Email đăng ký:</label>
                <input type="email" name="email" required placeholder="Nhập email tài khoản">
            </div>

            <div class="mk">
                <label>Số điện thoại:</label>
                <input type="text" name="soDienThoai" required placeholder="Nhập số điện thoại đăng ký">
            </div>

            <div class="dangki">
                <button type="submit">Gửi mật khẩu mới</button>
                <button type="button" onclick="window.location.href='login.php'">
                    Quay lại đăng nhập
                </button>
            </div>

            <?php if (!empty($error)): ?>
                <p style="color: #ffffff; text-align: center; margin-top: 20px; font-weight: bold; font-size: 15px;">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p style="color: #00ff33; text-align: center; margin-top: 20px; font-weight: bold; font-size: 15px; line-height: 1.4;">
                    <?= htmlspecialchars($success) ?>
                </p>
            <?php endif; ?>

        </form>
    </div>
</body>
</html>