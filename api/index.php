<?php
declare(strict_types=1);
// Vercel PHP runtime entrypoint.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/../index.php';
    exit;
}
if ($path === '/dashboard.php') {
    require __DIR__ . '/../php-version/dashboard.php';
    exit;
}
if ($path === '/logout.php') {
    require __DIR__ . '/../php-version/logout.php';
    exit;
}
http_response_code(404);
echo 'Not Found';
