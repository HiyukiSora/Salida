<?php require_once 'includes/header.php'; ?>

<?php
$movieId = (int)($_GET['id'] ?? 0);
$movie = getMovie($movieId);

if (!$movie):
?>
    <section class="section" style="text-align:center;padding:100px 20px;">
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <h2>Movie not found</h2>
            <p>The movie you're looking for doesn't exist.</p>
            <a href="browse.php" class="btn" style="margin-top:15px;">Browse Movies</a>
        </div>
    </section>
<?php
else:
    $avgRating = getAverageRating($movieId);
    $ratings = getRatings($movieId);
    $inWatchlist = $currentUser ? isInWatchlist($currentUser['id'], $movieId) : false;
?>

<section class="movie-detail">
    <div class="movie-detail-poster">
        <div class="poster-frame">
            <?php if ($movie['poster_path']): ?>
                <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
            <?php else: ?>
                <div class="poster-initial"><?= strtoupper(substr($movie['title'], 0, 1)) ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="movie-detail-info">
        <h1><?= htmlspecialchars($movie['title']) ?></h1>

        <div class="detail-meta">
            <span>📅 <?= $movie['release_year'] ?></span>
            <span>⏱ <?= $movie['runtime'] ?> min</span>
            <span class="detail-rating">★ <?= $movie['vote_average'] ?></span>
            <span class="detail-genre"><?= htmlspecialchars($movie['genre']) ?></span>
        </div>

        <p class="detail-overview"><?= htmlspecialchars($movie['overview']) ?></p>

        <div class="detail-extra">
            <div class="extra-item">
                <strong>Director</strong>
                <?= htmlspecialchars($movie['director'] ?? 'N/A') ?>
            </div>
            <div class="extra-item">
                <strong>Cast</strong>
                <?= htmlspecialchars($movie['actors'] ?? 'N/A') ?>
            </div>
            <div class="extra-item">
                <strong>Rating Average</strong>
                ★ <?= number_format((float)($avgRating['avg_rating'] ?? $movie['vote_average']), 1) ?> / 5
            </div>
            <div class="extra-item">
                <strong>Total Reviews</strong>
                <?= $avgRating['count'] ?? 0 ?>
            </div>
        </div>

        <div class="detail-actions">
            <?php if ($currentUser): ?>
                <button class="btn <?= $inWatchlist ? 'btn-danger' : '' ?>"
                        id="watchlist-btn"
                        data-movie-id="<?= $movie['id'] ?>"
                        data-in-watchlist="<?= $inWatchlist ? '1' : '0' ?>">
                    <?= $inWatchlist ? '♥ In Your Watchlist' : '♡ Add to Watchlist' ?>
                </button>
            <?php endif; ?>
            <a href="randomizer.php?genre=<?= urlencode($movie['genre']) ?>" class="btn btn-outline">🎰 Find Similar</a>
        </div>
    </div>
</section>

<section class="ratings-section">
    <h2>Ratings & Reviews</h2>

    <?php if ($currentUser): ?>
        <form class="rating-form" id="review-form">
            <h3>Rate this movie</h3>
            <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
            <input type="hidden" name="rating" id="rating-value" value="0">
            <div class="star-rating" id="star-rating">
                <span class="star" data-value="1">☆</span>
                <span class="star" data-value="2">☆</span>
                <span class="star" data-value="3">☆</span>
                <span class="star" data-value="4">☆</span>
                <span class="star" data-value="5">☆</span>
            </div>
            <textarea name="review_text" placeholder="Write your thoughts about this movie..." rows="3"></textarea>
            <button type="submit" class="btn" style="margin-top:12px;">Submit Review</button>
        </form>
        <div id="review-message" style="margin-top:10px;"></div>
    <?php else: ?>
        <p style="color:#888;margin-bottom:20px;"><a href="login.php" style="color:#e50914;">Login</a> to rate and review movies.</p>
    <?php endif; ?>

    <div id="reviews-container">
        <?php if (empty($ratings)): ?>
            <p style="color:#666;">No reviews yet. Be the first to share your thoughts!</p>
        <?php else: ?>
            <?php foreach ($ratings as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="review-user"><?= htmlspecialchars($review['username']) ?></span>
                        <span class="review-stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></span>
                    </div>
                    <?php if ($review['review_text']): ?>
                        <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                    <?php endif; ?>
                    <div class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>