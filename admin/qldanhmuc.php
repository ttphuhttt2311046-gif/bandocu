<?php

session_start();
include "../db.php";
// xử lý GET JSON trước khi xuất HTML
if (isset($_GET['getOtherCategories'])) {
    header('Content-Type: application/json; charset=utf-8');
    $excludeId = intval($_GET['getOtherCategories']);
    $stmt = $conn->prepare("SELECT maDanhMuc, tenDanhMuc FROM danhmuc WHERE maDanhMuc <> ? ORDER BY tenDanhMuc ASC");
    $stmt->bind_param("i", $excludeId);
    $stmt->execute();
    $res = $stmt->get_result();
    $cats = [];
    while ($row = $res->fetch_assoc()) {
        $cats[] = $row;
    }
    echo json_encode($cats);
    exit;
}
// Kiểm tra quyền admin
if ($_SESSION['vaitro'] !== 'admin') {
    echo json_encode(['status'=>'error','msg'=>'Bạn không có quyền truy cập']);
    exit;
}

// ===== AJAX HANDLER =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // THÊM DANH MỤC
    if (isset($_POST['them_danhmuc'])) {
        $tenDanhMuc = trim($_POST['tenDanhMuc'] ?? '');
        $moTa = trim($_POST['moTa'] ?? '');

        if (empty($tenDanhMuc)) {
            echo json_encode(['status'=>'error','msg'=>'Tên danh mục không được để trống']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO danhmuc (tenDanhMuc, moTa) VALUES (?, ?)");
        $stmt->bind_param("ss", $tenDanhMuc, $moTa);

        if ($stmt->execute()) {
            echo json_encode([
                'status'=>'ok',
                'id'=>$stmt->insert_id,
                'tenDanhMuc'=>$tenDanhMuc,
                'moTa'=>$moTa
            ]);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Lỗi thêm danh mục']);
        }
        $stmt->close();
        exit;
    }

    // // XÓA DANH MỤC
    // if (isset($_POST['xoa_danhmuc'])) {
    //     $id = intval($_POST['xoa_danhmuc']);
    //     $moveToCategory = isset($_POST['moveToCategory']) ? intval($_POST['moveToCategory']) : 0;

    //     // kiểm tra danh mục tồn tại
    //     $check = $conn->prepare("SELECT maDanhMuc FROM danhmuc WHERE maDanhMuc = ?");
    //     $check->bind_param("i", $id);
    //     $check->execute();
    //     $checkRes = $check->get_result();
    //     if (!$checkRes || $checkRes->num_rows === 0) {
    //         echo json_encode(['status'=>'error','msg'=>'Danh mục không tồn tại']);
    //         $check->close();
    //         exit;
    //     }
    //     $check->close();

    //     // đếm sản phẩm thuộc danh mục
    //     $cntStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM sanpham WHERE maDanhMuc = ?");
    //     $cntStmt->bind_param("i", $id);
    //     $cntStmt->execute();
    //     $cnt = $cntStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
    //     $cntStmt->close();

    //     // nếu có sản phẩm bắt buộc chọn danh mục đích hợp lệ
    //     if ($cnt > 0) {
    //         if ($moveToCategory <= 0) {
    //             echo json_encode(['status'=>'error','msg'=>'Danh mục có sản phẩm. Vui lòng chọn danh mục đích để chuyển sản phẩm trước khi xóa.']);
    //             exit;
    //         }
    //         if ($moveToCategory === $id) {
    //             echo json_encode(['status'=>'error','msg'=>'Vui lòng chọn danh mục đích khác với danh mục đang xóa.']);
    //             exit;
    //         }
    //         // kiểm tra danh mục đích tồn tại
    //         $chkDest = $conn->prepare("SELECT maDanhMuc FROM danhmuc WHERE maDanhMuc = ?");
    //         $chkDest->bind_param("i", $moveToCategory);
    //         $chkDest->execute();
    //         $destRes = $chkDest->get_result();
    //         if (!$destRes || $destRes->num_rows === 0) {
    //             echo json_encode(['status'=>'error','msg'=>'Danh mục đích không tồn tại']);
    //             $chkDest->close();
    //             exit;
    //         }
    //         $chkDest->close();
    //     }

    //     // thực hiện cập nhật và xóa trong transaction
    //     $conn->begin_transaction();
    //     try {
    //         if ($cnt > 0) {
    //             $upd = $conn->prepare("UPDATE sanpham SET maDanhMuc = ? WHERE maDanhMuc = ?");
    //             if (!$upd) throw new Exception('Lỗi prepare UPDATE: '.$conn->error);
    //             $upd->bind_param("ii", $moveToCategory, $id);
    //             if (!$upd->execute()) {
    //                 $err = $upd->error;
    //                 $upd->close();
    //                 throw new Exception('Lỗi cập nhật sản phẩm: '.$err);
    //             }
    //             $upd->close();
    //         }

    //         $del = $conn->prepare("DELETE FROM danhmuc WHERE maDanhMuc = ?");
    //         if (!$del) throw new Exception('Lỗi prepare DELETE: '.$conn->error);
    //         $del->bind_param("i", $id);
    //         if (!$del->execute()) {
    //             $err = $del->error;
    //             $del->close();
    //             throw new Exception('Lỗi xóa danh mục: '.$err);
    //         }
    //         $del->close();

    //         $conn->commit();
    //         echo json_encode(['status'=>'ok','id'=>$id]);
    //     } catch (Exception $e) {
    //         $conn->rollback();
    //         echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
    //     }
    //     exit;
    // }
if (isset($_POST['an_danhmuc'])) {
        $id = intval($_POST['an_danhmuc']);
        $trangThai = isset($_POST['trangThai']) ? intval($_POST['trangThai']) : 0;

        // kiểm tra danh mục tồn tại
        $check = $conn->prepare("SELECT maDanhMuc FROM danhmuc WHERE maDanhMuc = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $checkRes = $check->get_result();
        if (!$checkRes || $checkRes->num_rows === 0) {
            echo json_encode(['status'=>'error','msg'=>'Danh mục không tồn tại']);
            $check->close();
            exit;
        }
        $check->close();

        // cập nhật trạng thái ẩn/hiện
        $update = $conn->prepare("UPDATE danhmuc SET trangThai = ? WHERE maDanhMuc = ?");
        $update->bind_param("ii", $trangThai, $id);

        if ($update->execute()) {
            $msg = $trangThai == 1 ? "Danh mục đã được ẩn" : "Danh mục đã được hiển thị";
            echo json_encode(['status'=>'ok','id'=>$id,'msg'=>$msg]);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Lỗi cập nhật trạng thái']);
        }
        $update->close();
        exit;
    }
    /// XỬ LÝ CẬP NHẬT THỨ TỰ
    if (isset($_POST['cap_nhat_thu_tu'])) {
    $order = json_decode($_POST['cap_nhat_thu_tu'], true);
    
    if (empty($order) || !is_array($order)) {
        echo json_encode(['status'=>'error','msg'=>'Dữ liệu không hợp lệ']);
        exit;
    }

    $success = true;
    foreach ($order as $index => $id) {
        $id = intval($id);
        $thuTu = $index;
        
        $update = $conn->prepare("UPDATE danhmuc SET thu_tu = ? WHERE maDanhMuc = ?");
        $update->bind_param("ii", $thuTu, $id);
        
        if (!$update->execute()) {
            $success = false;
        }
        $update->close();
    }

    if ($success) {
        echo json_encode(['status'=>'ok','msg'=>'✅ Cập nhật thứ tự thành công']);
    } else {
        echo json_encode(['status'=>'error','msg'=>'❌ Lỗi cập nhật']);
    }
    exit;
    }
}
// ===== LẤY DANH SÁCH DANH MỤC =====
$danhmuc = $conn->query("SELECT * FROM danhmuc ORDER BY thu_tu ASC, maDanhMuc ASC");
?>
<link rel="stylesheet" href="css/qldanhmuc.css">
<h4 class="mb-3">📚 Quản lý Danh Mục</h4>
<div class="danhmuc-container">
    
    <!-- FORM THÊM DANH MỤC -->
    <div class="form-add-danhmuc">
        <h5>➕ Thêm Danh Mục Mới</h5>
        <form id="formThemDanhMuc">
            <input type="hidden" name="them_danhmuc" value="1">
            <input 
                type="text" 
                name="tenDanhMuc" 
                placeholder="Tên danh mục" 
                required
            >
            <textarea 
                name="moTa" 
                rows="2" 
                placeholder="Mô tả (không bắt buộc)"
            ></textarea>
            <button type="submit">➕ Thêm</button>
        </form>
    </div>

    <!-- DANH SÁCH DANH MỤC -->
    <h5>📋 Danh Sách Danh Mục (Kéo để sắp xếp)</h5>
    <ul id="danhsach-danhmuc" class="sortable-list">
        
        <?php while ($dm = $danhmuc->fetch_assoc()): 
            // Đếm số sản phẩm trong danh mục
            $countProduct = $conn->query("SELECT COUNT(*) as cnt FROM sanpham WHERE maDanhMuc={$dm['maDanhMuc']}")->fetch_assoc();
            $productCount = $countProduct['cnt'];
            $trangThai = isset($dm['trangThai']) ? intval($dm['trangThai']) : 0;
            $isHidden = ($trangThai == 1) ? true : false;
        ?>
        <li class="danhmuc-item" draggable="true" data-id="<?= $dm['maDanhMuc'] ?>">
            <span class="drag-handle">☰</span>
            <div class="danhmuc-info">
                <strong><?= htmlspecialchars($dm['tenDanhMuc']) ?></strong>
                <small>
                    <?= $productCount > 0 
                        ? "📦 " . $productCount . " sản phẩm" 
                        : "Không có sản phẩm" 
                    ?>
                    <?php if (!empty($dm['moTa'])): ?>
                        | <?= htmlspecialchars(substr($dm['moTa'], 0, 50)) ?>...
                    <?php endif; ?>
                </small>
            </div>
            <div class="danhmuc-actions">
                <!-- <button 
                    class="btn-xoa" 
                    onclick="xoaDanhMuc(<?= $dm['maDanhMuc'] ?>, '<?= htmlspecialchars($dm['tenDanhMuc']) ?>', <?= $productCount ?>)"
                >
                    🗑️ Xóa
                </button> -->
                <button 
                    class="btn-an" 
                    onclick="anDanhMuc(<?= $dm['maDanhMuc'] ?>, '<?= htmlspecialchars($dm['tenDanhMuc']) ?>', <?= $trangThai ?>)"
                    style="background-color: <?= $isHidden ? '#28a745' : '#ffc107' ?>; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                >
                    <?= $isHidden ? 'Hiện' : 'Ẩn' ?>
                </button>
            </div>
        </li>
        <?php endwhile; ?>
    </ul>
    <!-- BUTTON LƯU THỨ TỰ -->
    <div style="margin-top: 20px; text-align: center;">
        <button id="btn-luu-thu-tu" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; display: none;">
            Lưu thay đổi
        </button>
        <span id="trang-thai-keo" style="margin-left: 10px; color: #666; display: none;">Nhấn "Lưu thay đổi" để cập nhật</span>
    </div>
</div>

<!-- MODAL XÓA DANH MỤC -->
<!--<div id="modal-xoa" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; display:none;">
    <div style="background:white; padding:30px; border-radius:8px; max-width:500px; width:90%;">
        <h4>⚠️ Xác Nhận Xóa Danh Mục</h4>
        <p id="modal-msg"></p>
        
        <div id="move-category-section" style="display:none; margin-top:15px; padding-top:15px; border-top:1px solid #ddd;">
            <label><strong>Chuyển sản phẩm sang danh mục:</strong></label>
            <select id="moveToCategory" style="width:100%; padding:8px; margin-top:8px;">
                <option value="">-- Không chuyển (xóa luôn) --</option>
            </select>
        </div>

        <div style="margin-top:20px; text-align:right;">
            <button 
                onclick="document.getElementById('modal-xoa').style.display='none'" 
                style="background:#6c757d; color:white; padding:8px 16px; border:none; border-radius:4px; cursor:pointer; margin-right:10px;"
            >
                ❌ Hủy
            </button>
            <button 
                id="btn-xoa-confirm" 
                onclick="confirmXoa()" 
                style="background:#dc3545; color:white; padding:8px 16px; border:none; border-radius:4px; cursor:pointer;"
            >
                🗑️ Xóa
            </button>
        </div>
    </div>
</div> -->

<script>
// let currentDeleteId = null;
// let currentDeleteName = null;
// let currentProductCount = 0;

// function xoaDanhMuc(id, name, productCount) {
//     currentDeleteId = id;
//     currentDeleteName = name;
//     currentProductCount = productCount;

//     const msg = document.getElementById('modal-msg');
//     const moveSection = document.getElementById('move-category-section');
//     const moveSelect = document.getElementById('moveToCategory');
//     const btnConfirm = document.getElementById('btn-xoa-confirm');

//     if (productCount > 0) {
//         msg.innerHTML = `
//             <strong>Danh mục "${name}"</strong> có <strong>${productCount} sản phẩm</strong>.<br>
//             <br>
//             Bạn cần chuyển các sản phẩm này sang danh mục khác trước khi xóa.
//         `;
//         moveSection.style.display = 'block';

//         // chuẩn bị UI
//         btnConfirm.disabled = true;
//         moveSelect.innerHTML = '<option value="">Đang tải...</option>';

//         // Load danh sách danh mục khác (có catch để debug)
//         fetch('qldanhmuc.php?getOtherCategories=' + id, { cache: 'no-store' })
//             .then(response => {
//                 if (!response.ok) throw new Error('HTTP ' + response.status + ' - ' + response.statusText);
//                 return response.json();
//             })
//             .then(cats => {
//                 moveSelect.innerHTML = '<option value="">-- Chọn danh mục --</option>';

//                 if (!Array.isArray(cats)) {
//                     console.error('Không phải mảng JSON:', cats);
//                     moveSelect.innerHTML = '<option value="">-- Dữ liệu không hợp lệ --</option>';
//                     btnConfirm.disabled = true;
//                     return;
//                 }

//                 if (cats.length === 0) {
//                     moveSelect.innerHTML = '<option value="">-- Không có danh mục khác --</option>';
//                     btnConfirm.disabled = true;
//                     return;
//                 }

//                 cats.forEach(cat => {
//                     const opt = document.createElement('option');
//                     opt.value = cat.maDanhMuc;
//                     opt.textContent = cat.tenDanhMuc;
//                     moveSelect.appendChild(opt);
//                 });

//                 btnConfirm.disabled = false;
//             })
//             .catch(err => {
//                 console.error('Lỗi tải danh mục:', err);
//                 moveSelect.innerHTML = '<option value="">-- Lỗi tải danh mục --</option>';
//                 btnConfirm.disabled = true;
//                 // hiển thị cảnh báo ngắn để developer kiểm tra Network/Console
//                 alert('Lỗi tải danh mục đích. Mở DevTools -> Network xem response của qldanhmuc.php?getOtherCategories=' + id);
//             });

//     } else {
//         msg.innerHTML = `Xóa danh mục <strong>"${name}"</strong>? Không có sản phẩm nào.`;
//         moveSection.style.display = 'none';
//         btnConfirm.disabled = false;
//     }

//     document.getElementById('modal-xoa').style.display = 'flex';
// }

// function confirmXoa() {
//     const moveToCategory = document.getElementById('moveToCategory').value || '0';

//     if (currentProductCount > 0 && !moveToCategory) {
//         alert('⚠️ Vui lòng chọn danh mục đích cho sản phẩm');
//         return;
//     }

//     const fd = new FormData();
//     fd.append('xoa_danhmuc', currentDeleteId);
//     fd.append('moveToCategory', moveToCategory);

//     fetch('qldanhmuc.php', {
//         method: 'POST',
//         body: fd
//     })
//     .then(r => r.json())
//     .then(d => {
//         if (d.status === 'ok') {
//             alert('✅ Xóa danh mục thành công!');
//             document.getElementById('modal-xoa').style.display = 'none';
//             location.reload();
//         } else {
//             alert('❌ ' + d.msg);
//         }
//     });
// }
// ===== CHỨC NĂNG ẨN DANH MỤC =====
function anDanhMuc(id, name, trangThai) {
    const trangThaiMoi = trangThai == 1 ? 0 : 1;
    const hanhDong = trangThaiMoi == 1 ? 'ẩn' : 'hiển thị';
    
    if (confirm(`Bạn muốn ${hanhDong} danh mục "${name}"?`)) {
        const fd = new FormData();
        fd.append('an_danhmuc', id);
        fd.append('trangThai', trangThaiMoi);

        fetch('qldanhmuc.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'ok') {
                alert('✅ ' + d.msg);
                location.reload();
            } else {
                alert('❌ ' + d.msg);
            }
        });
    }
}
// ===== KÉO THẢ SẮP XẾP =====
const sortableList = document.getElementById('danhsach-danhmuc');
const btnLuu = document.getElementById('btn-luu-thu-tu');
const trangThaiKeo = document.getElementById('trang-thai-keo');
let draggedItem = null;
let thayDoi = false;

