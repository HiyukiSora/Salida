<?php require_once 'includes/header.php'; ?>
<?php $featured = getFeaturedMovies(); ?>

<section class="hero">
    <div class="hero-content">
        <h1>Can't Decide What to Watch?</h1>
        <p>Browse our curated collection of top-rated movies or let fate decide. Your next favorite film is just a click away.</p>
        <div class="hero-buttons">
            <a href="randomizer.php" class="btn btn-lg btn-lucky">🎰 I'm Feeling Lucky</a>
            <a href="browse.php" class="btn btn-lg btn-outline">Browse All Movies</a>
        </div>
    </div>
</section>

<section class="section">
    <h2 class="section-title">Top Rated Picks</h2>
    <div class="movie-grid">
        <?php foreach ($featured as $movie): ?>
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
</section>

<?php require_once 'includes/footer.php'; ?>