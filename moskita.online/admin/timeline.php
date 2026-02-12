<?php
$title = '时间线';
require __DIR__ . '/layout_top.php';

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) $pdo->prepare("DELETE FROM timeline_items WHERE id=?")->execute([$id]);
  header('Location: /admin/timeline.php');
  exit;
}

$items = $pdo->query("
  SELECT t.id, t.title, t.event_date, t.same_day_order, t.is_published, t.photo_id
  FROM timeline_items t
  ORDER BY t.event_date DESC, t.same_day_order ASC, t.id DESC
  LIMIT 300
")->fetchAll();
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <strong>时间线</strong>
    <a class="btn primary" href="/admin/timeline_edit.php">新增条目</a>
  </div>

  <table style="margin-top:12px;">
    <tr><th>ID</th><th>日期</th><th>标题</th><th>同日序</th><th>photo_id</th><th>发布</th><th>操作</th></tr>
    <?php foreach ($items as $t): ?>
      <tr>
        <td><?= (int)$t['id'] ?></td>
        <td><?= h($t['event_date']) ?></td>
        <td><?= h($t['title']) ?></td>
        <td><?= (int)$t['same_day_order'] ?></td>
        <td><?= (int)$t['photo_id'] ?></td>
        <td><?= (int)$t['is_published'] ?></td>
        <td class="actions">
          <a href="/admin/timeline_edit.php?id=<?= (int)$t['id'] ?>">编辑</a>
          <a href="/admin/timeline.php?delete=<?= (int)$t['id'] ?>" onclick="return confirm('确定删除？');">删除</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>