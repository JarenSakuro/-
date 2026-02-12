<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$albumId = get_int('id', 0);
if ($albumId <= 0) { http_response_code(404); exit('Not Found'); }

$album = $pdo->prepare("
  SELECT id, title, description
  FROM albums
  WHERE id=? AND is_published=1
");
$album->execute([$albumId]);
$album = $album->fetch();
if (!$album) { http_response_code(404); exit('Not Found'); }

$title = $album['title'];
$bodyClass = 'page';

$photos = $pdo->prepare("
  SELECT id, file_path, thumb_path, description, shot_date, sort_order
  FROM photos
  WHERE album_id=? AND is_published=1
  ORDER BY shot_date DESC, sort_order ASC, id DESC
");
$photos->execute([$albumId]);
$photos = $photos->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <h1><?= h($album['title']) ?></h1>
  <?php if (!empty($album['description'])): ?>
    <p class="muted"><?= h($album['description']) ?></p>
  <?php endif; ?>

  <div class="photo-grid">
    <?php foreach ($photos as $p): ?>
      <?php $img = $p['thumb_path'] ?: $p['file_path']; ?>
      <a class="photo-tile" href="/photo.php?id=<?= (int)$p['id'] ?>">
        <img src="<?= h($img) ?>" alt="" loading="lazy">
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>