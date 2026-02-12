<?php
$title = '照片';
require __DIR__ . '/layout_top.php';

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) $pdo->prepare("DELETE FROM photos WHERE id=?")->execute([$id]);
  header('Location: /admin/photos.php');
  exit;
}

$photos = $pdo->query("
  SELECT p.id, p.file_path, p.thumb_path, p.shot_date, p.is_published, a.title AS album_title
  FROM photos p JOIN albums a ON a.id=p.album_id
  ORDER BY p.id DESC
  LIMIT 200
")->fetchAll();
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <strong>照片（最近200条）</strong>
    <a class="btn primary" href="/admin/photo_edit.php">上传照片</a>
  </div>

  <table style="margin-top:12px;">
    <tr><th>ID</th><th>相册</th><th>拍摄日期</th><th>发布</th><th>路径</th><th>操作</th></tr>
    <?php foreach ($photos as $p): ?>
      <tr>
        <td><?= (int)$p['id'] ?></td>
        <td><?= h($p['album_title']) ?></td>
        <td><?= h($p['shot_date'] ?? '') ?></td>
        <td><?= (int)$p['is_published'] ?></td>
        <td class="muted"><?= h($p['thumb_path'] ?: $p['file_path']) ?></td>
        <td class="actions">
          <a href="/admin/photo_edit.php?id=<?= (int)$p['id'] ?>">编辑</a>
          <a href="/admin/photos.php?delete=<?= (int)$p['id'] ?>" onclick="return confirm('确定删除？');">删除</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>