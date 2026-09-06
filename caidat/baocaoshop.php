<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../admin/login.php');
    exit;
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$userId = (int)$_SESSION['user_id'];
$shopId = isset($_POST['seller'])
    ? (int)$_POST['seller']
    : (int)($_GET['seller'] ?? 0);

if ($shopId <= 0 || $shopId === $userId) {
    exit('Shop không hợp lệ');
}

$stmt = $conn->prepare("
    SELECT maTaiKhoan, tenNguoiDung, tenDangNhap
    FROM taikhoan
    WHERE maTaiKhoan = ?
      AND vaitro = 'seller'
      AND trangThai = 1
");
$stmt->bind_param("i", $shopId);
$stmt->execute();
$shop = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$shop) {
    exit('Không tìm thấy shop');
}

$shopName = $shop['tenNguoiDung'] ?: $shop['tenDangNhap'];
$reporterName = $_SESSION['tenNguoiDung'] ?? $_SESSION['email'] ?? 'Người dùng';
$errors = [];
$success = '';

$reasons = [
    'hang_gia' => 'Kinh doanh hàng giả, hàng nhái, hàng kém chất lượng',
    'lua_dao' => 'Lừa đảo, chiếm đoạt tài sản',
    'hang_cam' => 'Đăng bán sản phẩm cấm, vi phạm pháp luật',
    'gia_mao' => 'Thông tin shop giả mạo',
    'gian_lan_spam' => 'Gian lận, spam hoặc khiếm nhã',
    'khac' => 'Khác'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrf)
    ) {
        $errors[] = 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.';
    }

    $lyDo = $_POST['lyDo'] ?? '';
    $lyDoKhac = trim($_POST['lyDoKhac'] ?? '');
    $moTa = trim($_POST['moTa'] ?? '');

    if (!array_key_exists($lyDo, $reasons)) {
        $errors[] = 'Vui lòng chọn lý do báo cáo.';
    }

    if ($lyDo === 'khac' && $lyDoKhac === '') {
        $errors[] = 'Vui lòng nhập lý do khác.';
    }

    if (mb_strlen($moTa, 'UTF-8') < 20) {
        $errors[] = 'Mô tả phải có ít nhất 20 ký tự.';
    }

    if (mb_strlen($moTa, 'UTF-8') > 5000) {
        $errors[] = 'Mô tả không được vượt quá 5000 ký tự.';
    }

    $files = [];

    if (isset($_FILES['bangChung']) && is_array($_FILES['bangChung']['name'])) {
        foreach ($_FILES['bangChung']['name'] as $index => $originalName) {
            if ($_FILES['bangChung']['error'][$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $files[] = [
                'name' => $originalName,
                'tmp' => $_FILES['bangChung']['tmp_name'][$index],
                'size' => (int)$_FILES['bangChung']['size'][$index],
                'error' => (int)$_FILES['bangChung']['error'][$index]
            ];
        }
    }

    if (count($files) > 5) {
        $errors[] = 'Chỉ được tải tối đa 5 file.';
    }

    $allowedMime = [
        'image/jpeg' => ['type' => 'image', 'ext' => 'jpg'],
        'image/png' => ['type' => 'image', 'ext' => 'png'],
        'image/webp' => ['type' => 'image', 'ext' => 'webp'],
        'video/mp4' => ['type' => 'video', 'ext' => 'mp4'],
        'video/webm' => ['type' => 'video', 'ext' => 'webm']
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    foreach ($files as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Có file tải lên bị lỗi.';
            continue;
        }

        if (!is_uploaded_file($file['tmp'])) {
            $errors[] = 'File tải lên không hợp lệ.';
            continue;
        }

        $maxSize = str_starts_with($finfo->file($file['tmp']), 'video/')
            ? 50 * 1024 * 1024
            : 5 * 1024 * 1024;

        if ($file['size'] <= 0 || $file['size'] > $maxSize) {
            $errors[] = 'Kích thước file vượt quá giới hạn cho phép.';
        }

        $mime = $finfo->file($file['tmp']);

        if (!isset($allowedMime[$mime])) {
            $errors[] = 'Chỉ chấp nhận JPG, PNG, WEBP, MP4 hoặc WEBM.';
        }
    }

    if (!$errors) {
        $uploadDir = __DIR__ . '/../assets/uploads/bao_cao_shop/';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $errors[] = 'Không thể tạo thư mục lưu bằng chứng.';
        }
    }

    if (!$errors) {
        $savedFiles = [];

        try {
            $conn->begin_transaction();

            $emptyEvidence = '[]';

            $stmt = $conn->prepare("
                INSERT INTO baocao_shop
                (
                    maShop,
                    maNguoiBaoCao,
                    tenShop,
                    tenNguoiBaoCao,
                    lyDo,
                    lyDoKhac,
                    moTa,
                    bangChung
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iissssss",
                $shopId,
                $userId,
                $shopName,
                $reporterName,
                $lyDo,
                $lyDoKhac,
                $moTa,
                $emptyEvidence
            );

            if (!$stmt->execute()) {
                throw new Exception('Không thể lưu báo cáo.');
            }

            $reportId = $stmt->insert_id;
            $stmt->close();
            $savedFilesData = [];
            foreach ($files as $file) {
                $mime = $finfo->file($file['tmp']);
                $fileInfo = $allowedMime[$mime];

                $randomName = bin2hex(random_bytes(16));
                $savedName = $randomName . '.' . $fileInfo['ext'];
                $absolutePath = $uploadDir . $savedName;
                $relativePath = 'assets/uploads/bao_cao_shop/' . $savedName;

                if (!move_uploaded_file($file['tmp'], $absolutePath)) {
                    throw new Exception('Không thể lưu file bằng chứng.');
                }

                $savedFiles[] = $absolutePath;

                $savedFilesData[] = [
                    'tenGoc' => basename($file['name']),
                    'tenLuu' => $savedName,
                    'duongDan' => $relativePath,
                    'loai' => $fileInfo['type'],
                    'mime' => $mime,
                    'kichThuoc' => $file['size']
                ];
            }

            $evidenceJson = json_encode(
                $savedFilesData ?? [],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            $stmt = $conn->prepare("
                UPDATE baocao_shop
                SET bangChung = ?
                WHERE maBaoCao = ?
            ");
            $stmt->bind_param("si", $evidenceJson, $reportId);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            header('Location: baocaoshop.php?seller=' . $shopId . '&sent=1');
            exit;
        } catch (Throwable $exception) {
            $conn->rollback();

            foreach ($savedFiles as $savedFile) {
                if (is_file($savedFile)) {
                    unlink($savedFile);
                }
            }

            $errors[] = 'Gửi báo cáo thất bại. Vui lòng thử lại.';
        }
    }
}

if (isset($_GET['sent']) && $_GET['sent'] === '1') {
    $success = 'Báo cáo đã được gửi. Admin sẽ kiểm tra trong thời gian sớm nhất.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Báo cáo shop</title>
    <link rel="stylesheet" href="../admin/css/baocao.css">
</head>
<body class="report-page report-page-shop">
<div class="report-box">
    <h2>Báo cáo shop: <?= e($shopName) ?></h2>

    <?php if ($errors): ?>
        <div class="error">
            <?= e(implode(' ', $errors)) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="seller" value="<?= (int)$shopId ?>">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
        >

        <label for="lyDo">Lý do báo cáo</label>
        <select name="lyDo" id="lyDo" required>
            <option value="">-- Chọn lý do --</option>
            <?php foreach ($reasons as $value => $label): ?>
                <option
                    value="<?= e($value) ?>"
                    <?= (($_POST['lyDo'] ?? '') === $value) ? 'selected' : '' ?>
                >
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="lyDoKhac">Lý do khác</label>
        <input
            type="text"
            id="lyDoKhac"
            name="lyDoKhac"
            maxlength="255"
            value="<?= e($_POST['lyDoKhac'] ?? '') ?>"
            placeholder="Nhập nếu chọn Khác"
            disabled
        >

        <label for="moTa">Mô tả chi tiết</label>
        <textarea
            id="moTa"
            name="moTa"
            required
            minlength="20"
            maxlength="5000"
            placeholder="Mô tả sự việc cụ thể để Admin kiểm tra..."
        ><?= e($_POST['moTa'] ?? '') ?></textarea>

<label>Bằng chứng đính kèm</label>

<div id="preview-container" class="preview-container">
    <?php for ($i = 0; $i < 5; $i++): ?>
        <div class="preview-box" data-index="<?= $i ?>">
            <input
                type="file"
                name="bangChung[]"
                class="evidence-input"
                accept="image/jpeg,image/png,image/webp,video/mp4,video/webm"
            >

            <span class="plus">+</span>
        </div>
    <?php endfor; ?>
</div>

<div class="note">
    Tối đa 5 file. Ảnh tối đa 5MB, video tối đa 50MB.
</div>

        <div class="actions">
            <button type="submit">Gửi báo cáo</button>
            <a class="cancel" href="../shop.php?id=<?= (int)$shopId ?>">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
const lyDo = document.getElementById('lyDo');
const lyDoKhac = document.getElementById('lyDoKhac');

function toggleLyDoKhac() {
    const isOther = lyDo.value === 'khac';

    lyDoKhac.disabled = !isOther;
    lyDoKhac.required = isOther;

    if (!isOther) {
        lyDoKhac.value = '';
    }
}

lyDo.addEventListener('change', toggleLyDoKhac);
toggleLyDoKhac();

const previewBoxes = document.querySelectorAll('.preview-box');

previewBoxes.forEach(function (box) {
    const input = box.querySelector('.evidence-input');

    input.addEventListener('change', function () {
        const file = this.files[0];

        box.querySelector('img, video')?.remove();
        box.querySelector('.plus')?.remove();

        if (!file) {
            const plus = document.createElement('span');
            plus.className = 'plus';
            plus.textContent = '+';
            box.appendChild(plus);
            return;
        }

        const fileUrl = URL.createObjectURL(file);

        if (file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.src = fileUrl;
            image.alt = 'Bằng chứng hình ảnh';
            box.appendChild(image);
        } else if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = fileUrl;
            video.controls = true;
            box.appendChild(video);
        }
    });
});
</script>
</body>
</html>