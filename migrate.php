<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$schema = file_get_contents(__DIR__ . '/schema.sql');
$seed = file_get_contents(__DIR__ . '/seed.sql');

try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS movie_recommender");
    $pdo->exec("USE movie_recommender");
    $pdo->exec($schema);
    $pdo->exec($seed);
    echo "Database migrated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
