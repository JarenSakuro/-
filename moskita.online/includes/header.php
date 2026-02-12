<?php
$config = require __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($title ?? $config['site']['name']) ?></title>
  <link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>" />
</head>
<body class="<?= h($bodyClass ?? '') ?>">
  <header class="topbar" id="topbar" aria-hidden="true">
    <div class="topbar-inner">
      <a class="brand" href="/">河北阮鸿</a>
      <nav class="nav">
        <a href="/about.php">阮鸿简介</a>
        <a href="/albums.php">阮鸿视界</a>
        <a href="/news.php">阮鸿新闻</a>
      </nav>
    </div>
  </header>

  <main>