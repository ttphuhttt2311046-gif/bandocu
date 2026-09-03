<?php
session_start();
include "../db.php";
require_once '../admin/validation.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenNguoiDung = clean($_POST['tenNguoiDung'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = clean($_POST['password'] ?? '');
    $allowedRoles = ['buyer', 'seller'];
    $vaitro = $_POST['vaitro'] ?? '';
    if (!in_array($vaitro, $allowedRoles)) {
    exit("Vai trò không hợp lệ");
    }
    $soDienThoai = clean($_POST["soDienThoai"] ?? '');
    $diaChi = clean($_POST["diaChi"] ?? '');

    $error = [];
    if (!validUsername($tenNguoiDung)) {
    $error[] = "Tên đăng nhập không hợp lệ";
    }

    if (!validEmail($email)) {
    $error[] = "Email không hợp lệ";
    }

    if (!validPassword($password)) {
    $error[] = "Mật khẩu phải từ 8 ký tự, có ít nhất 1 chữ hoa và 1 số, 1 ký tự đặc biệt";
    }
    
    if (!validRequired($tenNguoiDung)) {
    $error[] = "Tên người dùng không được để trống";
    }

    if (!validLength($tenNguoiDung, 1, 50)) {
    $error[] = "Tên người dùng phải từ 1-50 ký tự";
    }

    if (!validPhone($soDienThoai)) {
    $error[] = "Số điện thoại không hợp lệ";
    }

    if (!validLength($diaChi, 5, 255)) {
    $error[] = "Địa chỉ không hợp lệ";
    }
    
    if (count($error) > 0) {
    // Chuyển mảng lỗi thành một đoạn văn bản, xuống dòng bằng \n
    $error_message = implode("\\n", $error);
    
    // Xuất ra đoạn Javascript hiển thị Popup Alert và quay lại trang trước
    echo "<script>
        alert('$error_message');
        window.history.back();
    </script>";
    exit; // Dừng thực thi tiếp tục code lưu database
}
    if ($email === "" || $password === "") {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } else {
        $check = $conn->prepare("SELECT * FROM taikhoan WHERE tenDangNhap = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email này đã tồn tại!";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            $stmt = $conn->prepare("INSERT INTO taikhoan (tenNguoiDung,tenDangNhap, matKhau, soDienThoai, diaChi, vaitro)
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss",$tenNguoiDung, $email, $hashed, $soDienThoai, $diaChi, $vaitro);

            if ($stmt->execute()) {
                echo "<script>alert('Đăng ký thành công! Hãy đăng nhập.'); window.location='login.php';</script>";
                exit;
            } else {
                $error = "Lỗi khi đăng ký: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>Đăng ký tài khoản</title>
  <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
  <div class="cakhoi">
    <h2 class="h2">ĐĂNG KÍ</h2>
    <form method="post" id="registerForm" novalidate>

<div class="mk">
    <label>Tên người dùng:</label>
    <input type="text" id="tenNguoiDung" name="tenNguoiDung">
    <small class="error-message" id="tenNguoiDungError"></small>
</div>

<div class="mk">
    <label>Email:</label>
    <input type="email" id="email" name="email">
    <small class="error-message" id="emailError"></small>
</div>

<div class="mk">
    <label for="password">Mật khẩu:</label>

    <input type="password"
           id="password"
           name="password"
           placeholder="Nhập mật khẩu..."
           oninput="checkPasswordRealtime()">

    <small class="error-message" id="passwordError"></small>

    <div id="password-requirements" class="requirements-box">
        <div id="req-length" class="req-item invalid">✕ Ít nhất 8 ký tự</div>
        <div id="req-uppercase" class="req-item invalid">✕ Ít nhất 1 chữ hoa</div>
        <div id="req-number" class="req-item invalid">✕ Ít nhất 1 chữ số</div>
        <div id="req-special" class="req-item invalid">✕ Ít nhất 1 ký tự đặc biệt</div>
    </div>
</div>

<div class="mk">
    <label>Số điện thoại:</label>
    <input type="text" id="soDienThoai" name="soDienThoai">
    <small class="error-message" id="phoneError"></small>
</div>

<div class="mk">
    <label>Địa chỉ:</label>
    <input type="text" id="diaChi" name="diaChi">
    <small class="error-message" id="diaChiError"></small>
</div>

<div class="banla">
    <label>Bạn là:</label>
    <select id="vaitro" name="vaitro"
            style="width:300px;height:40px;border-radius:8px;padding:5px 10px;font-size:16px;">
        <option value="buyer">Người mua</option>
        <option value="seller">Người bán</option>
    </select>
</div>

<div class="cadkdn">
    <div class="dangki">
        <button type="submit">Đăng ký</button>
    </div>

    <p style="color:white;font-size:18px;text-align:center;margin-top:30px;margin-left:15px;">
        Đã có tài khoản?
        <a href="login.php" style="color:#ffd700;font-weight:bold;">
            Đăng nhập
        </a>
    </p>
</div>

</form>
  </div>
  <script>
    function checkPasswordRealtime() {
    const passwordInput = document.getElementById('password');
    const password = passwordInput.value;

    // Lấy ra các thẻ chứa điều kiện để cập nhật trạng thái
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    // 1. Kiểm tra độ dài (>= 8 ký tự)
    if (password.length >= 8) {
        reqLength.classList.remove('invalid');
        reqLength.classList.add('valid');
        reqLength.innerHTML = '✓ Ít nhất 8 ký tự';
    } else {
        reqLength.classList.remove('valid');
        reqLength.classList.add('invalid');
        reqLength.innerHTML = '✕ Ít nhất 8 ký tự';
    }

    // 2. Kiểm tra có ít nhất 1 chữ hoa
    if (/[A-Z]/.test(password)) {
        reqUppercase.classList.remove('invalid');
        reqUppercase.classList.add('valid');
        reqUppercase.innerHTML = '✓ Ít nhất 1 chữ hoa';
    } else {
        reqUppercase.classList.remove('valid');
        reqUppercase.classList.add('invalid');
        reqUppercase.innerHTML = '✕ Ít nhất 1 chữ hoa';
    }

    // 3. Kiểm tra có ít nhất 1 chữ số
    if (/[0-9]/.test(password)) {
        reqNumber.classList.remove('invalid');
        reqNumber.classList.add('valid');
        reqNumber.innerHTML = '✓ Ít nhất 1 chữ số';
    } else {
        reqNumber.classList.remove('valid');
        reqNumber.classList.add('invalid');
        reqNumber.innerHTML = '✕ Ít nhất 1 chữ số';
    }

    // 4. Kiểm tra có ít nhất 1 ký tự đặc biệt
    if (/[^a-zA-Z0-9]/.test(password)) {
        reqSpecial.classList.remove('invalid');
        reqSpecial.classList.add('valid');
        reqSpecial.innerHTML = '✓ Ít nhất 1 ký tự đặc biệt';
    } else {
        reqSpecial.classList.remove('valid');
        reqSpecial.classList.add('invalid');
        reqSpecial.innerHTML = '✕ Ít nhất 1 ký tự đặc biệt';
    }
}
  </script>
  <script>
document.getElementById("registerForm").addEventListener("submit", function(e){

    let valid = true;

    document.querySelectorAll(".error-message").forEach(item=>{
        item.innerHTML = "";
    });

    document.querySelectorAll("input").forEach(item=>{
        item.classList.remove("input-error");
    });

    function showError(inputId,errorId,message){
        document.getElementById(errorId).innerHTML = "❌ " + message;
        document.getElementById(inputId).classList.add("input-error");
        valid = false;
    }

    if(document.getElementById("tenNguoiDung").value.trim() === ""){
        showError(
            "tenNguoiDung",
            "tenNguoiDungError",
            "Vui lòng nhập tên người dùng"
        );
    }

    const emailValue = document.getElementById("email").value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailValue === "") {
        showError(
            "email",
            "emailError",
            "Vui lòng nhập email"
        );
    } else if (!emailRegex.test(emailValue)) {
        showError(
            "email",
            "emailError",
            "Email không đúng định dạng"
        );
    }

    if(document.getElementById("password").value.trim() === ""){
        showError(
            "password",
            "passwordError",
            "Vui lòng nhập mật khẩu"
        );
    }

    if(document.getElementById("soDienThoai").value.trim() === ""){
        showError(
            "soDienThoai",
            "phoneError",
            "Vui lòng nhập số điện thoại"
        );
    }

    if(document.getElementById("diaChi").value.trim() === ""){
        showError(
            "diaChi",
            "diaChiError",
            "Vui lòng nhập địa chỉ"
        );
    }

    if(!valid){
        e.preventDefault();
    }

});
</script>
</body>
</html>
