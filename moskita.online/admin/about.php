<?php
$title = '简介页';
require __DIR__ . '/layout_top.php';

// 确保有 about 这条记录
$pdo->prepare("
  INSERT INTO pages (slug, title, body)
  VALUES ('about', '阮鸿简介', '<p>这里是阮鸿简介内容。</p>')
  ON DUPLICATE KEY UPDATE slug=VALUES(slug)
")->execute();

$st = $pdo->prepare("SELECT id, title, body FROM pages WHERE slug='about' LIMIT 1");
$st->execute();
$page = $st->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $t = trim($_POST['title'] ?? '阮鸿简介');
  $b = $_POST['body'] ?? '';
  $pdo->prepare("UPDATE pages SET title=?, body=? WHERE slug='about'")->execute([$t, $b]);
  header('Location: /admin/about.php?saved=1');
  exit;
}
?>
<div class="card">
  <strong>编辑简介页（前台 /about.php）</strong>
  <?php if (isset($_GET['saved'])): ?>
    <div class="muted" style="margin-top:8px;">已保存</div>
  <?php endif; ?>

  <form method="post" style="margin-top:12px;">
    <label>标题</label>
    <input name="title" value="<?= h($page['title'] ?? '阮鸿简介') ?>" />

    <label>正文（支持 HTML；想换行就用 &lt;p&gt;...&lt;/p&gt;）</label>
    <textarea name="body" rows="14"><?= h($page['body'] ?? '') ?></textarea>

    <div style="margin-top:14px;">
      <button class="btn primary" type="submit">保存</button>
    </div>
  </form>
</div>
<?php require __DIR__ . '/layout_bottom.php'; ?>