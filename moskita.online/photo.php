<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$photoId = get_int('id', 0);
if ($photoId <= 0) { http_response_code(404); exit('Not Found'); }

$stmt = $pdo->prepare("
  SELECT p.id, p.file_path, p.description, p.shot_date, p.album_id,
         a.title AS album_title
  FROM photos p
  JOIN albums a ON a.id = p.album_id
  WHERE p.id=? AND p.is_published=1 AND a.is_published=1
");
$stmt->execute([$photoId]);
$photo = $stmt->fetch();
if (!$photo) { http_response_code(404); exit('Not Found'); }

$title = '照片';
$bodyClass = 'page';

$tags = $pdo->prepare("
  SELECT t.id, t.name
  FROM photo_tags pt
  JOIN tags t ON t.id = pt.tag_id
  WHERE pt.photo_id=?
  ORDER BY t.name ASC
");
$tags->execute([$photoId]);
$tags = $tags->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-wrap">
  <div class="breadcrumb">
    <a href="/albums.php">阮鸿视界</a>
    <span class="sep">/</span>
    <a href="/album.php?id=<?= (int)$photo['album_id'] ?>"><?= h($photo['album_title']) ?></a>
  </div>

  <div class="photo-detail">
    <img class="photo-full" src="<?= h($photo['file_path']) ?>" alt="" />

    <div class="photo-info">
      <?php if (!empty($photo['shot_date'])): ?>
        <div class="muted"><?= h(date('Y.m.d', strtotime($photo['shot_date']))) ?></div>
      <?php endif; ?>

      <?php if (!empty($photo['description'])): ?>
        <div class="photo-desc"><?= nl2br(h($photo['description'])) ?></div>
      <?php endif; ?>

      <?php if ($tags): ?>
        <div class="tag-row">
          <?php foreach ($tags as $t): ?>
            <span class="tag"><?= h($t['name']) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>