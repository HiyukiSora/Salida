<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to submit a review.']);
    exit;
}

$userId = $_SESSION['user_id'];
$movieId = (int)($_POST['movie_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$reviewText = trim($_POST['review_text'] ?? '');

if (!$movieId || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please select a rating (1-5).']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM ratings WHERE user_id = ? AND movie_id = ?");
$stmt->execute([$userId, $movieId]);
if ($stmt->fetch()) {
    $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, review_text = ? WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$rating, $reviewText, $userId, $movieId]);
} else {
    $stmt = $pdo->prepare("INSERT INTO ratings (user_id, movie_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $movieId, $rating, $reviewText]);
}

$ratings = getRatings($movieId);
$html = '';

if (empty($ratings)) {
    $html = '<p style="color:#666;">No reviews yet. Be the first to share your thoughts!</p>';
} else {
    foreach ($ratings as $review) {
        $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
        $html .= '<div class="review-card">';
        $html .= '<div class="review-header">';
        $html .= '<span class="review-user">' . htmlspecialchars($review['username']) . '</span>';
        $html .= '<span class="review-stars">' . $stars . '</span>';
        $html .= '</div>';
        if ($review['review_text']) {
            $html .= '<p class="review-text">' . htmlspecialchars($review['review_text']) . '</p>';
        }
        $html .= '<div class="review-date">' . date('M j, Y', strtotime($review['created_at'])) . '</div>';
        $html .= '</div>';
    }
}

echo json_encode(['success' => true, 'message' => 'Review submitted!', 'html' => $html]);