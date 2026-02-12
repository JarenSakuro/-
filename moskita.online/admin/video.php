<?php
$title = '开场视频';
require __DIR__ . '/layout_top.php';

function save_video(string $field, string $absDir, string $publicPrefix): ?string {
  if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
  $tmp = $_FILES[$field]['tmp_name'];
  $name = $_FILES[$field]['name'] ?? 'video.mp4';
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

  // 允许的格式：mp4/webm/ogg
  if (!in_array($ext, ['mp4','webm','ogg'])) return null;

  if (!is_dir($absDir)) @mkdir($absDir, 0775, true);
  $filename = 'intro_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
  $absPath = rtrim($absDir, '/') . '/' . $filename;

  if (!move_uploaded_file($tmp, $absPath)) return null;

  return rtrim($publicPrefix, '/') . '/' . $filename;
}

$st = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key='intro_video_path' LIMIT 1");
$st->execute();
$current = $st->fetchColumn() ?: '/assets/video/intro.mp4';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uploaded = save_video('video_file', $_SERVER['DOCUMENT_ROOT'] . '/uploads/video', '/uploads/video');

  if (!$uploaded) {
    $err = '上传失败：请确认格式为 mp4/webm/ogg，并检查 /uploads/video 写入权限。';
  } else {
    $pdo->prepare("
      INSERT INTO site_settings (setting_key, setting_value)
      VALUES ('intro_video_path', ?)
      ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)
    ")->execute([$uploaded]);

    $current = $uploaded;
    $msg = '已更新开场视频。';
  }
}
?>
<div class="card">
  <strong>开场全屏无声视频</strong>
  <div class="muted" style="margin-top:8px;">前台首页自动播放（muted + loop）。</div>

  <?php if ($msg): ?><div style="margin-top:10px;"><?= h($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div style="margin-top:10px;color:#ffb3b3;"><?= h($err) ?></div><?php endif; ?>

  <div style="margin-top:12px;" class="muted">当前路径：<?= h($current) ?></div>

  <div style="margin-top:12px;">
    <video src="<?= h($current) ?>" muted loop controls style="max-width:520px;border-radius:10px;border:1px solid rgba(255,255,255,.15);"></video>
  </div>

  <form method="post" enctype="multipart/form-data" style="margin-top:12px;">
    <label>上传新视频（mp4/webm/ogg）</label>
    <input type="file" name="video_file" accept=".mp4,.webm,.ogg" required />
    <div style="margin-top:14px;">
      <button class="btn primary" type="submit">上传并替换</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/layout_bottom.php'; ?>