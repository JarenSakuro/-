<?php
$title = '上传/编辑照片';
require __DIR__ . '/layout_top.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$photo = ['album_id'=>0,'file_path'=>'','thumb_path'=>'','description'=>'','shot_date'=>null,'sort_order'=>0,'is_published'=>1];
if ($id > 0) {
  $st = $pdo->prepare("SELECT * FROM photos WHERE id=?");
  $st->execute([$id]);
  $photo = $st->fetch();
  if (!$photo) { echo "<div class='card'>Not Found</div>"; require __DIR__ . '/layout_bottom.php'; exit; }
}

$albums = $pdo->query("SELECT id, title FROM albums ORDER BY sort_order ASC, id DESC")->fetchAll();

function save_upload(string $field, string $absDir, string $publicPrefix): ?string {
  if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
  $tmp = $_FILES[$field]['tmp_name'];
  $name = $_FILES[$field]['name'] ?? 'file';
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) return null;

  if (!is_dir($absDir)) @mkdir($absDir, 0775, true);
  $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
  $absPath = rtrim($absDir, '/') . '/' . $filename;

  if (!move_uploaded_file($tmp, $absPath)) return null;

  return rtrim($publicPrefix, '/') . '/' . $filename;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $albumId = (int)($_POST['album_id'] ?? 0);
  $desc    = trim($_POST['description'] ?? '');
  $shot    = ($_POST['shot_date'] ?? '') ?: null;
  $sort    = (int)($_POST['sort_order'] ?? 0);
  $pub     = isset($_POST['is_published']) ? 1 : 0;

  $uploadPhoto = save_upload('photo_file', $_SERVER['DOCUMENT_ROOT'] . '/uploads/photos', '/uploads/photos');
  $uploadThumb = save_upload('thumb_file', $_SERVER['DOCUMENT_ROOT'] . '/uploads/thumbs', '/uploads/thumbs');

  $filePath  = $uploadPhoto ?: ($photo['file_path'] ?? '');
  $thumbPath = $uploadThumb ?: ($photo['thumb_path'] ?? '');

  if ($albumId <= 0) $error = '必须选择相册';
  elseif ($filePath === '') $error = '必须上传图片文件（或编辑时保留原图）';

  if ($error === '') {
    if ($id > 0) {
      $pdo->prepare("UPDATE photos SET album_id=?, file_path=?, thumb_path=?, description=?, shot_date=?, sort_order=?, is_published=? WHERE id=?")
          ->execute([$albumId, $filePath, $thumbPath, $desc, $shot, $sort, $pub, $id]);
    } else {
      $pdo->prepare("INSERT INTO photos (album_id, file_path, thumb_path, description, shot_date, sort_order, is_published)
                     VALUES (?,?,?,?,?,?,?)")
          ->execute([$albumId, $filePath, $thumbPath, $desc, $shot, $sort, $pub]);
    }
    header('Location: /admin/photos.php');
    exit;
  }
}
?>
<div class="card">
  <?php if ($error): ?><div style="color:#ffb3b3;margin-bottom:10px;"><?= h($error) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>所属相册</label>
    <select name="album_id" required>
      <option value="">请选择</option>
      <?php foreach ($albums as $a): ?>
        <option value="<?= (int)$a['id'] ?>" <?= ((int)$photo['album_id']===(int)$a['id']?'selected':'') ?>>
          #<?= (int)$a['id'] ?> <?= h($a['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>上传图片（新建时必选）</label>
    <input type="file" name="photo_file" accept=".jpg,.jpeg,.png,.webp,.gif"/>

    <label>上传缩略图（可选）</label>
    <input type="file" name="thumb_file" accept=".jpg,.jpeg,.png,.webp,.gif"/>

    <?php if (!empty($photo['file_path'])): ?>
      <div class="muted" style="margin-top:10px;">当前图片：<?= h($photo['file_path']) ?></div>
      <img src="<?= h($photo['file_path']) ?>" style="max-width:260px;border-radius:10px;border:1px solid rgba(255,255,255,.15);margin-top:10px;">
    <?php endif; ?>

    <div class="row">
      <div>
        <label>拍摄日期（可选，YYYY-MM-DD）</label>
        <input name="shot_date" value="<?= h($photo['shot_date'] ?? '') ?>" placeholder="2026-01-28"/>
      </div>
      <div>
        <label>排序（可选）</label>
        <input name="sort_order" type="number" value="<?= (int)$photo['sort_order'] ?>"/>
      </div>
    </div>

    <label>描述（可选）</label>
    <textarea name="description" rows="4"><?= h($photo['description'] ?? '') ?></textarea>

    <label style="margin-top:12px;">
      <input type="checkbox" name="is_published" <?= ((int)$photo['is_published']===1?'checked':'') ?> style="width:auto;">
      发布
    </label>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button class="btn primary" type="submit">保存</button>
      <a class="btn" href="/admin/photos.php">返回</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>