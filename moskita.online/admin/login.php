<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

session_start();
if (!empty($_SESSION['admin_user'])) {
  header('Location: /admin/index.php');
  exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim((string)($_POST['username'] ?? ''));
  $password = (string)($_POST['password'] ?? '');

  $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username=? LIMIT 1");
  $stmt->execute([$username]);
  $u = $stmt->fetch();

  if ($u && password_verify($password, $u['password_hash'])) {
    $_SESSION['admin_user'] = $u['username'];
    $_SESSION['admin_user_id'] = (int)$u['id'];
    header('Location: /admin/index.php');
    exit;
  } else {
    $err = '用户名或密码错误';
  }
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>后台登录</title>
  <style>
    body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0b0b0c;color:#f2f2f2;font-family:-apple-system,BlinkMacSystemFont,"PingFang SC","Microsoft YaHei",Segoe UI,Roboto,Arial,sans-serif}
    .card{width:360px;border:1px solid rgba(255,255,255,.14);border-radius:16px;background:rgba(255,255,255,.04);padding:18px}
    input{width:100%;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.04);color:#fff;margin-top:10px}
    button{width:100%;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(70,160,255,.25);color:#fff;margin-top:12px;cursor:pointer}
    .muted{color:rgba(242,242,242,.65);font-size:12px}
    .err{margin-top:10px;color:#ff8a8a}
  </style>
</head>
<body>
  <form class="card" method="post">
    <h2 style="margin:0 0 6px 0;">后台登录</h2>
    <div class="muted">如首次使用，请先执行初始化管理员 SQL（见我下一段）。</div>
    <?php if ($err): ?><div class="err"><?= h($err) ?></div><?php endif; ?>
    <input name="username" placeholder="用户名" autocomplete="username" required />
    <input name="password" type="password" placeholder="密码" autocomplete="current-password" required />
    <button type="submit">登录</button>
  </form>
</body>
</html>