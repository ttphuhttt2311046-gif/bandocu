<?php
session_start();
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:;");
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$addressError = null;
$addressSuccess = null;

$stmt = $conn->prepare("SELECT diaChi FROM taikhoan WHERE maTaiKhoan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $diaChi = trim($_POST["diaChi"] ?? "");

    if ($diaChi === "") {
        $addressError = "Vui lòng nhập địa chỉ.";
    } else {
        $update = $conn->prepare("UPDATE taikhoan SET diaChi = ? WHERE maTaiKhoan = ?");
        $update->bind_param("si", $diaChi, $id);

        if ($update->execute()) {
            $_SESSION['diaChi'] = $diaChi;
            $addressSuccess = "Cập nhật địa chỉ thành công.";
        } else {
            $addressError = "Lỗi khi cập nhật địa chỉ.";
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Địa chỉ</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f5f6f8;
    color: #000;
    font-family: Arial, sans-serif;
}

.cakhoi {
    width: 100%;
    max-width: 450px;
    padding: 30px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.h2 {
    margin: 0 0 25px;
    color: #000;
    font-size: 28px;
    font-weight: 600;
    text-align: center;
}

.mk {
    margin-bottom: 18px;
    color: #000;
}

.mk label {
    display: block;
    margin-bottom: 7px;
    font-weight: 600;
}

.mk input {
    width: 100%;
    height: 42px;
    padding: 9px 12px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    outline: none;
    color: #000;
    background: #fff;
    font-size: 16px;
}

.mk input:focus {
    border-color: #777;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
}

.dangki {
    display: flex;
    gap: 10px;
    margin-top: 22px;
}

.dangki button {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #222;
    border-radius: 7px;
    background: #222;
    color: #fff;
    cursor: pointer;
    font-size: 15px;
}

.dangki button:last-child {
    background: #fff;
    color: #222;
}

.dangki button:hover {
    opacity: 0.75;
}

@media (max-width: 480px) {
    .cakhoi {
        margin: 15px;
        padding: 24px 20px;
    }
}
</style>
</head>
<body>
<div class="cakhoi">
<h2 class="h2">Địa chỉ</h2>

<form method="post">
    <div class="mk">
        <label for="diaChi">Địa chỉ:</label>
        <input type="text" id="diaChi" name="diaChi" required value="<?php echo htmlspecialchars($user['diaChi'] ?? $_SESSION['diaChi'] ?? ''); ?>">
    </div>

    <div class="dangki">
        <button type="submit">Lưu địa chỉ</button>
        <button type="button" onclick="window.location='../index.php'">Quay lại</button>
    </div>

    <?php if ($addressError !== null): ?>
        <p style="color:red; text-align:center;"><?php echo htmlspecialchars($addressError); ?></p>
    <?php endif; ?>
    <?php if ($addressSuccess !== null): ?>
        <p style="color:green; text-align:center;"><?php echo htmlspecialchars($addressSuccess); ?></p>
    <?php endif; ?>
</form>
</div>
</body>
</html>