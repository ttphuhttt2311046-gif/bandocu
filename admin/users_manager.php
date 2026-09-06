<?php
session_start();
include "../db.php";

// Kiểm tra quyền admin
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    exit("NO_PERMISSION");
}

// ==================
//  XÓA NGƯỜI DÙNG
// ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM taikhoan WHERE maTaiKhoan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    exit("OK");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $requestId = (int)($_POST['request_id'] ?? 0);

    if ($requestId <= 0) {
        exit('Yêu cầu không hợp lệ');
    }

    if ($action === 'approve_delete') {
        $stmt = $conn->prepare("
            SELECT maTaiKhoan
            FROM yeucauxoatk
            WHERE id = ?
              AND trangThai = 'cho_xu_ly'
            LIMIT 1
        ");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$request) {
            exit('Yêu cầu không tồn tại hoặc đã xử lý');
        }

        $conn->begin_transaction();

        try {
            $delete = $conn->prepare("
                DELETE FROM taikhoan
                WHERE maTaiKhoan = ?
            ");
            $delete->bind_param("i", $request['maTaiKhoan']);
            $delete->execute();
            $delete->close();

            $conn->commit();
            exit('OK');
        } catch (Throwable $e) {
            $conn->rollback();
            exit('Không thể xóa tài khoản');
        }
    }

    if ($action === 'reject_delete') {
        $reason = trim((string)($_POST['reason'] ?? ''));

        if ($reason === '') {
            $reason = 'Yêu cầu chưa đủ điều kiện để xóa tài khoản.';
        }

        if (mb_strlen($reason, 'UTF-8') > 1000) {
            exit('Lý do từ chối quá dài');
        }

        $stmt = $conn->prepare("
            SELECT maTaiKhoan
            FROM yeucauxoatk
            WHERE id = ?
              AND trangThai = 'cho_xu_ly'
            LIMIT 1
        ");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$request) {
            exit('Yêu cầu không tồn tại hoặc đã xử lý');
        }

        $conn->begin_transaction();

        try {
            $status = 'tu_choi';

            $update = $conn->prepare("
                UPDATE yeucauxoatk
                SET trangThai = ?,
                    lyDoTuChoi = ?,
                    ngayXuLy = NOW()
                WHERE id = ?
            ");
            $update->bind_param("ssi", $status, $reason, $requestId);
            $update->execute();
            $update->close();

            $message = "Yêu cầu xóa tài khoản của bạn đã bị từ chối. Lý do: "
                . $reason;

            $notify = $conn->prepare("
                INSERT INTO nhantin
                    (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
                VALUES (?, ?, ?, 'chua_xem', NOW())
            ");

            $adminId = (int)$_SESSION['user_id'];
            $notify->bind_param(
                "sii",
                $message,
                $adminId,
                $request['maTaiKhoan']
            );
            $notify->execute();
            $notify->close();

            $conn->commit();
            exit('OK');
        } catch (Throwable $e) {
            $conn->rollback();
            exit('Không thể từ chối yêu cầu');
        }
    }
}

// ==================
//  FORM EDIT
// ==================
if (isset($_GET['edit'])) {

    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE maTaiKhoan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) exit("USER_NOT_FOUND");
    ?>

    <div class="container">
        <form id="editForm">
            <input type="hidden" name="id" value="<?= $user['maTaiKhoan'] ?>">

            <label>Tên người dùng:</label>
            <input class="form-control" name="tenNguoiDung"
                   value="<?= htmlspecialchars($user['tenNguoiDung']) ?>">

            <label class="mt-2">Email:</label>
            <input class="form-control" name="tenDangNhap"
                   value="<?= htmlspecialchars($user['tenDangNhap']) ?>">

            <label class="mt-2">Vai trò:</label>
            <select class="form-control" name="vaitro">
                <option value="admin" <?= $user['vaitro']=='admin'?'selected':'' ?>>Admin</option>
                <option value="seller" <?= $user['vaitro']=='seller'?'selected':'' ?>>Người bán</option>
                <option value="buyer" <?= $user['vaitro']=='buyer'?'selected':'' ?>>Người mua</option>
            </select>

            <label class="mt-2">Số điện thoại:</label>
            <input class="form-control" name="soDienThoai"
                   value="<?= htmlspecialchars($user['soDienThoai']) ?>">

            <label class="mt-2">Địa chỉ:</label>
            <input class="form-control" name="diaChi"
                   value="<?= htmlspecialchars($user['diaChi']) ?>">

            <button class="btn btn-success mt-3">Cập nhật</button>
            <button type="button" class="btn btn-warning mt-3 ml-2"
            onclick="resetPassword(<?= $user['maTaiKhoan'] ?>)">Đặt mật khẩu mặc định</button>
        </form>
    </div>

    <script>
    $("#editForm").submit(function(e){
        e.preventDefault();

        $.post("users_manager.php", 
            $("#editForm").serialize() + "&update=1",
            function(res){
                if(res === "OK"){
                    alert("Cập nhật thành công!");
                    loadPage("users.php");
                } else {
                    alert("Có lỗi xảy ra!");
                }
            }
        );
    });
    </script>
    <script>
function resetPassword(id){

    if(!confirm("Bạn có chắc muốn đặt lại mật khẩu?")){
        return;
    }

    $.post(
        "users_manager.php",
        {
            resetpass: id
        },
        function(res){

            if(res === "OK"){
                alert("Đã đặt lại mật khẩu!");
            }else{
                alert(res);
            }

        }
    );
}
</script>
<?php } ?>
