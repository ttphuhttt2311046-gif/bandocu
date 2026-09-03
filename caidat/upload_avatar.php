<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success'=>false,'msg'=>'Not authenticated']);
  exit;
}
$id = (int)$_SESSION['user_id'];

$uploadDir = __DIR__ . '/../assets/img/avatars/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success'=>false,'msg'=>'No file uploaded']);
  exit;
}

$f = $_FILES['avatar'];
$maxSize = 2 * 1024 * 1024;
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $f['tmp_name']);
finfo_close($finfo);

if (!array_key_exists($mime, $allowed)) {
  echo json_encode(['success'=>false,'msg'=>'Invalid file type']);
  exit;
}
if ($f['size'] > $maxSize) {
  echo json_encode(['success'=>false,'msg'=>'File too large']);
  exit;
}

$ext = $allowed[$mime];
$filename = "user_{$id}." . $ext;
$dest = $uploadDir . $filename;

if (!move_uploaded_file($f['tmp_name'], $dest)) {
  echo json_encode(['success'=>false,'msg'=>'Move failed']);
  exit;
}

// public path relative to project root, adjust if needed
$publicPath = 'assets/img/avatars/' . $filename;
echo json_encode(['success'=>true,'path'=>$publicPath]);