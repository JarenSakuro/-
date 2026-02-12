<?php
$title = '编辑时间线';
require __DIR__ . '/layout_top.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$item = ['photo_id'=>0,'title'=>'','excerpt'=>'','event_date'=>date('Y-m-d'),'same_day_order'=>0,'is_published'=>1];
if ($id > 0) {
  $st = $pdo->prepare("SELECT * FROM timeline_items WHERE id=?");
  $st->execute([$id]);
  $item = $st->fetch();
  if (!$item) { echo "<div class='card'>Not Found</div>"; require __DIR__ . '/layout_bottom.php'; exit; }
}

$photos = $pdo->query("
  SELECT p.id, p.thumb_path, p.file_path, a.title AS album_title
  FROM photos p JOIN albums a ON a.id=p.album_id
  ORDER BY p.id DESC
  LIMIT 400
")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $photoId = (int)($_POST['photo_id'] ?? 0);
  $titleV  = trim($_POST['title'] ?? '');
  $excerpt = trim($_POST['excerpt'] ?? '');
  $dateV   = trim($_POST['event_date'] ?? '');
  $orderV  = (int)($_POST['same_day_order'] ?? 0);
  $pubV    = isset($_POST['is_published']) ? 1 : 0;

  if ($photoId <= 0) $error = '必须选择照片';
  elseif ($titleV === '') $error = '标题不能为空';
  elseif ($dateV === '') $error = '日期不能为空';

  if ($error === '') {
    if ($id > 0) {
      $pdo->prepare("UPDATE timeline_items SET photo_id=?, title=?, excerpt=?, event_date=?, same_day_order=?, is_published=? WHERE id=?")
          ->execute([$photoId, $titleV, $excerpt, $dateV, $orderV, $pubV, $id]);
    } else {
      $pdo->prepare("INSERT INTO timeline_items (photo_id, title, excerpt, event_date, same_day_order, is_published)
                     VALUES (?,?,?,?,?,?)")
          ->execute([$photoId, $titleV, $excerpt, $dateV, $orderV, $pubV]);
    }
    header('Location: /admin/timeline.php');
    exit;
  }
}
?>
<div class="card">
  <?php if ($error): ?><div style="color:#ffb3b3;margin-bottom:10px;"><?= h($error) ?></div><?php endif; ?>

  <form method="post">
    <label>选择照片</label>
    <select name="photo_id" required>
      <option value="">请选择</option>
      <?php foreach ($photos as $p): ?>
        <option value="<?= (int)$p['id'] ?>" <?= ((int)$item['photo_id']===(int)$p['id']?'selected':'') ?>>
          #<?= (int)$p['id'] ?> [<?= h($p['album_title']) ?>] <?= h($p['thumb_path'] ?: $p['file_path']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>标题</label>
    <input name="title" value="<?= h($item['title']) ?>" required />

    <label>摘要（可选）</label>
    <textarea name="excerpt" rows="3"><?= h($item['excerpt'] ?? '') ?></textarea>

    <div class="row">
      <div>
        <label>事件日期（YYYY-MM-DD）</label>
        <input name="event_date" value="<?= h($item['event_date']) ?>" required />
      </div>
      <div>
        <label>同日排序（小的在前）</label>
        <input name="same_day_order" type="number" value="<?= (int)$item['same_day_order'] ?>" />
      </div>
    </div>

    <label style="margin-top:12px;">
      <input type="checkbox" name="is_published" <?= ((int)$item['is_published']===1?'checked':'') ?> style="width:auto;">
      发布
    </label>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button class="btn primary" type="submit">保存</button>
      <a class="btn" href="/admin/timeline.php">返回</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>