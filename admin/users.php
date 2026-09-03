<?php 
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
session_start();
include "../db.php";
// Kiểm tra vai trò admin
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    die("Bạn không có quyền truy cập trang này");
}
// Khóa tài khoản
if (isset($_GET['lock'])) {
    $id = intval($_GET['lock']);
    $stmt = $conn->prepare("UPDATE taikhoan SET trangThai = 0 WHERE maTaiKhoan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    exit("OK");
}
// Mở khóa tài khoản
if (isset($_GET['unlock'])) {
    $id = intval($_GET['unlock']);
    $stmt = $conn->prepare("UPDATE taikhoan SET trangThai = 1 WHERE maTaiKhoan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    exit("OK");
}

// Lấy danh sách user
$users = $conn->query("SELECT * FROM taikhoan ORDER BY maTaiKhoan DESC");
?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý người dùng</h1>
    <!-- MOBILE COLUMN HEADER -->
<div class="mobile-column-header">
  <span>ID</span>
  <span>Trạng thái</span>
  <span>Tên</span>
  <span>Vai trò</span>
  <span></span>
</div>
    <div class="mobile-user-table">
  	<table class="table mobile-table">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Trạng thái</th>
                <th>Tên người dùng</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
<tr class="user-row" data-id="<?= $u['maTaiKhoan'] ?>">
    <td><?= $u['maTaiKhoan'] ?></td>
    <td>
        <?= $u['trangThai'] == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Bị khóa</span>' ?>
    </td>
    <td><?= htmlspecialchars($u['tenNguoiDung']) ?></td>
    <td><?= htmlspecialchars($u['tenDangNhap']) ?></td>
    <td><?= $u['vaitro'] ?></td>
    <td><?= htmlspecialchars($u['soDienThoai']) ?></td>
    <td><?= htmlspecialchars($u['diaChi']) ?></td>
    <td class="action-cell">
  <div class="desktop-actions">
    <button class="btn btn-danger btn-sm"
      onclick="manageUser('delete', <?= $u['maTaiKhoan'] ?>)">Xóa</button>

    <?php if ($u['trangThai'] == 1): ?>
      <button class="btn btn-warning btn-sm"
        onclick="toggleUser(<?= $u['maTaiKhoan'] ?>,'lock')">Khóa</button>
    <?php else: ?>
      <button class="btn btn-success btn-sm"
        onclick="toggleUser(<?= $u['maTaiKhoan'] ?>,'unlock')">Mở</button>
    <?php endif; ?>
  </div>
</td>
</tr>

<!-- HÀNG CHI TIẾT (ẨN) -->
<tr class="user-detail">
    <td colspan="8">
        <div class="user-detail-box">
            <div>Email: <?= htmlspecialchars($u['tenDangNhap']) ?></div>
            <div>Số điện thoại: <?= htmlspecialchars($u['soDienThoai']) ?></div>
            <div>Địa chỉ: <?= htmlspecialchars($u['diaChi']) ?></div>

            <div class="mt-2 d-flex gap-2">
                <button class="btn btn-danger btn-sm"
  onclick="event.stopPropagation(); manageUser('delete', <?= $u['maTaiKhoan'] ?>)">
  Xóa
</button>

                <?php if ($u['trangThai'] == 1): ?>
                    <button class="btn btn-warning btn-sm"
  onclick="event.stopPropagation(); toggleUser(<?= $u['maTaiKhoan'] ?>,'lock')">
  Khóa
</button>

                <?php else: ?>
                    <button class="btn btn-success btn-sm"
                        onclick="event.stopPropagation(); toggleUser(<?= $u['maTaiKhoan'] ?>,'unlock')">
                        Mở khóa
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </td>
</tr>
<?php endwhile; ?>
        </tbody>
       </table>
       </div> 
</div>
<script>
let isActionProcessing = false;

function loadPage(page){
  $("#ajax-content").load(page);
}

function toggleUser(id, action){
  isActionProcessing = true;
  $.get("users.php?" + action + "=" + id, function(res){
    if(res === "OK"){
      loadPage("users.php");
      setTimeout(()=>isActionProcessing=false,400);
    }
  });
}

function manageUser(action, id){

  // 👉 THÊM ĐOẠN NÀY
  if(action === 'delete'){
    if(!confirm("Bạn có chắc chắn muốn xóa người dùng này không?")){
      return; // ❌ Hủy nếu bấm Cancel
    }
  }

  isActionProcessing = true;

  $.get("users_manager.php?" + action + "=" + id, function(res){
    if(res === "OK"){
      loadPage("users.php");
      setTimeout(()=>isActionProcessing=false,400);
    }
  });
}

$(document).on("click", ".mobile-user-table .user-row", function(e){

  if (window.innerWidth > 768) return;
  if (isActionProcessing) return;
  if ($(e.target).closest("button,a").length) return;

  let $detail = $(this).next(".user-detail");

  $(".mobile-user-table .user-detail").not($detail).slideUp(150);
  $detail.stop(true,true).slideToggle(150);
});
</script>