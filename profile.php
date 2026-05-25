<?php
require_once 'includes/header.php';

if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$watchlist = getWatchlist($currentUser['id']);
$userRatings = getUserRatings($currentUser['id']);
?>

<section class="profile-page">
    <div class="profile-header">
        <div class="profile-avatar"><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($currentUser['username']) ?></h1>
            <p><?= htmlspecialchars($currentUser['email']) ?></p>
        </div>
    </div>

    <div class="profile-section">
        <h2>♥ My Watchlist (<?= count($watchlist) ?>)</h2>
        <?php if (empty($watchlist)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>Your watchlist is empty. Browse movies and add some!</p>
                <a href="browse.php" class="btn" style="margin-top:15px;">Browse Movies</a>
            </div>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($watchlist as $movie): ?>
                    <a href="movie.php?id=<?= $movie['id'] ?>" class="movie-card">
                        <div class="movie-card-poster">
                            <?php if ($movie['poster_path']): ?>
                                <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="poster-image">
                            <?php else: ?>
                                <div class="poster-initial"><?= strtoupper(substr($movie['title'], 0, 1)) ?></div>
                            <?php endif; ?>
                            <span class="card-badge"><?= $movie['vote_average'] ?></span>
                        </div>
                        <div class="movie-card-body">
                            <h3><?= htmlspecialchars($movie['title']) ?></h3>
                            <div class="card-meta">
                                <span><?= $movie['release_year'] ?></span>
                                <span class="card-genre"><?= htmlspecialchars($movie['genre']) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="profile-section">
        <h2>✍ My Reviews (<?= count($userRatings) ?>)</h2>
        <?php if (empty($userRatings)): ?>
            <div class="empty-state">
                <div class="empty-icon">💬</div>
                <p>You haven't reviewed any movies yet.</p>
                <a href="browse.php" class="btn" style="margin-top:15px;">Browse Movies</a>
            </div>
        <?php else: ?>
            <?php foreach ($userRatings as $review): ?>
                <a href="movie.php?id=<?= $review['movie_id'] ?>" style="text-decoration:none;">
                    <div class="review-card">
                        <div class="review-header">
                            <span class="review-user"><?= htmlspecialchars($review['title']) ?></span>
                            <span class="review-stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></span>
                        </div>
                        <?php if ($review['review_text']): ?>
                            <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                        <?php endif; ?>
                        <div class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>