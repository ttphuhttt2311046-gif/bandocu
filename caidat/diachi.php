<?php
session_start();
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:;");
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

$id = (int) $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$addressError = null;

function redirectWithMessage(string $message): void
{
    header('Location: diachi.php?message=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $tenNguoiNhan = trim($_POST['tenNguoiNhan'] ?? '');
        $soDienThoai = trim($_POST['soDienThoai'] ?? '');
        $diaChi = trim($_POST['diaChi'] ?? '');
        $loaiDiaChi = $_POST['loaiDiaChi'] ?? 'Nha Rieng';
        $macDinh = isset($_POST['macDinh']) ? 1 : 0;

        if ($tenNguoiNhan === '' || $soDienThoai === '' || $diaChi === '') {
            $addressError = 'Vui lòng nhập đầy đủ thông tin địa chỉ.';
        } elseif (!in_array($loaiDiaChi, ['Nha Rieng', 'Van Phong'], true)) {
            $addressError = 'Loại địa chỉ không hợp lệ.';
        } else {
            $conn->begin_transaction();

            try {
                if ($macDinh === 1) {
                    $reset = $conn->prepare(
                        'UPDATE diachi SET macDinh = 0 WHERE maTaiKhoan = ?'
                    );
                    $reset->bind_param('i', $id);
                    $reset->execute();
                    $reset->close();
                }

                $insert = $conn->prepare(
                    'INSERT INTO diachi
                    (maTaiKhoan, tenNguoiNhan, soDienThoai, diaChi, loaiDiaChi, macDinh)
                    VALUES (?, ?, ?, ?, ?, ?)'
                );
                $insert->bind_param(
                    'issssi',
                    $id,
                    $tenNguoiNhan,
                    $soDienThoai,
                    $diaChi,
                    $loaiDiaChi,
                    $macDinh
                );
                $insert->execute();
                $insert->close();

                if ($macDinh === 1) {
                    $sync = $conn->prepare(
                        'UPDATE taikhoan SET diaChi = ? WHERE maTaiKhoan = ?'
                    );
                    $sync->bind_param('si', $diaChi, $id);
                    $sync->execute();
                    $sync->close();
                    $_SESSION['diaChi'] = $diaChi;
                }

                $conn->commit();
                redirectWithMessage('Đã thêm địa chỉ thành công.');
            } catch (Throwable $e) {
                $conn->rollback();
                $addressError = 'Không thể lưu địa chỉ. Vui lòng thử lại.';
            }
        }
    } elseif ($action === 'set_default') {
        $maDiaChi = (int) ($_POST['maDiaChi'] ?? 0);
        $conn->begin_transaction();

        try {
            $selected = $conn->prepare(
                'SELECT diaChi FROM diachi WHERE maDiaChi = ? AND maTaiKhoan = ? LIMIT 1'
            );
            $selected->bind_param('ii', $maDiaChi, $id);
            $selected->execute();
            $selectedAddress = $selected->get_result()->fetch_assoc();
            $selected->close();

            if (!$selectedAddress) {
                throw new RuntimeException('Địa chỉ không tồn tại.');
            }

            $reset = $conn->prepare(
                'UPDATE diachi SET macDinh = 0 WHERE maTaiKhoan = ?'
            );
            $reset->bind_param('i', $id);
            $reset->execute();
            $reset->close();

            $setDefault = $conn->prepare(
                'UPDATE diachi SET macDinh = 1 WHERE maDiaChi = ? AND maTaiKhoan = ?'
            );
            $setDefault->bind_param('ii', $maDiaChi, $id);
            $setDefault->execute();
            $setDefault->close();

            $sync = $conn->prepare(
                'UPDATE taikhoan SET diaChi = ? WHERE maTaiKhoan = ?'
            );
            $sync->bind_param('si', $selectedAddress['diaChi'], $id);
            $sync->execute();
            $sync->close();

            $_SESSION['diaChi'] = $selectedAddress['diaChi'];
            $conn->commit();
            redirectWithMessage('Đã đặt địa chỉ mặc định.');
        } catch (Throwable $e) {
            $conn->rollback();
            $addressError = 'Không thể đặt địa chỉ mặc định.';
        }
    } elseif ($action === 'delete') {
        $maDiaChi = (int) ($_POST['maDiaChi'] ?? 0);
        $delete = $conn->prepare(
            'DELETE FROM diachi WHERE maDiaChi = ? AND maTaiKhoan = ? AND macDinh = 0'
        );
        $delete->bind_param('ii', $maDiaChi, $id);
        $delete->execute();
        $deleted = $delete->affected_rows;
        $delete->close();
        redirectWithMessage($deleted ? 'Đã xóa địa chỉ.' : 'Không thể xóa địa chỉ mặc định.');
    }
}

