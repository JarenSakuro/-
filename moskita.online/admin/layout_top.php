<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/auth.php';
admin_require_login();

$adminUser = $_SESSION['admin_user'] ?? 'admin';
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($title ?? '后台') ?></title>
  <style>
    body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"PingFang SC","Microsoft YaHei",Segoe UI,Roboto,Arial,sans-serif;background:#0b0b0c;color:#f2f2f2}
    a{color:inherit;text-decoration:none}
    .wrap{display:flex;min-height:100vh}
    .side{width:220px;border-right:1px solid rgba(255,255,255,.12);padding:18px}
    .main{flex:1;padding:18px}
    .muted{color:rgba(242,242,242,.65)}
    .nav a{display:block;padding:10px 10px;border-radius:10px;color:rgba(242,242,242,.75)}
    .nav a:hover{background:rgba(255,255,255,.06);color:#fff}
    h1{margin:0 0 14px 0;font-size:20px}
    table{width:100%;border-collapse:collapse}
    th,td{border-bottom:1px solid rgba(255,255,255,.12);padding:10px 8px;text-align:left;vertical-align:top}
    input,select,textarea{width:100%;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.04);color:#fff}
    textarea{min-height:140px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .btn{display:inline-block;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);cursor:pointer;color:#fff}
    .btn:hover{background:rgba(255,255,255,.10)}
    .btn-primary{background:rgba(70,160,255,.25);border-color:rgba(70,160,255,.5)}
    .btn-danger{background:rgba(255,70,70,.18);border-color:rgba(255,70,70,.45)}
    .bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
    .msg{padding:10px 12px;border:1px solid rgba(255,255,255,.12);border-radius:12px;background:rgba(255,255,255,.04);margin-bottom:14px}
  </style>
</head>
<body>
<div class="wrap">
  <aside class="side">
    <div style="margin-bottom:10px;">登录：<span class="muted"><?= h($adminUser) ?></span></div>
    <div class="nav">
      <a href="/admin/index.php">控制台</a>
      <a href="/admin/video.php">开场视频</a>
      <a href="/admin/albums.php">相册</a>
      <a href="/admin/photos.php">照片</a>
      <a href="/admin/timeline.php">时间线</a>
      <a href="/admin/news.php">新闻</a>
      <a href="/admin/about.php">简介页</a>
      <a href="/admin/logout.php">退出</a>
    </div>
  </aside>
  <main class="main">