sortableList.addEventListener('dragstart', function(e) {
    draggedItem = this.querySelector('[draggable="true"]');
    if (e.target.classList.contains('danhmuc-item') || e.target.closest('.danhmuc-item')) {
        draggedItem = e.target.closest('.danhmuc-item');
        draggedItem.classList.add('dragging');
    }
});

sortableList.addEventListener('dragover', function(e) {
    e.preventDefault();
});

sortableList.addEventListener('drop', function(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(sortableList, e.clientY);
    
    if (afterElement == null) {
        sortableList.appendChild(draggedItem);
    } else {
        sortableList.insertBefore(draggedItem, afterElement);
    }

    draggedItem.classList.remove('dragging');
    
    // 🎯 Đánh dấu có thay đổi
    thayDoi = true;
    btnLuu.style.display = 'inline-block';
    trangThaiKeo.style.display = 'inline';
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.danhmuc-item:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

// 🔥 HÀM LƯU THỨ TỰ KHI BẤM BUTTON
function luuThuTu() {
    if (!thayDoi) {
        alert('Chưa có thay đổi nào để lưu');
        return;
    }

    const items = document.querySelectorAll('.danhmuc-item');
    const order = Array.from(items).map(item => item.dataset.id);

    console.log('Thứ tự mới:', order);

    // 🔥 GỬI LÊN SERVER
    const fd = new FormData();
    fd.append('cap_nhat_thu_tu', JSON.stringify(order));

    fetch('qldanhmuc.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'ok') {
            console.log('Đã lưu:', order);
            alert(d.msg);
            
            // 🎯 Ẩn button và thông báo sau khi lưu
            thayDoi = false;
            btnLuu.style.display = 'none';
            trangThaiKeo.style.display = 'none';
        } else {
            alert('❌ ' + d.msg);
        }
    })
    .catch(err => {
        console.error('Lỗi:', err);
        alert('Lỗi khi lưu thứ tự');
    });
}

