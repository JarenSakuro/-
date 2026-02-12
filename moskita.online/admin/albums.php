<?php
$title = '相册';
require __DIR__ . '/layout_top.php';

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) {
    $pdo->prepare("DELETE FROM albums WHERE id=?")->execute([$id]);
  }
  header('Location: /admin/albums.php');
  exit;
}

$albums = $pdo->query("SELECT * FROM albums ORDER BY sort_order ASC, id DESC")->fetchAll();
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <strong>相册列表</strong>
    <a class="btn primary" href="/admin/album_edit.php">新增相册</a>
  </div>

  <table style="margin-top:12px;">
    <tr><th>ID</th><th>标题</th><th>排序</th><th>发布</th><th>操作</th></tr>
    <?php foreach ($albums as $a): ?>
      <tr>
        <td><?= (int)$a['id'] ?></td>
        <td><?= h($a['title']) ?></td>
        <td><?= (int)$a['sort_order'] ?></td>
        <td><?= (int)$a['is_published'] ?></td>
        <td class="actions">
          <a href="/admin/album_edit.php?id=<?= (int)$a['id'] ?>">编辑</a>
          <a href="/admin/albums.php?delete=<?= (int)$a['id'] ?>" onclick="return confirm('确定删除？');">删除</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>