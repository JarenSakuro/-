<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$title = '阮鸿视界';
$bodyClass = 'home';
$st = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key='intro_video_path' LIMIT 1");
$st->execute();
$introVideoPath = $st->fetchColumn();
if (!$introVideoPath) $introVideoPath = '/assets/video/intro.mp4';

$timeline = $pdo->query("
  SELECT t.id, t.title, t.excerpt, t.event_date, t.same_day_order,
         p.id AS photo_id, p.file_path, p.thumb_path
  FROM timeline_items t
  JOIN photos p ON p.id = t.photo_id
  WHERE t.is_published=1 AND p.is_published=1
  ORDER BY t.event_date DESC, t.same_day_order ASC, t.id DESC
  LIMIT 12
")->fetchAll();

$albums = $pdo->query("
  SELECT a.id, a.title, a.description, a.sort_order,
         cp.file_path AS cover_path, cp.thumb_path AS cover_thumb
  FROM albums a
  LEFT JOIN photos cp ON cp.id = a.cover_photo_id
  WHERE a.is_published=1
  ORDER BY a.sort_order ASC, a.id DESC
  LIMIT 12
")->fetchAll();
$featuredNews = $pdo->query("
  SELECT id, title, excerpt, cover_photo_url, publish_date
  FROM news_links
  WHERE is_published=1 AND is_featured=1
  ORDER BY sort_order ASC, publish_date DESC, id DESC
  LIMIT 12
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero" id="hero">
  <video class="hero-video" id="introVideo"
         autoplay muted loop playsinline
         preload="auto"
         src="<?= h($introVideoPath) ?>"></video>

  <div class="hero-overlay" id="heroOverlay">
    <div class="hero-guide">
      <div class="hero-date"><?= date('Y.m.d') ?></div>
      <div class="hero-hint">滚动或点击进入</div>
    </div>
  </div>
</section>

<section class="content" id="content">
  <section class="timeline">
    <div class="section-title">时间线</div>

    <?php if (!$timeline): ?>
      <p class="muted">暂无时间线内容（请在数据库 timeline_items/photos 中添加）。</p>
    <?php else: ?>
      <div class="timeline-carousel" data-carousel>
        <button class="carousel-btn" data-prev type="button">‹</button>

        <div class="carousel-track" data-track>
          <?php foreach ($timeline as $item): ?>
            <?php
              $img = $item['thumb_path'] ?: $item['file_path'];
              $date = date('Y.m.d', strtotime($item['event_date']));
            ?>
            <article class="timeline-card">
              <a href="/photo.php?id=<?= (int)$item['photo_id'] ?>" class="timeline-media">
                <img src="<?= h($img) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
              </a>
              <div class="timeline-meta">
                <div class="timeline-date"><?= h($date) ?></div>
                <div class="timeline-title"><?= h($item['title']) ?></div>
                <?php if (!empty($item['excerpt'])): ?>
                  <div class="timeline-excerpt"><?= h($item['excerpt']) ?></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <button class="carousel-btn" data-next type="button">›</button>
      </div>
    <?php endif; ?>
  </section>
  <section class="news-featured">
    <div class="section-title">新闻</div>

    <?php if (!$featuredNews): ?>
      <p class="muted">暂无精选新闻（请在后台新闻里勾选“精选”）。</p>
    <?php else: ?>
      <div class="timeline-carousel" data-carousel>
        <button class="carousel-btn" data-prev type="button">‹</button>

        <div class="carousel-track" data-track>
          <?php foreach ($featuredNews as $n): ?>
            <article class="timeline-card">
              <a href="/news_detail.php?id=<?= (int)$n['id'] ?>" class="timeline-media">
                <?php if (!empty($n['cover_photo_url'])): ?>
                  <img src="<?= h($n['cover_photo_url']) ?>" alt="<?= h($n['title']) ?>" loading="lazy">
                <?php else: ?>
                  <div style="height:200px;background:rgba(255,255,255,.06);"></div>
                <?php endif; ?>
              </a>
              <div class="timeline-meta">
                <?php if (!empty($n['publish_date'])): ?>
                  <div class="timeline-date"><?= h(date('Y.m.d', strtotime($n['publish_date']))) ?></div>
                <?php endif; ?>
                <div class="timeline-title"><?= h($n['title']) ?></div>
                <?php if (!empty($n['excerpt'])): ?>
                  <div class="timeline-excerpt"><?= h($n['excerpt']) ?></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <button class="carousel-btn" data-next type="button">›</button>
      </div>
    <?php endif; ?>
  </section>
  <section class="albums">
    <div class="section-title">阮鸿素材库</div>

    <?php if (!$albums): ?>
      <p class="muted">暂无相册（请在数据库 albums/photos 中添加）。</p>
    <?php else: ?>
      <div class="album-grid">
        <?php foreach ($albums as $a): ?>
          <?php
            $cover = $a['cover_thumb'] ?: $a['cover_path'] ?: '';
          ?>
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
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>