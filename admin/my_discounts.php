<?php
session_start();
include "../db.php";

$user_id = $_SESSION['user_id'];
if ($_SESSION['vaitro'] !== 'seller') die("Từ chối truy cập");

// Cập nhật giá và quyền tham gia
if (isset($_POST['update_discount'])) {
    $idSP = intval($_POST['maSanPham']);
    $giaGiam = $_POST['giaGiam']; // Giá tiền cụ thể sau khi giảm riêng
    $thamGia = isset($_POST['thamGiaSuKien']) ? 1 : 0;

    $sql = "UPDATE sanpham SET giamGia = ?, thamGiaSuKien = ? WHERE maSanPham = ? AND maNguoiBan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("diii", $giaGiam, $thamGia, $idSP, $user_id);
    $stmt->execute();
}

// Lấy danh sách sản phẩm của người bán
$products = $conn->query("SELECT maSanPham, tenSanPham, gia, giamGia, thamGiaSuKien FROM sanpham WHERE maNguoiBan = $user_id");
?>

<div class="container-fluid">
    <h3 class="h3 mb-4 text-gray-800">Khuyến mãi của tôi</h3>
    <div class="alert alert-info">
        <b>Lưu ý:</b> Nếu bạn chọn "Tham gia sự kiện chung", hệ thống sẽ ưu tiên áp dụng mức % giảm giá của Admin khi có sự kiện lớn.
    </div>

    <table class="table table-hover bg-white shadow-sm">
        <thead class="thead-dark">
            <tr>
                <th>Sản phẩm</th>
                <th>Giá gốc</th>
                <th>Giá giảm riêng (VNĐ)</th>
                <th>Tham gia sự kiện Admin</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $products->fetch_assoc()): ?>
            <form method="POST">
                <tr>
                    <td><?= $row['tenSanPham'] ?></td>
                    <td><?= number_format($row['gia']) ?></td>
                    <td>
                        <input type="number" name="giaGiam" class="form-control form-control-sm" value="<?= $row['giamGia'] ?>">
                        <input type="hidden" name="maSanPham" value="<?= $row['maSanPham'] ?>">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="thamGiaSuKien" <?= ($row['thamGiaSuKien'] == 1) ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <button type="submit" name="update_discount" class="btn btn-sm btn-success">Lưu</button>
                    </td>
                </tr>
            </form>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>