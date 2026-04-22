<?php
require_once __DIR__ . '/includes/why-page-template.php';
$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
  http_response_code(404);
  require_once __DIR__ . '/404.php';
  exit;
}
render_why_choose_page($slug);
