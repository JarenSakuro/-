<?php
$title = '新闻';
require __DIR__ . '/layout_top.php';

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) $pdo->prepare("DELETE FROM news_links WHERE id=?")->execute([$id]);
  header('Location: /admin/news.php');
  exit;
}

$news = $pdo->query("
  SELECT id, title, source_name, url, publish_date, sort_order, is_published
  FROM news_links
  ORDER BY sort_order ASC, publish_date DESC, id DESC
  LIMIT 300
")->fetchAll();
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <strong>新闻链接</strong>
    <a class="btn primary" href="/admin/news_edit.php">新增新闻</a>
  </div>

  <table style="margin-top:12px;">
    <tr>
      <th>ID</th><th>标题</th><th>来源</th><th>日期</th><th>排序</th><th>发布</th><th>操作</th>
    </tr>
    <?php foreach ($news as $n): ?>
      <tr>
        <td><?= (int)$n['id'] ?></td>
        <td>
          <div><?= h($n['title']) ?></div>
          <div class="muted" style="margin-top:4px;word-break:break-all;"><?= h($n['url']) ?></div>
        </td>
        <td><?= h($n['source_name'] ?? '') ?></td>
        <td><?= h($n['publish_date'] ?? '') ?></td>
        <td><?= (int)$n['sort_order'] ?></td>
        <td><?= (int)$n['is_published'] ?></td>
        <td class="actions">
          <a href="/admin/news_edit.php?id=<?= (int)$n['id'] ?>">编辑</a>
          <a href="/admin/news.php?delete=<?= (int)$n['id'] ?>" onclick="return confirm('确定删除？');">删除</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>