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

$requests = $conn->query("
    SELECT
        yc.id,
        yc.maTaiKhoan,
        yc.lyDo,
        yc.lyDoKhac,
        yc.trangThai,
        tk.tenNguoiDung,
        tk.tenDangNhap
    FROM yeucauxoatk yc
    INNER JOIN taikhoan tk
        ON tk.maTaiKhoan = yc.maTaiKhoan
    ORDER BY yc.id DESC
");
?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý người dùng</h1>
    <ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-tab="usersTab">
            Quản lý người dùng
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="requestsTab">
            Yêu cầu
        </button>
    </li>
</ul>
<div id="usersTab" class="admin-tab-content">
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
<div id="requestsTab" class="admin-tab-content" style="display:none">
    <h2 class="h4 mb-3">Yêu cầu xóa tài khoản</h2>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($request = $requests->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$request['maTaiKhoan'] ?></td>
                    <td>
                        <?= htmlspecialchars(
                            $request['tenNguoiDung'] ?: $request['tenDangNhap'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($request['lyDo'], ENT_QUOTES, 'UTF-8') ?>

                        <?php if (!empty($request['lyDoKhac'])): ?>
                            <br>
                            <small>
                                Chi tiết:
                                <?= htmlspecialchars(
                                    $request['lyDoKhac'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($request['trangThai'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($request['trangThai'] === 'cho_xu_ly'): ?>
                            <button
                                class="btn btn-success btn-sm"
                                onclick="approveDelete(<?= (int)$request['id'] ?>)">
                                Đồng ý
                            </button>

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="rejectDelete(<?= (int)$request['id'] ?>)">
                                Từ chối
                            </button>
                        <?php else: ?>
                            Đã xử lý
                        <?php endif; ?>
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
$(document).on("click", "[data-tab]", function () {
    const tab = $(this).data("tab");

    $(".admin-tab-content").hide();
    $("#" + tab).show();

    $("[data-tab]").removeClass("active");
    $(this).addClass("active");
});

function approveDelete(requestId) {
    if (!confirm("Bạn có chắc chắn muốn đồng ý xóa tài khoản này không?")) {
        return;
    }

    $.post(
        "users_manager.php",
        {
            action: "approve_delete",
            request_id: requestId
        },
        function (response) {
            if (response === "OK") {
                alert("Đã đồng ý và xóa tài khoản.");
                loadPage("users.php");
            } else {
                alert(response);
            }
        }
    );
}

function rejectDelete(requestId) {
    if (!confirm("Bạn có chắc chắn muốn từ chối yêu cầu này không?")) {
        return;
    }

    const defaultReason =
        "Yêu cầu chưa đủ điều kiện để xóa tài khoản.";

    const reason = prompt(
        "Nhập lý do từ chối. Bấm Hủy để dùng lý do mặc định:",
        defaultReason
    );

    if (reason === null) {
        return;
    }

    const finalReason = reason.trim() || defaultReason;

    $.post(
        "users_manager.php",
        {
            action: "reject_delete",
            request_id: requestId,
            reason: finalReason
        },
        function (response) {
            if (response === "OK") {
                alert("Đã từ chối yêu cầu.");
                loadPage("users.php");
            } else {
                alert(response);
            }
        }
    );
}
</script>