$stmt = $conn->prepare(
    'SELECT * FROM diachi WHERE maTaiKhoan = ? ORDER BY macDinh DESC, maDiaChi DESC'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$message = $_GET['message'] ?? '';
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
    background: #f5f6f8;
    color: #222;
    font-family: Arial, sans-serif;
}

.cakhoi {
    width: 100%;
    max-width: 720px;
    min-height: 100vh;
    margin: 0 auto;
    padding: 28px 24px 110px;
}

.page-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 28px;
}

.back-link {
    color: #35e638;
    font-size: 30px;
    line-height: 1;
    text-decoration: none;
}

.h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
}

.section-title {
    margin: 0 -24px 0;
    padding: 12px 24px;
    background: #ededed;
    color: #666;
    font-size: 15px;
}

.address-card,
.address-form {
    margin: 0 -24px 10px;
    padding: 20px 24px;
    background: #fff;
    border-bottom: 1px solid #e7e7e7;
}

.address-name {
    margin: 0 0 8px;
    font-size: 18px;
    font-weight: 700;
}

.address-phone {
    margin-left: 12px;
    color: #666;
    font-weight: 400;
}

.address-text {
    margin: 0 0 12px;
    color: #666;
    line-height: 1.5;
}

.default-badge {
    display: inline-block;
    padding: 4px 8px;
    border: 1px solid #3bd51d;
    border-radius: 4px;
    color: #0bd853;
    font-size: 13px;
}

.address-actions {
    display: flex;
    gap: 10px;
    margin-top: 14px;
}

.inline-form {
    margin: 0;
}

.text-button {
    padding: 0;
    border: 0;
    background: transparent;
    color: #08de10;
    cursor: pointer;
    font-size: 14px;
}

.add-link {
    position: fixed;
    right: 24px;
    bottom: 24px;
    left: 24px;
    display: block;
    max-width: 672px;
    margin: 0 auto;
    padding: 16px;
    border: 1px solid #0ade3f;
    border-radius: 8px;
    background: #fff;
    color: #07dd2b;
    font-size: 18px;
    text-align: center;
    text-decoration: none;
}

.form-title {
    margin: 0 0 20px;
    font-size: 22px;
}

.field {
    margin-bottom: 16px;
}

.field label {
    display: block;
    margin-bottom: 7px;
    font-weight: 600;
}

.field input,
.field select {
    width: 100%;
    min-height: 44px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    color: #222;
    background: #fff;
    font-size: 16px;
}

.field input:focus,
.field select:focus {
    border-color: #0dd95f;
    outline: none;
    box-shadow: 0 0 0 3px rgba(230, 83, 53, 0.12);
}

.default-choice {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 20px 0;
}

.default-choice input {
    width: 18px;
    height: 18px;
}

.dangki {
    display: flex;
    gap: 10px;
}

.dangki button {
    flex: 1;
    padding: 12px 14px;
    border: 1px solid #03d10d;
    border-radius: 7px;
    background: #11d118;
    color: #fff;
    cursor: pointer;
    font-size: 15px;
}

.dangki button:last-child {
    background: #fff;
    color: #10dc43;
}

