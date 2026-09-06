<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../admin/login.php');
    exit;
}

function e($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userId = (int)$_SESSION['user_id'];
$shopAccountId = 9998;

$reasons = [
    'khong_con_nhu_cau' => 'Tôi không còn nhu cầu sử dụng tài khoản',
    'co_tai_khoan_khac' => 'Tôi đã có tài khoản khác',
    'bao_mat' => 'Tôi lo ngại về quyền riêng tư và bảo mật',
    'khong_hai_long' => 'Tôi không hài lòng với dịch vụ',
    'khac' => 'Lý do khác'
];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrfToken)
    ) {
        $error = 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.';
    }

    $reasonKey = trim((string)($_POST['lyDo'] ?? ''));
    $otherReason = trim((string)($_POST['lyDoKhac'] ?? ''));

    if ($error === '' && !array_key_exists($reasonKey, $reasons)) {
        $error = 'Vui lòng chọn lý do xóa tài khoản.';
    }

    if (
        $error === '' &&
        $reasonKey === 'khac' &&
        (
            mb_strlen($otherReason, 'UTF-8') < 5 ||
            mb_strlen($otherReason, 'UTF-8') > 1000
        )
    ) {
        $error = 'Lý do khác phải có từ 5 đến 1000 ký tự.';
    }

    if ($error === '') {
        $check = $conn->prepare("
            SELECT id
            FROM yeucauxoatk
            WHERE maTaiKhoan = ?
              AND trangThai = 'cho_xu_ly'
            LIMIT 1
        ");
        $check->bind_param('i', $userId);
        $check->execute();
        $pendingRequest = $check->get_result()->fetch_assoc();
        $check->close();

        if ($pendingRequest) {
            $error = 'Bạn đã có một yêu cầu đang chờ Admin xử lý.';
        }
    }

    if ($error === '') {
        $reasonText = $reasons[$reasonKey];
        $customReason = $reasonKey === 'khac' ? $otherReason : null;

        $adminIds = [];
        $adminResult = $conn->query("
            SELECT maTaiKhoan
            FROM taikhoan
            WHERE vaitro = 'admin'
              AND trangThai = 1
        ");

        if ($adminResult) {
            while ($admin = $adminResult->fetch_assoc()) {
                $adminIds[] = (int)$admin['maTaiKhoan'];
            }
        }

        if (count($adminIds) === 0) {
            $error = 'Hiện chưa tìm thấy Admin để tiếp nhận yêu cầu.';
        } else {
            $conn->begin_transaction();

            try {
                $insert = $conn->prepare("
                    INSERT INTO yeucauxoatk
                        (maTaiKhoan, lyDo, lyDoKhac)
                    VALUES (?, ?, ?)
                ");
                $insert->bind_param(
                    'iss',
                    $userId,
                    $reasonText,
                    $customReason
                );
                $insert->execute();
                $requestId = $insert->insert_id;
                $insert->close();

                $userStmt = $conn->prepare("
                    SELECT tenNguoiDung, tenDangNhap
                    FROM taikhoan
                    WHERE maTaiKhoan = ?
                    LIMIT 1
                ");
                $userStmt->bind_param('i', $userId);
                $userStmt->execute();
                $user = $userStmt->get_result()->fetch_assoc();
                $userStmt->close();

                $userName = $user['tenNguoiDung']
                    ?: ($user['tenDangNhap'] ?? 'Người dùng');

                $message = 'Yêu cầu xóa tài khoản #' . $requestId
                    . ' từ ' . $userName
                    . '. Lý do: ' . $reasonText;

                if ($customReason !== null) {
                    $message .= '. Chi tiết: ' . $customReason;
                }

                $notify = $conn->prepare("
                    INSERT INTO nhantin
                        (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
                    VALUES (?, ?, ?, 'chua_xem', NOW())
                ");

                foreach ($adminIds as $adminId) {
                    $senderId = $shopAccountId;
                    $notify->bind_param(
                        'sii',
                        $message,
                        $senderId,
                        $adminId
                    );
                    $notify->execute();
                }

                $notify->close();
                $conn->commit();

                $success = 'Đã gửi yêu cầu xóa tài khoản đến Admin.';
            } catch (Throwable $exception) {
                $conn->rollback();
                $error = 'Không thể gửi yêu cầu. Vui lòng thử lại sau.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yêu cầu xóa tài khoản</title>

    <style>
        :root {
            --blue: #08ce19;
            --blue-dark: #059f0d;
            --blue-light: #eaf6ff;
            --text: #1f2937;
            --border: #d8e3ec;
            --danger: #c62828;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f9fc;
            color: var(--text);
            font-family: Arial, sans-serif;
        }

        .delete-page {
            width: min(100% - 32px, 640px);
            margin: 48px auto;
        }

        .delete-card {
            background: #fff;
            border: 1px solid var(--border);
            border-top: 5px solid var(--blue);
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 8px 24px rgba(0, 91, 153, .09);
        }

        h1 {
            margin: 0 0 10px;
            color: var(--blue-dark);
            font-size: 26px;
        }

        .description {
            color: #5f6b76;
            line-height: 1.6;
        }

        .alert {
            padding: 12px 14px;
            margin: 16px 0;
            border-radius: 8px;
            line-height: 1.5;
        }

        .alert-error {
            color: var(--danger);
            background: #fff0f0;
            border: 1px solid #ffcaca;
        }

        .alert-success {
            color: #176b39;
            background: #edfff3;
            border: 1px solid #b9e8c8;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            font-weight: 600;
        }

        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            font: inherit;
        }

        select:focus,
        textarea:focus {
            outline: 2px solid rgba(8, 127, 206, .2);
            border-color: var(--blue);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .hidden {
            display: none;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 22px;
        }

        button,
        .cancel-link {
            border: 0;
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .submit-button {
            color: #fff;
            background: var(--blue);
        }

        .submit-button:hover {
            background: var(--blue-dark);
        }

        .cancel-link {
            color: #52606d;
            background: #edf1f4;
        }

        @media (max-width: 480px) {
            .delete-page {
                width: min(100% - 20px, 640px);
                margin: 20px auto;
            }

            .delete-card {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<main class="delete-page">
    <section class="delete-card">
        <h1>Yêu cầu xóa tài khoản</h1>

        <p class="description">
            Vui lòng chọn lý do. Yêu cầu sẽ được gửi đến Admin để xem xét.
            Tài khoản chưa bị xóa ngay sau khi gửi.
        </p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php else: ?>
            <form method="post" id="deleteRequestForm">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <label for="lyDo">Lý do xóa tài khoản</label>
                <select name="lyDo" id="lyDo" required>
                    <option value="">-- Chọn lý do --</option>

                    <?php foreach ($reasons as $key => $label): ?>
                        <option value="<?= e($key) ?>">
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div id="otherReasonBox" class="hidden">
                    <label for="lyDoKhac">Nhập lý do của bạn</label>
                    <textarea
                        name="lyDoKhac"
                        id="lyDoKhac"
                        maxlength="1000"
                        placeholder="Vui lòng nêu rõ lý do..."
                    ></textarea>
                </div>

                <div class="buttons">
                    <button type="submit" class="submit-button">
                        Gửi yêu cầu
                    </button>

                    <a href="../index.php" class="cancel-link">
                        Hủy
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<script>
const reasonSelect = document.getElementById('lyDo');
const otherReasonBox = document.getElementById('otherReasonBox');
const otherReason = document.getElementById('lyDoKhac');
const form = document.getElementById('deleteRequestForm');

if (reasonSelect) {
    reasonSelect.addEventListener('change', function () {
        const isOther = this.value === 'khac';

        otherReasonBox.classList.toggle('hidden', !isOther);
        otherReason.required = isOther;

        if (!isOther) {
            otherReason.value = '';
        }
    });
}

if (form) {
    form.addEventListener('submit', function (event) {
        const confirmed = window.confirm(
            'Bạn có muốn gửi yêu cầu xóa tài khoản không?'
        );

        if (!confirmed) {
            event.preventDefault();
        }
    });
}
</script>
</body>
</html>