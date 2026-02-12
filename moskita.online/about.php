<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$stmt = $pdo->prepare("SELECT title, body FROM pages WHERE slug='about' LIMIT 1");
$stmt->execute();
$page = $stmt->fetch();

$title = $page ? $page['title'] : '阮鸿简介';
$bodyClass = 'page';

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <h1><?= h($title) ?></h1>
  <div class="rich">
    <?= $page ? $page['body'] : '<p class="muted">暂无内容</p>' ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>