.notice {
    margin: 0 0 16px;
    padding: 12px;
    border-radius: 6px;
    background: #fff1ed;
    color: #0bdb1c;
    text-align: center;
}

@media (max-width: 480px) {
    .cakhoi {
        padding-right: 16px;
        padding-left: 16px;
    }

    .section-title,
    .address-card,
    .address-form {
        margin-right: -16px;
        margin-left: -16px;
        padding-right: 16px;
        padding-left: 16px;
    }

    .add-link {
        right: 16px;
        left: 16px;
    }
}
</style>
</head>
<body>
<div class="cakhoi">
<div class="page-header">
    <a class="back-link" href="../index.php" aria-label="Quay lại">&#8592;</a>
    <h1 class="h2"><?= $action === 'add' ? 'Địa chỉ mới' : 'Địa chỉ của tôi' ?></h1>
</div>

<?php if ($addressError !== null): ?>
    <p class="notice"><?= htmlspecialchars($addressError) ?></p>
<?php endif; ?>

<?php if ($action === 'add'): ?>
    <form class="address-form" method="post">
        <h2 class="form-title">Địa chỉ nhận hàng</h2>
        <input type="hidden" name="action" value="add">

        <div class="field">
            <label for="tenNguoiNhan">Họ và tên</label>
            <input type="text" id="tenNguoiNhan" name="tenNguoiNhan" required>
        </div>

        <div class="field">
            <label for="soDienThoai">Số điện thoại</label>
            <input type="tel" id="soDienThoai" name="soDienThoai" required>
        </div>

        <div class="field">
            <label for="diaChi">Địa chỉ</label>
            <input type="text" id="diaChi" name="diaChi"
                   placeholder="Tỉnh/Thành phố, Phường/Xã, Tên đường, số nhà" required>
        </div>

        <div class="field">
            <label for="loaiDiaChi">Loại địa chỉ</label>
            <select id="loaiDiaChi" name="loaiDiaChi">
                <option value="Nha Rieng">Nhà riêng</option>
                <option value="Van Phong">Văn phòng</option>
            </select>
        </div>

        <label class="default-choice">
            <input type="checkbox" name="macDinh" value="1">
            Đặt làm địa chỉ mặc định
        </label>

        <div class="dangki">
            <button type="submit">Hoàn thành</button>
            <button type="button" onclick="window.location='diachi.php'">Hủy</button>
        </div>
    </form>
<?php else: ?>
    <div class="section-title">Địa chỉ</div>

    <?php if ($message !== ''): ?>
        <p class="notice"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (empty($addresses)): ?>
        <div class="address-card">Bạn chưa có địa chỉ nhận hàng.</div>
    <?php endif; ?>

    <?php foreach ($addresses as $address): ?>
        <article class="address-card">
            <p class="address-name">
                <?= htmlspecialchars($address['tenNguoiNhan']) ?>
                <span class="address-phone">| <?= htmlspecialchars($address['soDienThoai']) ?></span>
            </p>
            <p class="address-text"><?= htmlspecialchars($address['diaChi']) ?></p>
            <?php if ((int) $address['macDinh'] === 1): ?>
                <span class="default-badge">Mặc định</span>
            <?php endif; ?>

            <div class="address-actions">
                <?php if ((int) $address['macDinh'] !== 1): ?>
                    <form class="inline-form" method="post">
                        <input type="hidden" name="action" value="set_default">
                        <input type="hidden" name="maDiaChi" value="<?= (int) $address['maDiaChi'] ?>">
                        <button class="text-button" type="submit">Đặt làm mặc định</button>
                    </form>
                    <form class="inline-form" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="maDiaChi" value="<?= (int) $address['maDiaChi'] ?>">
                        <button class="text-button" type="submit">Xóa</button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <a class="add-link" href="diachi.php?action=add">+ Thêm địa chỉ mới</a>
<?php endif; ?>
</div>
</body>
</html>