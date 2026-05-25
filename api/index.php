<?php
ob_start();
register_shutdown_function(function () {
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
});

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');
$path = $path ?: 'index';

$safePath = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $path);

$phpFile = __DIR__ . '/../' . $safePath . '.php';

if (file_exists($phpFile)) {
    require $phpFile;
} else {
    http_response_code(404);
    echo '404 Not Found';
}