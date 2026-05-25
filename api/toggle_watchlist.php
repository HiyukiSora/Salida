<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$movieId = (int)($_POST['movie_id'] ?? 0);
$remove = (bool)($_POST['remove'] ?? false);

if (!$movieId) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID.']);
    exit;
}

if ($remove) {
    $stmt = $pdo->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$userId, $movieId]);
    echo json_encode(['success' => true, 'in_watchlist' => false]);
} else {
    $stmt = $pdo->prepare("INSERT IGNORE INTO watchlist (user_id, movie_id) VALUES (?, ?)");
    $stmt->execute([$userId, $movieId]);
    echo json_encode(['success' => true, 'in_watchlist' => true]);
}