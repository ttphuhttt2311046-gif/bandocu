<?php
session_start();
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:;");
include "../db.php";
require_once "../admin/validation.php";

// Nếu chưa đăng nhập thì quay về login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];

// Kiểm tra xem bảng taikhoan có cột avatar không
$hasAvatarCol = false;
$colCheck = $conn->query("SHOW COLUMNS FROM taikhoan LIKE 'avatar'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasAvatarCol = true;
}

// Lấy thông tin cũ
$sql = "SELECT tenNguoiDung, soDienThoai, diaChi" . ($hasAvatarCol ? ", avatar" : "") . " FROM taikhoan WHERE maTaiKhoan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Đường dẫn lưu avatar
$uploadDir = "../assets/img/avatars/";
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$profileError = null;
$passwordError = null;
$passwordSuccess = null;
$avatarPath = $user['avatar'] ?? "assets/img/avatars/default.png";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenNguoiDung = trim($_POST["tenNguoiDung"] ?? "");
    $soDienThoai = trim($_POST["soDienThoai"] ?? "");
    $diaChi = trim($_POST["diaChi"] ?? "");

    $oldPassword = $_POST["oldPassword"] ?? "";
    $newPassword = $_POST["newPassword"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    // Validate thông tin cá nhân
    if (!preg_match('/^[0-9]{10}$/', $soDienThoai)) {
        $profileError = "Không nhập chữ và nhập đúng 10 chữ số";
    }

    // Validate đổi mật khẩu nếu có nhập bất kỳ trường nào
    $passwordChangeRequested = ($oldPassword !== "" || $newPassword !== "" || $confirmPassword !== "");
    if ($passwordChangeRequested) {
        $currentUser = $conn->prepare("SELECT matKhau FROM taikhoan WHERE maTaiKhoan = ?");
        $currentUser->bind_param("i", $id);
        $currentUser->execute();
        $currentUserResult = $currentUser->get_result();
        $currentUserData = $currentUserResult->fetch_assoc();
        $currentUser->close();

        $storedHash = $currentUserData['matKhau'] ?? "";

        if (empty($oldPassword)) {
            $passwordError = "Vui lòng nhập mật khẩu cũ.";
        } elseif (!password_verify($oldPassword, $storedHash) && $oldPassword !== $storedHash) {
            $passwordError = "Mật khẩu cũ không đúng.";
        } elseif (empty($newPassword)) {
            $passwordError = "Vui lòng nhập mật khẩu mới.";
        } elseif (!validPassword($newPassword)) {
            $passwordError = "Mật khẩu phải từ 8 ký tự, có ít nhất 1 chữ hoa, 1 chữ số và 1 ký tự đặc biệt.";
        } elseif (empty($confirmPassword)) {
            $passwordError = "Vui lòng nhập lại mật khẩu mới.";
        } elseif ($newPassword !== $confirmPassword) {
            $passwordError = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        }
    }

    // Nếu không có lỗi thì xử lý lưu
    if ($profileError === null && $passwordError === null) {
    $avatarPath = $user['avatar'] ?? "assets/img/avatars/default.png";
    $avatarUploadedPath = isset($_POST['avatar_uploaded']) ? trim((string) $_POST['avatar_uploaded']) : "";

    // Nếu client đã upload trước và gửi lại đường dẫn server
    if ($avatarUploadedPath !== "") {
        $avatarPath = $avatarUploadedPath;
    }

    // Xử lý avatar từ crop base64
    if (!empty($_POST['avatar_data'])) {
        $data = $_POST['avatar_data'];
        if (preg_match('/^data:image\/(png|jpeg|webp);base64,/i', $data, $m)) {
            $mime = strtolower($m[1]);
            $ext = $mime === 'jpeg' ? 'jpg' : $mime;
            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

            if ($imgData === false) {
                $profileError = "Dữ liệu ảnh không hợp lệ.";
            } else {
                $filename = "user_{$id}." . $ext;
                $dest = $uploadDir . $filename;

                if (file_put_contents($dest, $imgData) !== false) {
                    $publicPath = "assets/img/avatars/" . $filename;
                    $avatarPath = $publicPath;
                } else {
                    $profileError = "Không lưu được ảnh.";
                }
            }
        } else {
            $profileError = "Định dạng ảnh không được hỗ trợ.";
        }
    }

    // Xử lý file upload truyền thống
    if ($profileError === null && $avatarUploadedPath === "" && isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['avatar_file'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $maxSize = 2 * 1024 * 1024;
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mime, $allowed)) {
                $profileError = "Chỉ cho phép ảnh PNG/JPG/WebP";
            } elseif ($f['size'] > $maxSize) {
                $profileError = "Kích thước ảnh tối đa 2MB";
            } else {
                $ext = $allowed[$mime];
                $filename = "user_{$id}." . $ext;
                $dest = $uploadDir . $filename;

                if (is_uploaded_file($f['tmp_name']) && move_uploaded_file($f['tmp_name'], $dest)) {
                    $publicPath = "assets/img/avatars/" . $filename;
                    $avatarPath = $publicPath;
                } else {
                    $profileError = "Không lưu được ảnh, thử lại.";
                }
            }
        } else {
            $profileError = "Lỗi upload ảnh (code " . $f['error'] . ")";
        }
    }

        // Lưu profile nếu không lỗi
        if ($profileError === null) {
            if ($hasAvatarCol) {
                $update = $conn->prepare("UPDATE taikhoan SET tenNguoiDung=?, soDienThoai=?, diaChi=?, avatar=? WHERE maTaiKhoan=?");
                $update->bind_param("ssssi", $tenNguoiDung, $soDienThoai, $diaChi, $avatarPath, $id);
            } else {
                $update = $conn->prepare("UPDATE taikhoan SET tenNguoiDung=?, soDienThoai=?, diaChi=? WHERE maTaiKhoan=?");
                $update->bind_param("sssi", $tenNguoiDung, $soDienThoai, $diaChi, $id);
            }

            if ($update->execute()) {
                $_SESSION['tenNguoiDung'] = $tenNguoiDung;
                $_SESSION['soDienThoai'] = $soDienThoai;
                $_SESSION['diaChi'] = $diaChi;
                if (!empty($avatarPath)) {
                    $_SESSION['avatar'] = $avatarPath;
                }

                // Cập nhật mật khẩu nếu có yêu cầu
                if ($passwordChangeRequested) {
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
                    $pwStmt = $conn->prepare("UPDATE taikhoan SET matKhau = ? WHERE maTaiKhoan = ?");
                    $pwStmt->bind_param("si", $hashedNewPassword, $id);
                    $pwStmt->execute();
                    $pwStmt->close();
                    $passwordSuccess = "Đổi mật khẩu thành công.";
                }

                echo "<script>
    alert('" . ($passwordSuccess ?: "Cập nhật thành công") . "');
    window.location='ttnguoidung.php?v=" . time() . "';
</script>";
                exit;
            } else {
                $profileError = "Lỗi khi cập nhật dữ liệu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin người dùng</title>
<link rel="stylesheet" href="../assets/css/register.css">
<style>
  .profile-row{display:flex;align-items:center;gap:16px;margin-bottom:12px;}
  .avatar-preview{width:96px;height:96px;border-radius:50%;object-fit:cover;border:2px solid #eee;background:#fff}
  .file-input{display:block;margin-top:6px}
  .hint{font-size:13px;color:#666;margin-top:6px}
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.6);display:none;align-items:center;justify-content:center;z-index:99999 !important;}
  .crop-card{background:#fff;padding:12px;border-radius:12px;max-width:720px;width:90%;display:flex;gap:12px;align-items:center}
  .crop-area{position:relative;width:360px;height:360px;background:#222;border-radius:8px;overflow:hidden;touch-action:none}
  .crop-canvas{display:block;width:100%;height:100%;cursor:grab}
  .crop-overlay{position:absolute;inset:0;border-radius:50%;box-shadow:0 0 0 9999px rgba(0,0,0,0.45);pointer-events:none;}
  .controls{flex:1}
  .controls .row{margin-bottom:10px}
  .btn{padding:8px 12px;border-radius:8px;border:0;cursor:pointer;margin-right:8px}
  .btn-save{background:#2f903f;color:#fff}
  .btn-close{background:#eee}
  .controls label{font-weight:bold}
  .controls input[type="range"]{width:100%;margin-top:6px}

  .requirements-box{margin:10px 0 16px;display:grid;gap:6px}
  .req-item{font-size:13px;padding:3px 0}
  .req-item.valid{color:#0a8f3f;font-weight:600}
  .req-item.invalid{color:#b42318;font-weight:600}
</style>
</head>
<body>
<div class="cakhoi">

<h2 class="h2">Thông tin cá nhân</h2>

<form method="post" enctype="multipart/form-data">

<div class="profile-row">
  <?php
    $curAvatar = $_SESSION['avatar'] ?? ($user['avatar'] ?? "assets/img/avatars/default.png");
    if (strpos($curAvatar, 'assets/') === 0) {
        $curAvatar = "../" . $curAvatar;
    }
    $curAvatar .= (strpos($curAvatar, '?') === false ? '?v=' : '&v=') . time();
?>
<img id="avatarPreview" src="<?= htmlspecialchars($curAvatar) ?>" alt="Avatar" class="avatar-preview">
  <div>
    <label for="avatar">Thay đổi avatar</label>
    <input type="file" name="avatar_file" id="avatar" accept="image/png,image/jpeg,image/webp" class="file-input" style="display:none">
    <input type="hidden" name="avatar_data" id="avatarData">
    <input type="hidden" name="avatar_uploaded" id="avatarUploaded" value="">
    <div class="hint">PNG/JPG/WebP — tối đa 2MB. Chọn ảnh vuông để hiển thị đẹp.</div>
    <div style="margin-top:8px;">
      <button type="button" id="btnChoose" style="padding:8px 12px;border-radius:8px;cursor:pointer;background:#f1c40f;color:#333;border:none;font-weight:bold">Chọn ảnh</button>
    </div>
  </div>
</div>

<div class="mk">
<label>Tên người dùng:</label>
<input type="text" name="tenNguoiDung" required value="<?php echo htmlspecialchars($user['tenNguoiDung'] ?? $_SESSION['tenNguoiDung'] ?? ''); ?>">
</div>

<div class="mk">
<label>Số điện thoại:</label>
<input type="text" name="soDienThoai" required value="<?php echo htmlspecialchars($user['soDienThoai'] ?? $_SESSION['soDienThoai'] ?? ''); ?>">
</div>

<div class="mk">
<label>Địa chỉ:</label>
<input type="text" name="diaChi" required value="<?php echo htmlspecialchars($user['diaChi'] ?? $_SESSION['diaChi'] ?? ''); ?>">
</div>

<div class="mk">
    <label>Mật khẩu cũ:</label>
    <input type="password" name="oldPassword" id="oldPassword" placeholder="Nhập mật khẩu cũ">
</div>

<div class="mk">
    <label>Mật khẩu mới:</label>
    <input type="password" name="newPassword" id="newPassword" placeholder="Nhập mật khẩu mới">
</div>

<div class="mk">
    <label>Xác nhận mật khẩu mới:</label>
    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Nhập lại mật khẩu mới">
</div>

<div id="password-requirements" class="requirements-box">
    <div id="req-length" class="req-item invalid">✕ Ít nhất 8 ký tự</div>
    <div id="req-uppercase" class="req-item invalid">✕ Ít nhất 1 chữ hoa</div>
    <div id="req-number" class="req-item invalid">✕ Ít nhất 1 chữ số</div>
    <div id="req-special" class="req-item invalid">✕ Ít nhất 1 ký tự đặc biệt</div>
</div>

<div class="dangki" style="display:flex; gap:10px;">
    <button type="submit">Lưu thay đổi</button>
    <button type="button" onclick="window.location='../index.php'">Quay lại</button>
</div>

<?php if (isset($profileError)) echo "<p style='color:red; text-align:center;'>".htmlspecialchars($profileError)."</p>"; ?>
<?php if (isset($passwordError)) echo "<p style='color:red; text-align:center;'>".htmlspecialchars($passwordError)."</p>"; ?>
<?php if (isset($passwordSuccess)) echo "<p style='color:green; text-align:center;'>".htmlspecialchars($passwordSuccess)."</p>"; ?>

</form>
</div>

<div id="cropModal" style="display:none;">
  <div class="modal-backdrop" id="cropBackdrop" aria-hidden="true">
    <div class="crop-card" role="dialog" aria-modal="true" aria-label="Cắt ảnh avatar">
      <div class="crop-area" id="cropArea">
        <canvas id="cropCanvas" class="crop-canvas" width="720" height="720"></canvas>
        <div class="crop-overlay"></div>
      </div>
      <div class="controls">
        <div class="row">
          <label>Phóng to</label><br>
          <input type="range" id="zoomRange" min="0.5" max="3.0" step="0.01" value="1">
        </div>
        <div class="row">
          <button type="button" class="btn btn-save" id="btnSaveCrop">Lưu</button>
          <button type="button" class="btn btn-close" id="btnCancelCrop">Thoát</button>
          <button type="button" class="btn" id="btnRechoose">Chọn lại</button>
        </div>
        <p style="font-size:13px;color:#555;margin-top:8px">Kéo ảnh để di chuyển; dùng thanh zoom để phóng to/thu nhỏ. Lưu sẽ cập nhật ảnh preview.</p>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const fileInput = document.getElementById('avatar');
  const btnChoose = document.getElementById('btnChoose');
  const avatarPreview = document.getElementById('avatarPreview');
  const avatarUploadedInput = document.getElementById('avatarUploaded');
  const modal = document.getElementById('cropModal');
  const canvas = document.getElementById('cropCanvas');
  const ctx = canvas.getContext('2d');
  const zoomRange = document.getElementById('zoomRange');
  const btnSaveCrop = document.getElementById('btnSaveCrop');
  const btnCancelCrop = document.getElementById('btnCancelCrop');
  const btnRechoose = document.getElementById('btnRechoose');

  let img = new Image();
  let imgLoaded = false;
  let scale = 1;
  let pos = {x:0, y:0};
  let dragging = false, start = {x:0, y:0};
  const canvasSize = 720;

  let objectUrl = null;
  let previewUrl = null;

  function bustCache(url) {
  if (!url) return url;
  return url + (url.includes('?') ? '&' : '?') + 'v=' + Date.now();
}
  btnChoose.addEventListener('click', (e) => {
    e.preventDefault();
    fileInput.click();
  });

  const ZOOM_MIN = 0.1;
  const ZOOM_MAX = 3.0;
  zoomRange.min = ZOOM_MIN;
  zoomRange.max = ZOOM_MAX;
  zoomRange.step = 0.01;

  function render() {
    if (!imgLoaded) {
      ctx.clearRect(0, 0, canvasSize, canvasSize);
      return;
    }
    ctx.clearRect(0, 0, canvasSize, canvasSize);
    ctx.save();
    ctx.fillStyle = '#222';
    ctx.fillRect(0, 0, canvasSize, canvasSize);
    ctx.drawImage(img, pos.x, pos.y, img.width * scale, img.height * scale);
    ctx.restore();
  }

  zoomRange.addEventListener('input', () => {
    if (!imgLoaded) return;
    const newScale = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, parseFloat(zoomRange.value)));
    const cx = canvasSize / 2, cy = canvasSize / 2;
    const imgCenterX = (cx - pos.x) / scale;
    const imgCenterY = (cy - pos.y) / scale;
    pos.x = cx - imgCenterX * newScale;
    pos.y = cy - imgCenterY * newScale;
    scale = newScale;
    render();
  });

  // Hàm mở modal (Đã chỉnh sửa để đặt aria-hidden = "false")
  function openModal() {
    modal.style.display = 'block';
    const bd = document.getElementById('cropBackdrop');
    if (bd) {
      bd.style.display = 'flex';
      bd.setAttribute('aria-hidden', 'false');
    }
  }

  // Hàm đóng modal (Đã chỉnh sửa để blur và đặt aria-hidden = "true")
  function closeModal() {
    if (document.activeElement) {
      document.activeElement.blur();
    }
    modal.style.display = 'none';
    const bd = document.getElementById('cropBackdrop');
    if (bd) {
      bd.style.display = 'none';
      bd.setAttribute('aria-hidden', 'true');
    }
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    fileInput.value = '';
    imgLoaded = false;
  }

  function openModalWithBlob(file) {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    objectUrl = URL.createObjectURL(file);

    img.onload = function() {
      imgLoaded = true;
      const initial = Math.max(canvasSize / img.width, canvasSize / img.height);
      scale = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, initial));
      pos.x = (canvasSize - img.width * scale) / 2;
      pos.y = (canvasSize - img.height * scale) / 2;
      zoomRange.value = scale;
      render();
      openModal();
    };
    img.onerror = function() {
      alert('Không thể mở ảnh.');
    };
    img.src = objectUrl;
  }

  function openModalWithUrl(url, fallbackFile) {
    img.onload = function() {
      imgLoaded = true;
      const initial = Math.max(canvasSize / img.width, canvasSize / img.height);
      scale = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, initial));
      pos.x = (canvasSize - img.width * scale) / 2;
      pos.y = (canvasSize - img.height * scale) / 2;
      zoomRange.value = scale;
      render();
      openModal();
    };
    img.onerror = function() {
      if (fallbackFile) openModalWithBlob(fallbackFile);
      else alert('Không load được ảnh từ server.');
    };
    img.src = url;
  }

  fileInput.addEventListener('change', function() {
    const f = this.files[0];
    if (!f) return;
    if (!f.type.startsWith('image/')) return alert('Chỉ chọn ảnh');

    avatarUploadedInput.value = '';
    if (previewUrl) {
      URL.revokeObjectURL(previewUrl);
      previewUrl = null;
    }

    const fd = new FormData();
    fd.append('avatar', f, 'orig.png');

    fetch('upload_avatar.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(j => {
        if (!j || !j.success) {
          alert('Upload thất bại: ' + (j && j.msg ? j.msg : 'Unknown'));
          return;
        }
        const url = bustCache('../' + j.path);
avatarUploadedInput.value = j.path;
openModalWithUrl(url, f);
      })
      .catch(() => {
        openModalWithBlob(f);
      });
  });

  const revokePreview = () => {
    if (previewUrl) {
      URL.revokeObjectURL(previewUrl);
      previewUrl = null;
    }
  };

  btnSaveCrop.addEventListener('click', () => {
    if (!imgLoaded) return;

    const outSize = 600;
    const tmp = document.createElement('canvas');
    tmp.width = outSize;
    tmp.height = outSize;
    const tctx = tmp.getContext('2d');
    const ratio = outSize / canvasSize;

    tctx.fillStyle = '#fff';
    tctx.fillRect(0, 0, outSize, outSize);
    tctx.beginPath();
    tctx.arc(outSize/2, outSize/2, outSize/2, 0, Math.PI*2);
    tctx.closePath();
    tctx.drawImage(img, pos.x * ratio, pos.y * ratio, img.width * scale * ratio, img.height * scale * ratio);

    tmp.toBlob(blob => {
      if (!blob) {
        alert('Không tạo được ảnh');
        return;
      }

      const fd = new FormData();
      fd.append('avatar', blob, 'avatar.png');

      fetch('upload_avatar.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => {
          if (!j || !j.success) {
            alert('Upload thất bại: ' + (j && j.msg ? j.msg : 'Unknown error'));
            return;
          }

          revokePreview();
previewUrl = bustCache('../' + j.path);
avatarPreview.src = previewUrl;
avatarUploadedInput.value = j.path;
closeModal();
        })
        .catch(err => {
          console.error(err);
          alert('Lỗi upload: ' + (err.message || err));
        });
    }, 'image/png', 0.92);
  });

  btnCancelCrop.addEventListener('click', () => {
    closeModal();
  });

  btnRechoose.addEventListener('click', () => {
    avatarUploadedInput.value = '';
    revokePreview();
    fileInput.value = '';
    fileInput.click();
  });

  canvas.addEventListener('mousedown', (e) => {
    e.preventDefault();
    dragging = true;
    start.x = e.clientX;
    start.y = e.clientY;
    canvas.style.cursor = 'grabbing';
  });

  window.addEventListener('mousemove', (e) => {
    if (!dragging) return;
    e.preventDefault();
    const dx = e.clientX - start.x;
    const dy = e.clientY - start.y;
    const rect = canvas.getBoundingClientRect();
    const scaleFactor = canvas.width / rect.width;
    pos.x += dx * scaleFactor;
    pos.y += dy * scaleFactor;
    start.x = e.clientX;
    start.y = e.clientY;
    render();
  });

  window.addEventListener('mouseup', () => {
    if (!dragging) return;
    dragging = false;
    canvas.style.cursor = 'grab';
  });

  canvas.addEventListener('touchstart', (e) => {
    e.preventDefault();
    dragging = true;
    const t = e.touches[0];
    start.x = t.clientX;
    start.y = t.clientY;
    canvas.style.cursor = 'grabbing';
  }, {passive: false});

  canvas.addEventListener('touchmove', (e) => {
    if (!dragging) return;
    e.preventDefault();
    const t = e.touches[0];
    const dx = t.clientX - start.x;
    const dy = t.clientY - start.y;
    const rect = canvas.getBoundingClientRect();
    const scaleFactor = canvas.width / rect.width;
    pos.x += dx * scaleFactor;
    pos.y += dy * scaleFactor;
    start.x = t.clientX;
    start.y = t.clientY;
    render();
  }, {passive: false});

  canvas.addEventListener('touchend', () => {
    dragging = false;
    canvas.style.cursor = 'grab';
  });

  canvas.width = canvasSize;
  canvas.height = canvasSize;

})();