// 🔗 GÁN SỰ KIỆN CHO BUTTON LƯU
btnLuu.addEventListener('click', luuThuTu);

// FORM THÊM DANH MỤC
document.getElementById('formThemDanhMuc').addEventListener('submit', function(e) {
    e.preventDefault();

    fetch('qldanhmuc.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'ok') {
            alert('Thêm danh mục thành công!');
            this.reset();
            location.reload();
        } else {
            alert('❌ ' + d.msg);
        }
    });
});

// Xử lý GET request để lấy danh mục khác
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('getOtherCategories')) {
        const id = params.get('getOtherCategories');
        fetch('qldanhmuc.php?getOtherCategories=' + id)
            .then(r => r.json())
            .then(data => console.log(data));
    }
});
</script>

<?php
// Xử lý GET request - lấy danh mục khác
// if (isset($_GET['getOtherCategories'])) {
//     header('Content-Type: application/json');
//     $excludeId = intval($_GET['getOtherCategories']);
//     $result = $conn->query("SELECT maDanhMuc, tenDanhMuc FROM danhmuc WHERE maDanhMuc != $excludeId ORDER BY tenDanhMuc ASC");
    
//     $categories = [];
//     while ($row = $result->fetch_assoc()) {
//         $categories[] = $row;
//     }
//     echo json_encode($categories);
//     exit;
// }
?>