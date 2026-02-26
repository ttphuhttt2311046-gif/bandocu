<?php
session_start();
include "../db.php";

/* ======================
   AJAX HANDLER
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // THÊM BANNER
    if (isset($_POST['them_banner'])) {

        $uploadDir = __DIR__ . "/../assets/uploads/banner/";
        $dbPath    = "assets/uploads/banner/";

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if (!isset($_FILES['hinh']) || $_FILES['hinh']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status'=>'error','msg'=>'Upload lỗi']); exit;
        }

        $filename = time().'_'.basename($_FILES['hinh']['name']);
        $fullPath = $uploadDir.$filename;
        $savePath = $dbPath.$filename;

        if (!move_uploaded_file($_FILES['hinh']['tmp_name'], $fullPath)) {
            echo json_encode(['status'=>'error','msg'=>'Không lưu file']); exit;
        }

        $link   = trim($_POST['link'] ?? '');
        $thu_tu = intval($_POST['thu_tu'] ?? 0);

        $stmt = $conn->prepare(
            "INSERT INTO banner (hinh, link, thu_tu, trangthai)
             VALUES (?, ?, ?, 1)"
        );
        $stmt->bind_param("ssi", $savePath, $link, $thu_tu);
        $stmt->execute();

        echo json_encode([
            'status'=>'ok',
            'id'=>$stmt->insert_id,
            'hinh'=>$savePath,
            'link'=>$link,
            'thu_tu'=>$thu_tu
        ]);
        exit;
    }

    // XÓA BANNER
    if (isset($_POST['xoa'])) {
        $id = intval($_POST['xoa']);
        $res = $conn->query("SELECT hinh FROM banner WHERE id=$id");
        if ($row = $res->fetch_assoc()) {
            $file = __DIR__ . "/../" . $row['hinh'];
            if (file_exists($file)) unlink($file);
        }
        $conn->query("DELETE FROM banner WHERE id=$id");
        echo json_encode(['status'=>'ok','id'=>$id]); 
        exit; // Chỉ exit ở đây khi thực hiện xong lệnh xóa
    }
    // CẬP NHẬT (SỬA) BANNER
    if (isset($_POST['sua_banner'])) {
        $id = intval($_POST['id']);
        $link = trim($_POST['link'] ?? '');
        $thu_tu = intval($_POST['thu_tu'] ?? 0);
        
        // Nếu có upload ảnh mới
        if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/../assets/uploads/banner/";
            $filename = time().'_'.basename($_FILES['hinh']['name']);
            move_uploaded_file($_FILES['hinh']['tmp_name'], $uploadDir.$filename);
            $newHinh = "assets/uploads/banner/".$filename;
            
            // Xóa ảnh cũ
            $old = $conn->query("SELECT hinh FROM banner WHERE id=$id")->fetch_assoc();
            if ($old && file_exists(__DIR__."/../".$old['hinh'])) unlink(__DIR__."/../".$old['hinh']);

            $stmt = $conn->prepare("UPDATE banner SET hinh=?, link=?, thu_tu=? WHERE id=?");
            $stmt->bind_param("ssii", $newHinh, $link, $thu_tu, $id);
        } else {
            $stmt = $conn->prepare("UPDATE banner SET link=?, thu_tu=? WHERE id=?");
            $stmt->bind_param("sii", $link, $thu_tu, $id);
        }

        if($stmt->execute()) {
            echo json_encode(['status'=>'ok', 'msg'=>'Cập nhật thành công']);
        } exit;
    }
}

/* ======================
   LẤY DANH SÁCH
====================== */
$banners = $conn->query("SELECT * FROM banner ORDER BY thu_tu ASC, id DESC");
?>

<h4 class="mb-3">Quản lý Banner</h4>

<style>
.banner-thumb {
    max-height: 60px;
    width: auto;
    max-width: 180px;
    display: block;
}
</style>

<form id="formBanner" enctype="multipart/form-data" class="card p-3 mb-4">
    <input type="hidden" name="them_banner" value="1">
    <input type="file" name="hinh" class="form-control mb-2" required>
    <input type="text" name="link" class="form-control mb-2" placeholder="Link">
    <input type="number" name="thu_tu" class="form-control mb-2" value="0">
    <button class="btn btn-primary">Thêm banner</button>
</form>

<table class="table table-bordered align-middle" id="tableBanner">
    <thead>
        <tr>
            <th>Hình</th>
            <th>Link / Thứ tự</th>
            <th width="150">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while($b = $banners->fetch_assoc()): ?>
        <tr id="banner-<?= $b['id'] ?>" 
            data-hinh="../<?= $b['hinh'] ?>" 
            data-link="<?= htmlspecialchars($b['link']) ?>" 
            data-thutu="<?= $b['thu_tu'] ?>">
            
            <td><img src="../<?= $b['hinh'] ?>" class="banner-thumb"></td>
            <td>
                <b>Link:</b> <?= htmlspecialchars($b['link']) ?><br>
                <b>Thứ tự:</b> <?= $b['thu_tu'] ?>
            </td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $b['id'] ?>)">Sửa</button>
                <button class="btn btn-danger btn-sm" onclick="xoaBanner(<?= $b['id'] ?>)">Xóa</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditBanner" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Sửa Banner</h5></div>
      <div class="modal-body">
        <input type="hidden" name="sua_banner" value="1">
        <input type="hidden" name="id" id="edit_id">
        <div class="mb-2">
            <label>Ảnh mới (để trống nếu giữ nguyên)</label>
            <input type="file" name="hinh" class="form-control">
        </div>
        <div class="mb-2">
            <label>Link</label>
            <input type="text" name="link" id="edit_link" class="form-control">
        </div>
        <div class="mb-2">
            <label>Thứ tự</label>
            <input type="number" name="thu_tu" id="edit_thutu" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
      </div>
    </form>
  </div>
</div>

<script>
// Đảm bảo bạn dùng Bootstrap 5 JS
const editModalEl = document.getElementById('editModal');
const editModal = new bootstrap.Modal(editModalEl);

// MỞ MODAL
function openEditModal(id) {
    const row = document.getElementById('banner-' + id);
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_link').value = row.dataset.link;
    document.getElementById('edit_thutu').value = row.dataset.thutu;
    editModal.show();
}

// XỬ LÝ LƯU (SỬA)
document.getElementById('formEditBanner').onsubmit = function(e) {
    e.preventDefault();
    fetch('banner.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'ok') {
            editModal.hide();
            location.reload(); // Reload để cập nhật data-attributes cho lần sửa sau
        } else {
            alert("Lỗi: " + (d.msg || "Không rõ lỗi"));
        }
    });
};

// XỬ LÝ THÊM MỚI
document.getElementById('formBanner').onsubmit = function(e){
    e.preventDefault();
    fetch('banner.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status === 'ok') location.reload();
        else alert(d.msg);
    });
};

// XỬ LÝ XÓA
function xoaBanner(id){
    if(!confirm('Bạn có chắc chắn muốn xóa?')) return;
    const fd = new FormData();
    fd.append('xoa', id);
    fetch('banner.php', { method: 'POST', body: fd })
    .then(r=>r.json())
    .then(d=>{
        if(d.status==='ok') document.getElementById('banner-'+id).remove();
    });
}
</script>