function checkPasswordRealtime() {
    const password = document.getElementById('newPassword').value;

    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    if (password.length >= 8) {
        reqLength.classList.remove('invalid');
        reqLength.classList.add('valid');
        reqLength.innerHTML = '✓ Ít nhất 8 ký tự';
    } else {
        reqLength.classList.remove('valid');
        reqLength.classList.add('invalid');
        reqLength.innerHTML = '✕ Ít nhất 8 ký tự';
    }

    if (/[A-Z]/.test(password)) {
        reqUppercase.classList.remove('invalid');
        reqUppercase.classList.add('valid');
        reqUppercase.innerHTML = '✓ Ít nhất 1 chữ hoa';
    } else {
        reqUppercase.classList.remove('valid');
        reqUppercase.classList.add('invalid');
        reqUppercase.innerHTML = '✕ Ít nhất 1 chữ hoa';
    }

    if (/[0-9]/.test(password)) {
        reqNumber.classList.remove('invalid');
        reqNumber.classList.add('valid');
        reqNumber.innerHTML = '✓ Ít nhất 1 chữ số';
    } else {
        reqNumber.classList.remove('valid');
        reqNumber.classList.add('invalid');
        reqNumber.innerHTML = '✕ Ít nhất 1 chữ số';
    }

    if (/[^a-zA-Z0-9]/.test(password)) {
        reqSpecial.classList.remove('invalid');
        reqSpecial.classList.add('valid');
        reqSpecial.innerHTML = '✓ Ít nhất 1 ký tự đặc biệt';
    } else {
        reqSpecial.classList.remove('valid');
        reqSpecial.classList.add('invalid');
        reqSpecial.innerHTML = '✕ Ít nhất 1 ký tự đặc biệt';
    }
}

document.getElementById('newPassword').addEventListener('input', checkPasswordRealtime);
</script>
</body>
</html>