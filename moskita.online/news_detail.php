<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$id = get_int('id', 0);
if ($id <= 0) { http_response_code(404); exit('Not Found'); }

$st = $pdo->prepare("
  SELECT id, title, excerpt, body, source_name, url, cover_photo_url, publish_date
  FROM news_links
  WHERE id=? AND is_published=1
  LIMIT 1
");
$st->execute([$id]);
$n = $st->fetch();
if (!$n) { http_response_code(404); exit('Not Found'); }

$title = $n['title'];
$bodyClass = 'page';

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <h1><?= h($n['title']) ?></h1>

  <div class="muted" style="margin-bottom:14px;">
    <?= h($n['source_name'] ?? '') ?>
    <?php if (!empty($n['publish_date'])): ?>
      <span class="sep">·</span><?= h(date('Y.m.d', strtotime($n['publish_date']))) ?>
    <?php endif; ?>
  </div>

  <?php if (!empty($n['cover_photo_url'])): ?>
    <img src="<?= h($n['cover_photo_url']) ?>" alt="" style="width:100%;max-width:980px;border-radius:14px;border:1px solid var(--line);margin-bottom:16px;">
  <?php endif; ?>

  <?php if (!empty($n['excerpt'])): ?>
    <div class="muted" style="margin-bottom:14px;line-height:1.7;"><?= h($n['excerpt']) ?></div>
  <?php endif; ?>

  <div class="rich">
    <?= !empty($n['body']) ? $n['body'] : '<p class="muted">暂无详情内容。</p>' ?>
  </div>

  <?php if (!empty($n['url'])): ?>
    <div style="margin-top:18px;">
      <a class="btn" href="<?= h($n['url']) ?>" target="_blank" rel="noopener">查看外部来源</a>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>