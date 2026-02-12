<?php
$title = '编辑相册';
require __DIR__ . '/layout_top.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$album = ['title'=>'','description'=>'','sort_order'=>0,'is_published'=>1,'cover_photo_id'=>null];
if ($id > 0) {
  $st = $pdo->prepare("SELECT * FROM albums WHERE id=?");
  $st->execute([$id]);
  $album = $st->fetch();
  if (!$album) { echo "<div class='card'>Not Found</div>"; require __DIR__ . '/layout_bottom.php'; exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titleV = trim($_POST['title'] ?? '');
  $descV  = trim($_POST['description'] ?? '');
  $sortV  = (int)($_POST['sort_order'] ?? 0);
  $pubV   = isset($_POST['is_published']) ? 1 : 0;

  $coverRaw = $_POST['cover_photo_id'] ?? '';
  $coverId = ($coverRaw === '' ? null : (int)$coverRaw);

  if ($id > 0) {
    $pdo->prepare("UPDATE albums SET title=?, description=?, sort_order=?, is_published=?, cover_photo_id=? WHERE id=?")
        ->execute([$titleV, $descV, $sortV, $pubV, $coverId, $id]);
  } else {
    $pdo->prepare("INSERT INTO albums (title, description, sort_order, is_published, cover_photo_id) VALUES (?,?,?,?,?)")
        ->execute([$titleV, $descV, $sortV, $pubV, $coverId]);
    $id = (int)$pdo->lastInsertId();
  }

  header('Location: /admin/albums.php');
  exit;
}

$coverOptions = [];
if ($id > 0) {
  $st = $pdo->prepare("SELECT id, file_path, thumb_path FROM photos WHERE album_id=? ORDER BY id DESC LIMIT 200");
  $st->execute([$id]);
  $coverOptions = $st->fetchAll();
}
?>
<div class="card">
  <form method="post">
    <label>相册标题</label>
    <input name="title" value="<?= h($album['title']) ?>" required />

    <label>相册简介（可选）</label>
    <textarea name="description" rows="4"><?= h($album['description'] ?? '') ?></textarea>

    <div class="row">
      <div>
        <label>排序（小的在前）</label>
        <input name="sort_order" type="number" value="<?= (int)$album['sort_order'] ?>"/>
      </div>
      <div>
        <label>发布</label>
        <div style="margin-top:10px;">
          <label style="display:flex;gap:10px;align-items:center;margin:0;">
            <input type="checkbox" name="is_published" <?= ((int)$album['is_published']===1?'checked':'') ?> style="width:auto;">
            <span>对前台可见</span>
          </label>
        </div>
      </div>
    </div>

    <label>封面照片（可选：先上传照片后再回来选）</label>
    <select name="cover_photo_id">
      <option value="">不设置</option>
      <?php foreach ($coverOptions as $p): ?>
        <option value="<?= (int)$p['id'] ?>" <?= ((int)$album['cover_photo_id']===(int)$p['id']?'selected':'') ?>>
          #<?= (int)$p['id'] ?> <?= h($p['thumb_path'] ?: $p['file_path']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button class="btn primary" type="submit">保存</button>
      <a class="btn" href="/admin/albums.php">返回</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>