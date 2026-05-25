<?php
ob_start();
register_shutdown_function(function () {
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
});

$page = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($page, PHP_URL_PATH);
$path = trim($path, '/');
$path = $path ?: 'index';

$path = preg_replace('/\.php$/', '', $path);
$safePath = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $path);

$phpFile = __DIR__ . '/../' . $safePath . '.php';

if (file_exists($phpFile)) {
    require $phpFile;
} else {
    http_response_code(404);
    echo '404 Not Found';
}