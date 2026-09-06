<?php
session_start();
include "../db.php";

if (($_SESSION['vaitro'] ?? '') !== 'admin') {
    exit('<div class="alert alert-danger">Bạn không có quyền truy cập.</div>');
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function reasonText($reason)
{
    return [
        'hang_gia' => 'Hàng giả, hàng nhái, hàng kém chất lượng',
        'lua_dao' => 'Lừa đảo, chiếm đoạt tài sản',
        'hang_cam' => 'Hàng cấm, vi phạm pháp luật',
        'gia_mao' => 'Thông tin giả mạo',
        'gian_lan_spam' => 'Gian lận, spam hoặc khiếm nhã',
        'khac' => 'Khác'
    ][$reason] ?? $reason;
}

$statuses = [
    'cho_xu_ly' => 'Chờ xử lý',
    'dang_xu_ly' => 'Đang xử lý',
    'da_xac_minh' => 'Đã xác minh',
    'tu_choi' => 'Từ chối',
    'da_dong' => 'Đã đóng'
];

/*
|--------------------------------------------------------------------------
| Lấy cài đặt
|--------------------------------------------------------------------------
*/

$settings = [
    'soLanBaoCaoShopTheoDoi' => 3,
    'soLanBaoCaoSanPhamTheoDoi' => 3,
    'report_watch_enabled' => 1,
    'thongBaoViPham' => json_encode([
        'bi_to_cao' => [
            'cho_xu_ly' => 'Báo cáo liên quan đến tài khoản của bạn đã được tiếp nhận và đang chờ xử lý.',
            'dang_xu_ly' => 'Báo cáo liên quan đến tài khoản của bạn đang được Admin xem xét.',
            'da_xac_minh' => 'Báo cáo liên quan đến tài khoản của bạn đã được Admin xác minh.',
            'tu_choi' => 'Báo cáo liên quan đến tài khoản của bạn đã được Admin từ chối.',
            'da_dong' => 'Vụ việc liên quan đến tài khoản của bạn đã được đóng.'
        ],
        'nguoi_to_cao' => [
            'cho_xu_ly' => 'Báo cáo của bạn đã được tiếp nhận và đang chờ xử lý.',
            'dang_xu_ly' => 'Báo cáo của bạn đang được Admin xem xét.',
            'da_xac_minh' => 'Báo cáo của bạn đã được Admin xác minh.',
            'tu_choi' => 'Báo cáo của bạn đã được Admin từ chối.',
            'da_dong' => 'Báo cáo của bạn đã được đóng.'
        ]
    ], JSON_UNESCAPED_UNICODE)
];
$defaultMessageJson = $settings['thongBaoViPham'];

$settingsResult = $conn->query("
    SELECT
        soLanBaoCaoShopTheoDoi,
        soLanBaoCaoSanPhamTheoDoi,
        report_watch_enabled,
        thongBaoViPham
    FROM duyet_settings
    WHERE id = 1
    LIMIT 1
");

if ($settingsResult && $settingsResult->num_rows > 0) {
    $settings = array_merge(
        $settings,
        $settingsResult->fetch_assoc()
    );
}

if (!is_string($settings['thongBaoViPham'])
    || !is_array(json_decode($settings['thongBaoViPham'], true))) {
    $settings['thongBaoViPham'] = $defaultMessageJson;
}

/*
|--------------------------------------------------------------------------
| Lưu cài đặt
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['save_report_settings'])
) {
    $shopThreshold = max(
        1,
        min(1000, (int)($_POST['soLanBaoCaoShopTheoDoi'] ?? 3))
    );

    $productThreshold = max(
        1,
        min(1000, (int)($_POST['soLanBaoCaoSanPhamTheoDoi'] ?? 3))
    );

    $watchEnabled = isset($_POST['report_watch_enabled']) ? 1 : 0;
    $messageSettings = json_decode(
        $_POST['thongBaoViPham'] ?? '',
        true
    );

    if (!is_array($messageSettings)) {
        http_response_code(400);
        exit('Nội dung tin nhắn không hợp lệ');
    }

    foreach (['bi_to_cao', 'nguoi_to_cao'] as $recipient) {
        foreach (array_keys($statuses) as $status) {
            $message = trim(
                (string)($messageSettings[$recipient][$status] ?? '')
            );

            if ($message === '' || mb_strlen($message, 'UTF-8') > 1000) {
                http_response_code(400);
                exit('Mỗi tin nhắn phải có từ 1 đến 1000 ký tự');
            }

            $messageSettings[$recipient][$status] = $message;
        }
    }

    $messageJson = json_encode(
        $messageSettings,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    $stmt = $conn->prepare("
        UPDATE duyet_settings
        SET
            soLanBaoCaoShopTheoDoi = ?,
            soLanBaoCaoSanPhamTheoDoi = ?,
            report_watch_enabled = ?,
            thongBaoViPham = ?
        WHERE id = 1
    ");

    if (!$stmt) {
        http_response_code(500);
        exit('Lỗi prepare SQL: ' . $conn->error);
    }

    $stmt->bind_param(
        "iiis",
        $shopThreshold,
        $productThreshold,
        $watchEnabled,
        $messageJson
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        exit('Không thể lưu cài đặt: ' . $stmt->error);
    }

    $stmt->close();
    exit('OK');
}

/*
|--------------------------------------------------------------------------
| Ẩn sản phẩm hoặc khóa tài khoản
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['watch_action'])
) {
    $action = $_POST['watch_action'];
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        exit('ID không hợp lệ');
    }

    if ($action === 'hide_product') {
        $stmt = $conn->prepare("
            UPDATE sanpham
            SET trangThai = 0
            WHERE maSanPham = ?
        ");

        if (!$stmt) {
            http_response_code(500);
            exit('Lỗi prepare SQL: ' . $conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            exit('Không thể ẩn sản phẩm: ' . $stmt->error);
        }

        $stmt->close();
        exit('OK');
    }

    if ($action === 'lock_account') {
        $stmt = $conn->prepare("
            UPDATE taikhoan
            SET trangThai = 0
            WHERE maTaiKhoan = ?
              AND vaitro = 'seller'
        ");

        if (!$stmt) {
            http_response_code(500);
            exit('Lỗi prepare SQL: ' . $conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            exit('Không thể khóa tài khoản: ' . $stmt->error);
        }

        $stmt->close();
        exit('OK');
    }

    if ($action === 'unlock_account') {
        $stmt = $conn->prepare("
            UPDATE taikhoan
            SET trangThai = 1
            WHERE maTaiKhoan = ?
              AND vaitro = 'seller'
        ");

        if (!$stmt) {
            http_response_code(500);
            exit('Lỗi prepare SQL: ' . $conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            exit('Không thể mở khóa tài khoản: ' . $stmt->error);
        }

        $stmt->close();
        exit('OK');
    }

    http_response_code(400);
    exit('Thao tác không hợp lệ');
}

/*
|--------------------------------------------------------------------------
| Cập nhật trạng thái báo cáo
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['update_statuses'])
) {
    $changes = json_decode($_POST['changes'] ?? '[]', true);

    if (!is_array($changes) || count($changes) === 0) {
        http_response_code(400);
        exit('Không có thay đổi cần lưu');
    }

    $allowedStatus = [
        'cho_xu_ly',
        'dang_xu_ly',
        'da_xac_minh',
        'tu_choi',
        'da_dong'
    ];

    $stmt = $conn->prepare("
        UPDATE baocao_shop
        SET
            trangThai = ?,
            ngayXuLy = CASE
                WHEN ? IN (
                    'da_xac_minh',
                    'tu_choi',
                    'da_dong'
                )
                THEN NOW()
                ELSE ngayXuLy
            END
        WHERE maBaoCao = ?
    ");

    if (!$stmt) {
        http_response_code(500);
        exit('Lỗi prepare SQL: ' . $conn->error);
    }

    $messageTemplates = json_decode(
        (string)$settings['thongBaoViPham'],
        true
    );

    $reportStmt = $conn->prepare(
        "SELECT maShop, maNguoiBaoCao FROM baocao_shop WHERE maBaoCao = ?"
    );

    $reportSenderId = 9998;

$messageStmt = $conn->prepare("
    INSERT INTO nhantin
        (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
    VALUES (?, ?, ?, 'chua_xem', NOW())
");

    if (!$reportStmt || !$messageStmt) {
        http_response_code(500);
        exit('Lỗi chuẩn bị gửi thông báo: ' . $conn->error);
    }

    $conn->begin_transaction();

    try {
        foreach ($changes as $change) {
            $reportId = (int)($change['id'] ?? 0);
            $status = $change['status'] ?? '';

            if (
                $reportId <= 0
                || !in_array($status, $allowedStatus, true)
            ) {
                throw new Exception('Dữ liệu trạng thái không hợp lệ');
            }

            $reportStmt->bind_param("i", $reportId);
            $reportStmt->execute();
            $reportData = $reportStmt->get_result()->fetch_assoc();

            if (!$reportData) {
                throw new Exception('Không tìm thấy báo cáo');
            }

            $stmt->bind_param(
                "ssi",
                $status,
                $status,
                $reportId
            );

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $templateData = is_array($messageTemplates)
                ? $messageTemplates
                : [];

            $replace = [
                '{maBaoCao}' => (string)$reportId,
                '{trangThai}' => $statuses[$status]
            ];

            $messages = [
                (int)$reportData['maShop'] => strtr(
                    (string)($templateData['bi_to_cao'][$status] ?? ''),
                    $replace
                ),
                (int)$reportData['maNguoiBaoCao'] => strtr(
                    (string)($templateData['nguoi_to_cao'][$status] ?? ''),
                    $replace
                )
            ];

            foreach ($messages as $receiverId => $message) {
                if ($receiverId <= 0 || trim($message) === '') {
                    continue;
                }

                $messageStmt->bind_param(
    "sii",
    $message,
    $reportSenderId,
    $receiverId
);

                if (!$messageStmt->execute()) {
                    throw new Exception($messageStmt->error);
                }
            }
        }

        $stmt->close();
        $reportStmt->close();
        $messageStmt->close();
        $conn->commit();

        exit('OK');
    } catch (Throwable $exception) {
        $stmt->close();
        $reportStmt->close();
        $messageStmt->close();
        $conn->rollback();

        http_response_code(500);
        exit('Không thể lưu thay đổi');
    }
}
/*
|--------------------------------------------------------------------------
| Lấy danh sách báo cáo shop
|--------------------------------------------------------------------------
*/

$shopReports = $conn->query("
    SELECT
        bc.*,
        shop.tenNguoiDung AS tenShopHienTai,
        nguoi.tenNguoiDung AS tenNguoiBaoHienTai
    FROM baocao_shop bc
    LEFT JOIN taikhoan shop
        ON shop.maTaiKhoan = bc.maShop
    LEFT JOIN taikhoan nguoi
        ON nguoi.maTaiKhoan = bc.maNguoiBaoCao
    WHERE bc.loaiBaoCao = 'shop'
    ORDER BY bc.ngayBaoCao DESC
");

/*
|--------------------------------------------------------------------------
| Lấy danh sách báo cáo sản phẩm
|--------------------------------------------------------------------------
*/

$productReports = $conn->query("
    SELECT
        bc.*,
        sp.tenSanPham,
        sp.hinhAnh,
        shop.tenNguoiDung AS tenShopHienTai,
        nguoi.tenNguoiDung AS tenNguoiBaoHienTai
    FROM baocao_shop bc
    LEFT JOIN sanpham sp
        ON sp.maSanPham = bc.maSanPham
    LEFT JOIN taikhoan shop
        ON shop.maTaiKhoan = bc.maShop
    LEFT JOIN taikhoan nguoi
        ON nguoi.maTaiKhoan = bc.maNguoiBaoCao
    WHERE bc.loaiBaoCao = 'sanpham'
    ORDER BY bc.ngayBaoCao DESC
");

/*
|--------------------------------------------------------------------------
| Lấy danh sách theo dõi
|--------------------------------------------------------------------------
*/

$watchShopReports = null;
$watchProductReports = null;

if ((int)$settings['report_watch_enabled'] === 1) {
    $shopThreshold = max(
        1,
        (int)$settings['soLanBaoCaoShopTheoDoi']
    );

    $productThreshold = max(
        1,
        (int)$settings['soLanBaoCaoSanPhamTheoDoi']
    );

    $watchShopReports = $conn->query("
        SELECT
            bc.maShop,
            MAX(bc.tenShop) AS tenShop,
            COUNT(*) AS soBaoCao,
            MAX(shop.trangThai) AS trangThaiShop
        FROM baocao_shop bc
        LEFT JOIN taikhoan shop
            ON shop.maTaiKhoan = bc.maShop
        WHERE bc.loaiBaoCao = 'shop'
          AND bc.trangThai = 'da_xac_minh'
        GROUP BY bc.maShop
        HAVING COUNT(*) >= {$shopThreshold}
        ORDER BY soBaoCao DESC
    ");

    $watchProductReports = $conn->query("
        SELECT
            bc.maSanPham,
            bc.maShop,
            MAX(sp.tenSanPham) AS tenSanPham,
            MAX(shop.tenNguoiDung) AS tenShop,
            COUNT(*) AS soBaoCao,
            MAX(sp.trangThai) AS trangThaiSanPham
        FROM baocao_shop bc
        LEFT JOIN sanpham sp
            ON sp.maSanPham = bc.maSanPham
        LEFT JOIN taikhoan shop
            ON shop.maTaiKhoan = bc.maShop
        WHERE bc.loaiBaoCao = 'sanpham'
          AND bc.trangThai = 'da_xac_minh'
        GROUP BY bc.maSanPham, bc.maShop
        HAVING COUNT(*) >= {$productThreshold}
        ORDER BY soBaoCao DESC
    ");
}

/*
|--------------------------------------------------------------------------
| Hiển thị bằng chứng
|--------------------------------------------------------------------------
*/

function renderEvidence($json)
{
    $files = json_decode($json ?: '[]', true);

    if (!is_array($files) || count($files) === 0) {
        return '<span class="text-muted">Không có bằng chứng</span>';
    }

    $html = '';

    foreach ($files as $file) {
        $path = $file['duongDan'] ?? '';
        $type = $file['loai'] ?? '';
        $name = $file['tenGoc'] ?? 'Bằng chứng';

        if (!$path) {
            continue;
        }

        $safePath = e('../' . ltrim($path, '/'));
        $safeName = e($name);

        if ($type === 'image') {
            $html .= '
                <a href="' . $safePath . '" target="_blank">
                    <img
                        src="' . $safePath . '"
                        alt="' . $safeName . '"
                        title="' . $safeName . '"
                        class="evidence-image">
                </a>
            ';
        } elseif ($type === 'video') {
            $html .= '
                <a
                    href="' . $safePath . '"
                    target="_blank"
                    class="btn btn-sm btn-outline-primary m-1">
                    Xem video
                </a>
            ';
        }
    }

    return $html ?: '<span class="text-muted">Không có bằng chứng</span>';
}

function renderStatusSelect($reportId, $currentStatus, $statuses)
{
    $html = '
        <select
            class="form-control form-control-sm report-status"
            data-id="' . (int)$reportId . '">
    ';

    foreach ($statuses as $value => $label) {
        $selected = $currentStatus === $value
            ? ' selected'
            : '';

        $html .= '
            <option value="' . e($value) . '"' . $selected . '>
                ' . e($label) . '
            </option>
        ';
    }

    $html .= '</select>';

    return $html;
}
?>

<link rel="stylesheet" href="../admin/css/baocao.css">

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        Quản lý vi phạm
    </h1>

    <!-- Chỉ phần điều hướng nằm trong ul -->
    <ul class="nav nav-tabs report-tabs" id="reportTabs">
        <li class="nav-item">
            <a
                class="nav-link active"
                href="#shop-reports"
                data-toggle="tab">
                Báo cáo shop
            </a>
        </li>
        
        <li class="nav-item">
            <a
                class="nav-link"
                href="#product-reports"
                data-toggle="tab">
                Báo cáo sản phẩm
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link"
                href="#watch-reports"
                data-toggle="tab">
                Theo dõi
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link"
                href="#report-settings"
                data-toggle="tab">
                Cài đặt
            </a>
        </li>
    </ul>

    <!-- Tất cả nội dung tab nằm trong tab-content -->
    <div class="tab-content">

        <!-- TAB BÁO CÁO SHOP -->
        <div
            class="tab-pane fade show active"
            id="shop-reports">
            <div class="card shadow mb-4">
                <div class="card-header">
                    Danh sách báo cáo shop
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover report-table">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Shop</th>
                                <th>Người báo cáo</th>
                                <th>Lý do</th>
                                <th>Ngày báo cáo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (!$shopReports || $shopReports->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="empty-message">
                                    Chưa có báo cáo shop.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($report = $shopReports->fetch_assoc()): ?>
                                <tr class="report-row" tabindex="0">
                                    <td>
                                        <?= (int)$report['maBaoCao'] ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            $report['tenShopHienTai']
                                            ?: $report['tenShop']
                                        ) ?>
                                        <br>
                                        <small>
                                            ID:
                                            <?= (int)$report['maShop'] ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= e(
                                            $report['tenNguoiBaoHienTai']
                                            ?: $report['tenNguoiBaoCao']
                                        ) ?>
                                        <br>
                                        <small>
                                            ID:
                                            <?= (int)$report['maNguoiBaoCao'] ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= e(reasonText($report['lyDo'])) ?>

                                        <?php if (
                                            $report['lyDo'] === 'khac'
                                            && !empty($report['lyDoKhac'])
                                        ): ?>
                                            <br>
                                            <small>
                                                <?= e($report['lyDoKhac']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= e($report['ngayBaoCao']) ?>
                                    </td>

                                    <td>
                                        <?= renderStatusSelect(
                                            $report['maBaoCao'],
                                            $report['trangThai'],
                                            $statuses
                                        ) ?>
                                    </td>
                                </tr>
                                <tr class="report-detail-row">
                                    <td colspan="6">
                                        <div class="report-detail-content">
                                            <div>
                                                <strong>Mô tả chi tiết</strong>
                                                <div class="report-detail-description">
                                                    <?= nl2br(e($report['moTa'])) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>Bằng chứng</strong>
                                                <?= renderEvidence($report['bangChung']) ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-actions">
                <button
                    type="button"
                    class="btn btn-primary save-report-changes"
                    data-report-type="shop"
                    disabled>
                    Lưu thay đổi
                </button>

                <span
                    class="save-report-message"
                    data-report-type="shop">
                </span>
            </div>
        </div>

        <!-- TAB BÁO CÁO SẢN PHẨM -->
        <div
            class="tab-pane fade"
            id="product-reports">

            <div class="card shadow mb-4">
                <div class="card-header">
                    Danh sách báo cáo sản phẩm
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover report-table">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Shop</th>
                                <th>Người báo cáo</th>
                                <th>Lý do</th>
                                <th>Ngày báo cáo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (
                            !$productReports
                            || $productReports->num_rows === 0
                        ): ?>
                            <tr>
                                <td colspan="7" class="empty-message">
                                    Chưa có báo cáo sản phẩm.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($report = $productReports->fetch_assoc()): ?>
                                <tr class="report-row" tabindex="0">
                                    <td>
                                        <?= (int)$report['maBaoCao'] ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            $report['tenSanPham']
                                            ?: 'Sản phẩm đã xóa'
                                        ) ?>
                                        <br>
                                        <small>
                                            ID:
                                            <?= (int)$report['maSanPham'] ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= e(
                                            $report['tenShopHienTai']
                                            ?: $report['tenShop']
                                        ) ?>
                                        <br>
                                        <small>
                                            ID:
                                            <?= (int)$report['maShop'] ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= e(
                                            $report['tenNguoiBaoHienTai']
                                            ?: $report['tenNguoiBaoCao']
                                        ) ?>
                                        <br>
                                        <small>
                                            ID:
                                            <?= (int)$report['maNguoiBaoCao'] ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= e(reasonText($report['lyDo'])) ?>

                                        <?php if (
                                            $report['lyDo'] === 'khac'
                                            && !empty($report['lyDoKhac'])
                                        ): ?>
                                            <br>
                                            <small>
                                                <?= e($report['lyDoKhac']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= e($report['ngayBaoCao']) ?>
                                    </td>

                                    <td>
                                        <?= renderStatusSelect(
                                            $report['maBaoCao'],
                                            $report['trangThai'],
                                            $statuses
                                        ) ?>
                                    </td>
                                </tr>
                                <tr class="report-detail-row">
                                    <td colspan="7">
                                        <div class="report-detail-content">
                                            <div>
                                                <strong>Mô tả chi tiết</strong>
                                                <div class="report-detail-description">
                                                    <?= nl2br(e($report['moTa'])) ?>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>Bằng chứng</strong>
                                                <?= renderEvidence($report['bangChung']) ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="report-actions">
                <button
                    type="button"
                    class="btn btn-primary save-report-changes"
                    data-report-type="product"
                    disabled>
                    Lưu thay đổi
                </button>

                <span
                    class="save-report-message"
                    data-report-type="product">
                </span>
            </div>
        </div>

        <!-- TAB THEO DÕI -->
        <div
            class="tab-pane fade"
            id="watch-reports">

            <div class="card shadow watch-card">
                <div class="card-header">
                    Shop và sản phẩm cần theo dõi
                </div>

                <div class="card-body">

                    <h5 class="mb-3">
                        Shop có nhiều báo cáo đã xác minh
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered watch-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Shop</th>
                                    <th>Số báo cáo đúng</th>
                                    <th>Trạng thái tài khoản</th>
                                    <th>Quyết định</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if (
                                !$watchShopReports
                                || $watchShopReports->num_rows === 0
                            ): ?>
                                <tr>
                                    <td colspan="4" class="empty-message">
                                        Chưa có shop cần theo dõi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php while (
                                    $item = $watchShopReports->fetch_assoc()
                                ): ?>
                                    <tr>
                                        <td>
                                            <?= e($item['tenShop']) ?>
                                            <br>
                                            <small>
                                                ID:
                                                <?= (int)$item['maShop'] ?>
                                            </small>
                                        </td>

                                        <td>
                                            <span class="badge badge-danger">
                                                <?= (int)$item['soBaoCao'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?=
                                                (int)$item['trangThaiShop'] === 1
                                                ? 'Đang hoạt động'
                                                : 'Đã khóa'
                                            ?>
                                        </td>

                                        <td>
                                            <?php if (
                                                (int)$item['trangThaiShop'] === 1
                                            ): ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm watch-action"
                                                    data-action="lock_account"
                                                    data-id="<?= (int)$item['maShop'] ?>">
                                                    Khóa tài khoản
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-success btn-sm watch-action"
                                                    data-action="unlock_account"
                                                    data-id="<?= (int)$item['maShop'] ?>">
                                                    Mở khóa
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4 mb-3">
                        Sản phẩm có nhiều báo cáo đã xác minh
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered watch-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Shop</th>
                                    <th>Số báo cáo đúng</th>
                                    <th>Trạng thái</th>
                                    <th>Quyết định</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if (
                                !$watchProductReports
                                || $watchProductReports->num_rows === 0
                            ): ?>
                                <tr>
                                    <td colspan="5" class="empty-message">
                                        Chưa có sản phẩm cần theo dõi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php while (
                                    $item = $watchProductReports->fetch_assoc()
                                ): ?>
                                    <tr>
                                        <td>
                                            <?= e(
                                                $item['tenSanPham']
                                                ?: 'Sản phẩm đã xóa'
                                            ) ?>
                                            <br>
                                            <small>
                                                ID:
                                                <?= (int)$item['maSanPham'] ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= e(
                                                $item['tenShop']
                                                ?: 'Shop đã xóa'
                                            ) ?>
                                            <br>
                                            <small>
                                                ID:
                                                <?= (int)$item['maShop'] ?>
                                            </small>
                                        </td>

                                        <td>
                                            <span class="badge badge-danger">
                                                <?= (int)$item['soBaoCao'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?=
                                                (int)$item['trangThaiSanPham'] === 1
                                                ? 'Đang hiển thị'
                                                : 'Đã ẩn'
                                            ?>
                                        </td>

                                        <td>
                                            <?php if (
                                                (int)$item['trangThaiSanPham'] === 1
                                            ): ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-sm watch-action"
                                                    data-action="hide_product"
                                                    data-id="<?= (int)$item['maSanPham'] ?>">
                                                    Ẩn sản phẩm
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    Đã ẩn
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CÀI ĐẶT -->
        <div
            class="tab-pane fade"
            id="report-settings">

            <!-- Đưa thẻ form ra bọc ngoài cùng để lấy dữ liệu của cả 2 phần -->
            <form id="report-settings-form">
                <div class="card shadow">
                    
                    <!-- HEADER 1: CÀI ĐẶT THEO DÕI VI PHẠM -->
                    <div class="card-header">
                        Cài đặt theo dõi vi phạm
                    </div>

                    <!-- BODY 1 -->
                    <div class="card-body">
                        <div class="settings-box">
                            <div class="form-group">
                                <label for="soLanBaoCaoShopTheoDoi">
                                    Số báo cáo đúng để theo dõi shop
                                </label>
                                <input
                                    type="number"
                                    id="soLanBaoCaoShopTheoDoi"
                                    name="soLanBaoCaoShopTheoDoi"
                                    class="form-control"
                                    min="1"
                                    max="1000"
                                    value="<?= (int)$settings['soLanBaoCaoShopTheoDoi'] ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="soLanBaoCaoSanPhamTheoDoi">
                                    Số báo cáo đúng để theo dõi sản phẩm
                                </label>
                                <input
                                    type="number"
                                    id="soLanBaoCaoSanPhamTheoDoi"
                                    name="soLanBaoCaoSanPhamTheoDoi"
                                    class="form-control"
                                    min="1"
                                    max="1000"
                                    value="<?= (int)$settings['soLanBaoCaoSanPhamTheoDoi'] ?>"
                                    required>
                            </div>

                            <div class="form-check mb-3">
                                <input
                                    type="checkbox"
                                    id="report_watch_enabled"
                                    name="report_watch_enabled"
                                    class="form-check-input"
                                    <?= (int)$settings['report_watch_enabled'] === 1 ? 'checked' : '' ?>>
                                <label
                                    class="form-check-label"
                                    for="report_watch_enabled">
                                    Bật tab theo dõi tự động
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary">
                                Lưu cài đặt
                            </button>

                            <span
                                id="settings-message"
                                class="settings-message ml-2">
                            </span>
                        </div>
                    </div>

                    <?php
                    $messageTemplates = json_decode(
                        (string)$settings['thongBaoViPham'],
                        true
                    );
                    ?>

                    <!-- HEADER 2: TIN NHẮN TỰ ĐỘNG (Thêm border-top để có dòng kẻ phân cách) -->
                    <div class="card-header" style="border-top: 1px solid #e3e6f0;">
                        Tin nhắn tự động
                    </div>

                    <!-- BODY 2 -->
                    <div class="card-body">
                        <div class="message-settings">
                            <p class="text-muted">
                                Có thể dùng <code>{maBaoCao}</code> và
                                <code>{trangThai}</code> trong tin nhắn.
                            </p>

                            <div class="table-responsive">
                                <table class="table table-bordered message-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Trạng thái</th>
                                            <th>Người bị tố cáo</th>
                                            <th>Người tố cáo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($statuses as $value => $label): ?>
                                        <tr>
                                            <td><?= e($label) ?></td>
                                            <td>
                                                <textarea
                                                    name="message_<?= e($value) ?>_bi_to_cao"
                                                    class="form-control violation-message"
                                                    maxlength="1000"
                                                    required><?= e($messageTemplates['bi_to_cao'][$value] ?? '') ?></textarea>
                                            </td>
                                            <td>
                                                <textarea
                                                    name="message_<?= e($value) ?>_nguoi_to_cao"
                                                    class="form-control violation-message"
                                                    maxlength="1000"
                                                    required><?= e($messageTemplates['nguoi_to_cao'][$value] ?? '') ?></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

<script>
(function () {
    const reportUrl = '../caidat/baocao.php';
    const pendingChanges = {};

    $(document)
        .off('click.reportRow', '.report-row')
        .on('click.reportRow', '.report-row', function (event) {
            if ($(event.target).closest('select, button, a, input').length) {
                return;
            }

            $(this).next('.report-detail-row').toggleClass('is-open');
        })
        .off('keydown.reportRow', '.report-row')
        .on('keydown.reportRow', '.report-row', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            $(this).next('.report-detail-row').toggleClass('is-open');
        });

    $(document)
        .off('change.reportStatus', '.report-status')
        .on(
            'change.reportStatus',
            '.report-status',
            function () {
                const select = $(this);
                const reportId = String(select.data('id'));
                const status = select.val();

                pendingChanges[reportId] = {
                    id: Number(reportId),
                    status: status
                };

                const type = select.closest('.tab-pane').attr('id') === 'shop-reports'
                    ? 'shop'
                    : 'product';

                $(
                    '.save-report-changes[data-report-type="' + type + '"]'
                ).prop('disabled', false);

                $(
                    '.save-report-message[data-report-type="' + type + '"]'
                )
                    .removeClass('text-success text-danger')
                    .text('Có thay đổi chưa lưu');
            }
        );

    $(document)
        .off('submit.reportSettings', '#report-settings-form')
        .on(
            'submit.reportSettings',
            '#report-settings-form',
            function (event) {
                event.preventDefault();

                const form = $(this);
                const messageSettings = {
                    bi_to_cao: {},
                    nguoi_to_cao: {}
                };
                const statusValues = [
                    'cho_xu_ly',
                    'dang_xu_ly',
                    'da_xac_minh',
                    'tu_choi',
                    'da_dong'
                ];

                statusValues.forEach(function (status) {
                    messageSettings.bi_to_cao[status] = form
                        .find('[name="message_' + status + '_bi_to_cao"]')
                        .val();
                    messageSettings.nguoi_to_cao[status] = form
                        .find('[name="message_' + status + '_nguoi_to_cao"]')
                        .val();
                });

                $.post(reportUrl, {
                    save_report_settings: 1,
                    soLanBaoCaoShopTheoDoi: form
                        .find('[name="soLanBaoCaoShopTheoDoi"]')
                        .val(),
                    soLanBaoCaoSanPhamTheoDoi: form
                        .find('[name="soLanBaoCaoSanPhamTheoDoi"]')
                        .val(),
                    report_watch_enabled: form
                        .find('[name="report_watch_enabled"]')
                        .is(':checked') ? 1 : 0,
                    thongBaoViPham: JSON.stringify(messageSettings)
                })
                .done(function (response) {
                    const output = $.trim(response);
                    const message = $('#settings-message');

                    if (output === 'OK') {
                        message
                            .removeClass('text-danger')
                            .addClass('text-success')
                            .text('Đã lưu cài đặt.');
                    } else {
                        message
                            .removeClass('text-success')
                            .addClass('text-danger')
                            .text(output);
                    }
                })
                .fail(function (xhr) {
                    $('#settings-message')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text('Lỗi máy chủ: ' + xhr.status);
                });
            }
        );

    $(document)
        .off('click.saveReportChanges', '.save-report-changes')
        .on(
            'click.saveReportChanges',
            '.save-report-changes',
            function () {
                const button = $(this);
                const type = button.data('report-type');
                const message = $(
                    '.save-report-message[data-report-type="' + type + '"]'
                );

                const reportPane = type === 'shop'
                    ? '#shop-reports'
                    : '#product-reports';

                const changes = $(reportPane + ' .report-status')
                    .map(function () {
                        const select = $(this);
                        const reportId = String(select.data('id'));

                        return pendingChanges[reportId] || null;
                    })
                    .get();

                if (changes.length === 0) {
                    message
                        .removeClass('text-danger')
                        .addClass('text-muted')
                        .text('Chưa có thay đổi cần lưu');
                    return;
                }

                button.prop('disabled', true);
                message
                    .removeClass('text-success text-danger')
                    .addClass('text-muted')
                    .text('Đang lưu...');

                $.post(reportUrl, {
                    update_statuses: 1,
                    changes: JSON.stringify(changes)
                })
                .done(function (response) {
                    if ($.trim(response) !== 'OK') {
                        message
                            .removeClass('text-muted')
                            .addClass('text-danger')
                            .text($.trim(response));

                        button.prop('disabled', false);
                        return;
                    }

                    changes.forEach(function (change) {
                        delete pendingChanges[String(change.id)];
                    });

                    message
                        .removeClass('text-muted text-danger')
                        .addClass('text-success')
                        .text('Đã lưu thay đổi');

                    /*
                     * Tải lại toàn bộ module báo cáo.
                     * Các báo cáo đã xác minh đủ ngưỡng sẽ xuất hiện
                     * ngay trong tab Theo dõi.
                     */
                    $('#ajax-content').load(reportUrl, function () {
                        $('#reportTabs a[href="' + reportPane + '"]').tab('show');
                    });
                })
                .fail(function (xhr) {
                    message
                        .removeClass('text-muted')
                        .addClass('text-danger')
                        .text(
                            'Lỗi máy chủ: '
                            + xhr.status
                        );

                    button.prop('disabled', false);
                });
            }
        );

    $(document)
        .off('click.watchAction', '.watch-action')
        .on(
            'click.watchAction',
            '.watch-action',
            function () {
                const button = $(this);
                const action = button.data('action');
                const id = button.data('id');
                const actionText = action === 'lock_account'
                    ? 'khóa tài khoản này'
                    : 'mở khóa tài khoản này';

                if (!confirm('Bạn có chắc muốn ' + actionText + '?')) {
                    return;
                }

                button.prop('disabled', true);

                $.post(reportUrl, {
                    watch_action: action,
                    id: id
                })
                .done(function (response) {
                    const output = $.trim(response);

                    if (output === 'OK') {
                        $('#ajax-content').load(reportUrl);
                    } else {
                        alert(output || 'Không thể thực hiện thao tác.');
                        button.prop('disabled', false);
                    }
                })
                .fail(function (xhr) {
                    alert(
                        'Lỗi máy chủ: '
                        + xhr.status
                        + (xhr.responseText ? ' - ' + xhr.responseText : '')
                    );
                    button.prop('disabled', false);
                });
            }
        );
})();
</script>