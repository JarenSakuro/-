<?php
$title = '编辑新闻';
require __DIR__ . '/layout_top.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$item = [
  'title' => '',
  'excerpt' => '',
  'body' => '',
  'source_name' => '',
  'url' => '',
  'cover_photo_url' => '',
  'publish_date' => '',
  'sort_order' => 0,
  'is_published' => 1,
  'is_featured' => 0
];

if ($id > 0) {
  $st = $pdo->prepare("SELECT * FROM news_links WHERE id=?");
  $st->execute([$id]);
  $row = $st->fetch();
  if (!$row) {
    echo "<div class='card'>Not Found</div>";
    require __DIR__ . '/layout_bottom.php';
    exit;
  }
  $item = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titleV = trim($_POST['title'] ?? '');
  $excerptV = trim($_POST['excerpt'] ?? '');
  $bodyV = $_POST['body'] ?? '';

  $srcV   = trim($_POST['source_name'] ?? '');
  $urlV   = trim($_POST['url'] ?? '');
  $coverV = trim($_POST['cover_photo_url'] ?? '');
  $dateV  = trim($_POST['publish_date'] ?? '');
  $sortV  = (int)($_POST['sort_order'] ?? 0);

  $pubV = isset($_POST['is_published']) ? 1 : 0;
  $featuredV = isset($_POST['is_featured']) ? 1 : 0;

  if ($excerptV === '') $excerptV = null;
  if ($bodyV === '') $bodyV = null;
  if ($dateV === '') $dateV = null;
  if ($srcV === '') $srcV = null;
  if ($coverV === '') $coverV = null;

  // --- handle cover photo upload (optional) ---
  if (isset($_FILES['cover_photo_file']) && is_array($_FILES['cover_photo_file'])) {
    $f = $_FILES['cover_photo_file'];

    if ($f['error'] === UPLOAD_ERR_OK && $f['tmp_name'] !== '') {

      // 1) 基本校验：仅允许常见图片扩展名
      $allowedExt = ['jpg','jpeg','png','gif','webp'];
      $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, $allowedExt, true)) {
        echo "<div class='card'>上传失败：仅支持 " . implode(', ', $allowedExt) . "</div>";
        require __DIR__ . '/layout_bottom.php';
        exit;
      }

      // 2) 限制大小（5MB）
      $maxBytes = 5 * 1024 * 1024;
      if (!empty($f['size']) && $f['size'] > $maxBytes) {
        echo "<div class='card'>上传失败：图片不能超过 5MB</div>";
        require __DIR__ . '/layout_bottom.php';
        exit;
      }

      // 3) 保存目录：宝塔站点根目录 /uploads/photos
      $uploadDir = '/www/wwwroot/moskita.online/uploads/photos';

      if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
      }
      if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        echo "<div class='card'>上传失败：目录不可写 {$uploadDir}</div>";
        require __DIR__ . '/layout_bottom.php';
        exit;
      }

      // 4) 生成不重复文件名
      $filename = 'news_cover_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
      $destPath = rtrim($uploadDir, '/') . '/' . $filename;

      if (!move_uploaded_file($f['tmp_name'], $destPath)) {
        echo "<div class='card'>上传失败：保存文件失败</div>";
        require __DIR__ . '/layout_bottom.php';
        exit;
      }

      // 5) 写回数据库字段（Web 访问路径）
      $coverV = '/uploads/photos/' . $filename;
    }
  }
  // --- end upload ---

  if ($id > 0) {
    $pdo->prepare("
      UPDATE news_links
      SET title=?, excerpt=?, body=?, source_name=?, url=?, cover_photo_url=?, publish_date=?, sort_order=?, is_published=?, is_featured=?
      WHERE id=?
    ")->execute([$titleV, $excerptV, $bodyV, $srcV, $urlV, $coverV, $dateV, $sortV, $pubV, $featuredV, $id]);
  } else {
    $pdo->prepare("
      INSERT INTO news_links (title, excerpt, body, source_name, url, cover_photo_url, publish_date, sort_order, is_published, is_featured)
      VALUES (?,?,?,?,?,?,?,?,?,?)
    ")->execute([$titleV, $excerptV, $bodyV, $srcV, $urlV, $coverV, $dateV, $sortV, $pubV, $featuredV]);
  }

  header('Location: /admin/news.php');
  exit;
}
?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <label>标题</label>
    <input name="title" value="<?= h($item['title']) ?>" required />

    <label>摘要（首页卡片短文字，可选）</label>
    <textarea name="excerpt" rows="3"><?= h($item['excerpt'] ?? '') ?></textarea>

    <label>详情正文（站内详情页内容，支持 HTML，可选）</label>
    <textarea name="body" rows="10"><?= h($item['body'] ?? '') ?></textarea>

    <div class="row">
      <div>
        <label>来源名（可选）</label>
        <input name="source_name" value="<?= h($item['source_name'] ?? '') ?>" />
      </div>
      <div>
        <label>发布日期（可选，YYYY-MM-DD）</label>
        <input name="publish_date" value="<?= h($item['publish_date'] ?? '') ?>" placeholder="2026-01-28" />
      </div>
    </div>

    <label>跳转 URL（可填外部来源链接）</label>
    <input name="url" value="<?= h($item['url'] ?? '') ?>" />

    <label>代表图 URL（可选，可填站内 /uploads/... 或外链）</label>
    <input name="cover_photo_url" value="<?= h($item['cover_photo_url'] ?? '') ?>" placeholder="/uploads/photos/xxx.jpg" />

    <?php if (!empty($item['cover_photo_url'])): ?>
      <div style="margin:8px 0;">
        <img src="<?= h($item['cover_photo_url']) ?>" style="max-width:260px;max-height:160px;object-fit:cover;border:1px solid #eee;">
      </div>
    <?php endif; ?>

    <label>上传代表图（可选，上传后会自动写入上面的 URL）</label>
    <input type="file" name="cover_photo_file" accept="image/*" />

    <div class="row">
      <div>
        <label>排序（小的在前）</label>
        <input name="sort_order" type="number" value="<?= (int)($item['sort_order'] ?? 0) ?>" />
      </div>
      <div>
        <label>发布</label>
        <div style="margin-top:10px;">
          <label style="display:flex;gap:10px;align-items:center;margin:0;">
            <input type="checkbox" name="is_published" <?= ((int)($item['is_published'] ?? 0)===1?'checked':'') ?> style="width:auto;">
            <span>对前台可见</span>
          </label>

          <label style="display:flex;gap:10px;align-items:center;margin:10px 0 0 0;">
            <input type="checkbox" name="is_featured" <?= ((int)($item['is_featured'] ?? 0)===1?'checked':'') ?> style="width:auto;">
            <span>精选（显示在首页新闻轮播）</span>
          </label>
        </div>
      </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button class="btn primary" type="submit">保存</button>
      <a class="btn" href="/admin/news.php">返回</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>