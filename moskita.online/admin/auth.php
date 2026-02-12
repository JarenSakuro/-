<?php
// admin/_auth.php
session_start();

function admin_require_login(): void {
  if (empty($_SESSION['admin_user'])) {
    header('Location: /admin/login.php');
    exit;
  }
}