<?php
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function asset(string $path): string {
  return '/' . ltrim($path, '/');
}

function get_int(string $key, int $default = 0): int {
  $v = $_GET[$key] ?? null;
  if ($v === null) return $default;
  if (!is_numeric($v)) return $default;
  return (int)$v;
}