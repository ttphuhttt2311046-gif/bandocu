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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userId = (int)$_SESSION['user_id'];
$productId = isset($_POST['product_id'])
    ? (int)$_POST['product_id']
    : (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    exit('Sản phẩm không hợp lệ');
}

$stmt = $conn->prepare("
    SELECT
        sp.maSanPham,
        sp.tenSanPham,
        sp.maNguoiBan,
        tk.tenNguoiDung,
        tk.tenDangNhap,
        tk.vaitro,
        tk.trangThai
    FROM sanpham sp
    LEFT JOIN taikhoan tk
        ON tk.maTaiKhoan = sp.maNguoiBan
    WHERE sp.maSanPham = ?
    LIMIT 1
");

$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    exit('Không tìm thấy sản phẩm');
}

$sellerId = (int)$product['maNguoiBan'];

if ($sellerId === $userId) {
    exit('Bạn không thể báo cáo sản phẩm của chính mình');
}

if (
    empty($product['tenNguoiDung'])
    && empty($product['tenDangNhap'])
) {
    exit('Không tìm thấy thông tin shop');
}

$shopName = $product['tenNguoiDung'] ?: $product['tenDangNhap'];
$productName = $product['tenSanPham'];

$reporterName = $_SESSION['tenNguoiDung']
    ?? $_SESSION['email']
    ?? 'Người dùng';

$errors = [];
$success = '';

$reasons = [
    'hang_gia' => 'Hàng giả, hàng nhái, hàng kém chất lượng',
    'lua_dao' => 'Lừa đảo, chiếm đoạt tài sản',
    'hang_cam' => 'Sản phẩm cấm hoặc vi phạm pháp luật',
    'gia_mao' => 'Thông tin shop giả mạo',
    'gian_lan_spam' => 'Gian lận, spam hoặc nội dung khiếm nhã',
    'khac' => 'Lý do khác'
];

$allowedMime = [
    'image/jpeg' => ['type' => 'image', 'ext' => 'jpg'],
    'image/png' => ['type' => 'image', 'ext' => 'png'],
    'image/webp' => ['type' => 'image', 'ext' => 'webp'],
    'video/mp4' => ['type' => 'video', 'ext' => 'mp4'],
    'video/webm' => ['type' => 'video', 'ext' => 'webm']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $csrf)
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

    if (
        isset($_FILES['bangChung'])
        && is_array($_FILES['bangChung']['name'])
    ) {
        foreach ($_FILES['bangChung']['name'] as $index => $originalName) {
            if (
                ($_FILES['bangChung']['error'][$index] ?? UPLOAD_ERR_NO_FILE)
                === UPLOAD_ERR_NO_FILE
            ) {
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

        $mime = $finfo->file($file['tmp']);

        if (!isset($allowedMime[$mime])) {
            $errors[] = 'Chỉ chấp nhận JPG, PNG, WEBP, MP4 hoặc WEBM.';
            continue;
        }

        $maxSize = str_starts_with($mime, 'video/')
            ? 50 * 1024 * 1024
            : 5 * 1024 * 1024;

        if ($file['size'] <= 0 || $file['size'] > $maxSize) {
            $errors[] = 'Kích thước file vượt quá giới hạn cho phép.';
        }
    }

    $uploadDir = __DIR__ . '/../assets/uploads/bao_cao_san_pham/';

    if (!$errors && !is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $errors[] = 'Không thể tạo thư mục lưu bằng chứng.';
        }
    }

    if (!$errors) {
        $savedFiles = [];

        try {
            $conn->begin_transaction();

            $emptyEvidence = '[]';
            $reportType = 'sanpham';

            $stmt = $conn->prepare("
                INSERT INTO baocao_shop
                (
                    loaiBaoCao,
                    maSanPham,
                    maShop,
                    maNguoiBaoCao,
                    tenShop,
                    tenNguoiBaoCao,
                    lyDo,
                    lyDoKhac,
                    moTa,
                    bangChung
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                throw new Exception('Không thể chuẩn bị câu lệnh lưu báo cáo.');
            }

            $stmt->bind_param(
                "siiissssss",
                $reportType,
                $productId,
                $sellerId,
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

                $savedName = bin2hex(random_bytes(16))
                    . '.'
                    . $fileInfo['ext'];

                $absolutePath = $uploadDir . $savedName;
                $relativePath = 'assets/uploads/bao_cao_san_pham/'
                    . $savedName;

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
                $savedFilesData,
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

            header(
                'Location: baocaosp.php?id='
                . $productId
                . '&sent=1'
            );
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
    $success = 'Báo cáo đã được gửi. Admin sẽ kiểm tra sớm nhất.';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Báo cáo sản phẩm</title>

    <link rel="stylesheet" href="../admin/css/baocao.css">
</head>

<body class="report-page report-page-product">
<div class="report-box">
    <h2>Báo cáo sản phẩm</h2>

    <div class="product-info">
        <strong>Sản phẩm:</strong>
        <?= e($productName) ?><br>

        <strong>Shop:</strong>
        <?= e($shopName) ?>
    </div>

    <?php if ($errors): ?>
        <div class="error">
            <?= e(implode(' ', $errors)) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input
            type="hidden"
            name="product_id"
            value="<?= $productId ?>"
        >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION['csrf_token']) ?>"
        >

        <label for="lyDo">Lý do báo cáo</label>

        <select name="lyDo" id="lyDo" required>
            <option value="">-- Chọn lý do --</option>

            <?php foreach ($reasons as $value => $label): ?>
                <option
                    value="<?= e($value) ?>"
                    <?= (($_POST['lyDo'] ?? '') === $value)
                        ? 'selected'
                        : '' ?>
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
            placeholder="Nhập nếu chọn lý do khác"
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

        <div class="preview-container">
            <?php for ($i = 0; $i < 5; $i++): ?>
                <div class="preview-box">
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

            <a
                class="cancel"
                href="../product.php?id=<?= $productId ?>"
            >
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
const reasonSelect = document.getElementById('lyDo');
const otherReason = document.getElementById('lyDoKhac');

function toggleOtherReason() {
    const isOther = reasonSelect.value === 'khac';

    otherReason.disabled = !isOther;
    otherReason.required = isOther;

    if (!isOther) {
        otherReason.value = '';
    }
}

reasonSelect.addEventListener('change', toggleOtherReason);
toggleOtherReason();

document.querySelectorAll('.preview-box').forEach(function (box) {
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