<?php
session_start();
include("../db.php");
require_once '../admin/validation.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tenDangNhap = clean($_POST['email']);  // Form nhập là email
    $matKhau = clean($_POST['password']);
    if (!validRequired($tenDangNhap)) {
    exit("Vui lòng nhập tài khoản");
    }

    if (!validRequired($matKhau)) {
    exit("Vui lòng nhập mật khẩu");
    }
    // Truy vấn kiểm tra tài khoản
$stmt = $conn->prepare("SELECT * FROM taikhoan WHERE tenDangNhap = ?");
$stmt->bind_param("s", $tenDangNhap);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $taikhoan = $result->fetch_assoc();

    // 🔒 Kiểm tra tài khoản bị khóa
    if ($taikhoan['trangThai'] == 0) {
        echo "<script>alert('Tài khoản của bạn đã bị khóa!'); window.location='login.php';</script>";
        exit;
    }

    // Kiểm tra mật khẩu đã hash
$loginSuccess = false;

if (password_verify($matKhau, $taikhoan['matKhau'])) {
    $loginSuccess = true;
}

// Hỗ trợ tài khoản cũ lưu password text thường
elseif ($matKhau === $taikhoan['matKhau']) {

    $loginSuccess = true;

    // Tự động hash lại password cũ
    $newHash = password_hash($matKhau, PASSWORD_BCRYPT, [
        'cost' => 12
    ]);

    $update = $conn->prepare(
        "UPDATE taikhoan SET matKhau = ? WHERE maTaiKhoan = ?"
    );

    $update->bind_param(
        "si",
        $newHash,
        $taikhoan['maTaiKhoan']
    );

    $update->execute();
}

if ($loginSuccess) {

    session_regenerate_id(true);

    $_SESSION['tenNguoiDung'] = $taikhoan['tenNguoiDung'];
    $_SESSION['user_id'] = $taikhoan['maTaiKhoan'];
    $_SESSION['email'] = $taikhoan['tenDangNhap'];
    $_SESSION['vaitro'] = $taikhoan['vaitro'];
    $_SESSION['csrf_token'] =
    bin2hex(random_bytes(32));
    $_SESSION['login_success'] = true;

    if ($taikhoan['vaitro'] === 'admin') {
        header("Location: dashboard.php");
    }
    elseif ($taikhoan['vaitro'] === 'seller') {
        header("Location: ../index.php");
    }
    else {
        header("Location: ../index.php");
    }

    exit;
}
else {
    echo "<script>alert('Sai mật khẩu!');</script>";
}

} else {
    echo "<script>alert('Không tìm thấy tài khoản!');</script>";
}

$stmt->close();

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
   
    <div class="cakhoi">
        <div class="h2">ĐĂNG NHẬP</div>

        <form method="post">
            <div class="mk">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="mk">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required>
            </div>

            <div class="dangki">
                <button type="submit">Đăng Nhập</button>

                <button type="button"
                        onclick="window.location.href='register.php'">
                    Đăng ký
                </button>
            </div>

            <div class="cadkdn">
                <a href="forgot_password.php">Quên mật khẩu?</a>
            </div>
        </form>
    </div>
</body>
</html>
