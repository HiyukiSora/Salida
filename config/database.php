<?php
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || $trimmed[0] === '#') continue;
        putenv($trimmed);
    }
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'movie_recommender';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if ($host !== 'localhost') {
        $caCert = getenv('DB_CA_CERT');
        if ($caCert) {
            if (substr($caCert, 0, 11) === '-----BEGIN') {
                $caPath = sys_get_temp_dir() . '/aiven-ca.pem';
                @file_put_contents($caPath, $caCert);
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
            } else {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caCert;
            }
        } else {
            $options[PDO::MYSQL_ATTR_SSL_CA] = '';
        }
        if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
    }

    $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}