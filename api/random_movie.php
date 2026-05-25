<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$genre = $_GET['genre'] ?? '';

$movies = getMultipleRandomMovies(15, $genre);

if ($movies) {
    echo json_encode($movies);
} else {
    echo json_encode([]);
}