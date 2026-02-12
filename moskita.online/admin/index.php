<?php
$title = '控制台';
require __DIR__ . '/layout_top.php';

$counts = [
  'albums' => (int)$pdo->query("SELECT COUNT(*) c FROM albums")->fetch()['c'],
  'photos' => (int)$pdo->query("SELECT COUNT(*) c FROM photos")->fetch()['c'],
  'timeline' => (int)$pdo->query("SELECT COUNT(*) c FROM timeline_items")->fetch()['c'],
  'news' => (int)$pdo->query("SELECT COUNT(*) c FROM news_links")->fetch()['c'],
];
?>
<h1>控制台</h1>
<div class="card">
  <div>相册：<?= $counts['albums'] ?> / 照片：<?= $counts['photos'] ?> / 时间线：<?= $counts['timeline'] ?> / 新闻：<?= $counts['news'] ?></div>
  <div class="muted" style="margin-top:6px;">上传图片目录：/uploads/photos/（确保可写）</div>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>