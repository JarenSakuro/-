<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$title = '阮鸿视界';
$bodyClass = 'page';

$albums = $pdo->query("
  SELECT a.id, a.title, a.description, a.sort_order,
         cp.file_path AS cover_path, cp.thumb_path AS cover_thumb
  FROM albums a
  LEFT JOIN photos cp ON cp.id = a.cover_photo_id
  WHERE a.is_published=1
  ORDER BY a.sort_order ASC, a.id DESC
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <h1>阮鸿视界</h1>

  <?php if (!$albums): ?>
    <p class="muted">暂无相册（请先在后台新增相册并上传照片）。</p>
  <?php else: ?>
    <div class="album-grid">
      <?php foreach ($albums as $a): ?>
        <?php $cover = $a['cover_thumb'] ?: $a['cover_path'] ?: ''; ?>
        <a class="album-card" href="/album.php?id=<?= (int)$a['id'] ?>">
          <div class="album-cover">
            <?php if ($cover): ?>
              <img src="<?= h($cover) ?>" alt="<?= h($a['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="album-cover-placeholder"></div>
            <?php endif; ?>
          </div>
          <div class="album-title"><?= h($a['title']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>