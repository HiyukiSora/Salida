<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function getAllMovies($genre = '', $search = '', $sort = 'title', $page = 1, $perPage = 12) {
    global $pdo;
    $conditions = [];
    $params = [];

    if ($genre) {
        $conditions[] = "genre LIKE ?";
        $params[] = "%$genre%";
    }
    if ($search) {
        $conditions[] = "(title LIKE ? OR director LIKE ? OR actors LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $offset = ($page - 1) * $perPage;

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM movies $where");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    $allowedSort = ['title', 'release_year', 'vote_average'];
    $sortCol = in_array($sort, $allowedSort) ? $sort : 'title';
    $sortDir = ($sort === 'release_year') ? 'DESC' : 'ASC';

    $stmt = $pdo->prepare("SELECT * FROM movies $where ORDER BY $sortCol $sortDir LIMIT " . (int)$perPage . " OFFSET " . (int)$offset);
    $stmt->execute($params);

    return ['movies' => $stmt->fetchAll(), 'total' => $total, 'pages' => max(1, ceil($total / $perPage))];
}

function getMovie($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getRatings($movieId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, u.username
        FROM ratings r
        JOIN users u ON r.user_id = u.id
        WHERE r.movie_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$movieId]);
    return $stmt->fetchAll();
}

function getAverageRating($movieId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM ratings WHERE movie_id = ?");
    $stmt->execute([$movieId]);
    return $stmt->fetch();
}

function isInWatchlist($userId, $movieId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$userId, $movieId]);
    return (bool)$stmt->fetch();
}

function getWatchlist($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT m.*, w.added_at
        FROM watchlist w
        JOIN movies m ON w.movie_id = m.id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getUserRatings($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, m.title
        FROM ratings r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getRandomMovie($genre = '') {
    global $pdo;
    if ($genre) {
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE genre LIKE ? ORDER BY RAND() LIMIT 1");
        $stmt->execute(["%$genre%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM movies ORDER BY RAND() LIMIT 1");
    }
    return $stmt->fetch();
}

function getMultipleRandomMovies($count = 10, $genre = '') {
    global $pdo;
    $count = (int)$count;
    if ($genre) {
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE genre LIKE ? ORDER BY RAND() LIMIT $count");
        $stmt->execute(["%$genre%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM movies ORDER BY RAND() LIMIT $count");
    }
    return $stmt->fetchAll();
}

function getFeaturedMovies() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY vote_average DESC LIMIT 6");
    return $stmt->fetchAll();
}

function getUniqueGenres() {
    global $pdo;
    $stmt = $pdo->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL ORDER BY genre");
    $genres = [];
    while ($row = $stmt->fetch()) {
        $parts = explode(', ', $row['genre']);
        foreach ($parts as $g) {
            $g = trim($g);
            if ($g && !in_array($g, $genres)) {
                $genres[] = $g;
            }
        }
    }
    sort($genres);
    return $genres;
}