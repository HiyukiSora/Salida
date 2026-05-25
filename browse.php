<?php require_once 'includes/header.php'; ?>

<?php
$genre = $_GET['genre'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'title';
$page = max(1, (int)($_GET['page'] ?? 1));
$genres = getUniqueGenres();
$result = getAllMovies($genre, $search, $sort, $page);
$movies = $result['movies'];
$totalPages = $result['pages'];
?>

<section class="page-header">
    <h1>Browse Movies</h1>
    <p><?= $result['total'] ?> movie<?= $result['total'] !== 1 ? 's' : '' ?> in our collection</p>
</section>

<section class="section" style="padding-top: 0;">
    <form class="filters-bar" method="GET">
        <input type="text" name="search" placeholder="Search by title, director, or actor..." value="<?= htmlspecialchars($search) ?>">
        <label for="genre">Genre:</label>
        <select name="genre" id="genre">
            <option value="">All Genres</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= $genre === $g ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="sort">Sort:</label>
        <select name="sort" id="sort">
            <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Title A-Z</option>
            <option value="release_year" <?= $sort === 'release_year' ? 'selected' : '' ?>>Year</option>
            <option value="vote_average" <?= $sort === 'vote_average' ? 'selected' : '' ?>>Rating</option>
        </select>
        <button type="submit" class="btn btn-sm">Filter</button>
    </form>

    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <p>No movies found matching your criteria.</p>
            <a href="browse.php" class="btn" style="margin-top: 15px;">Clear Filters</a>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
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

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&genre=<?= urlencode($genre) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>