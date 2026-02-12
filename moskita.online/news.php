<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$title = '阮鸿新闻';
$bodyClass = 'page';

$news = $pdo->query("
  SELECT id, title, source_name, url, cover_photo_url, publish_date
  FROM news_links
  WHERE is_published=1
  ORDER BY sort_order ASC, publish_date DESC, id DESC
  LIMIT 100
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <h1>阮鸿新闻</h1>

  <div class="news-list">
    <?php foreach ($news as $n): ?>
      <a class="news-item" href="<?= h($n['url']) ?>" target="_blank" rel="noopener">
        <?php if (!empty($n['cover_photo_url'])): ?>
          <img class="news-cover" src="<?= h($n['cover_photo_url']) ?>" alt="" loading="lazy">
        <?php endif; ?>
        <div class="news-meta">
          <div class="news-title"><?= h($n['title']) ?></div>
          <div class="muted">
            <?= h($n['source_name'] ?: '') ?>
            <?php if (!empty($n['publish_date'])): ?>
              <span class="sep">·</span><?= h(date('Y.m.d', strtotime($n['publish_date']))) ?>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>