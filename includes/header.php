<?php
if (!isset($currentUser)) {
    $currentUser = (function_exists('isLoggedIn') && isLoggedIn()) ? getCurrentUser() : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S4L1D4 NI - Find Your Next Watch</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">🎬 S4L1D4 NI</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="browse.php">Browse</a>
                <a href="randomizer.php">Feeling Lucky</a>
            </div>
            <div class="nav-user">
                <?php if ($currentUser): ?>
                    <a href="profile.php" class="btn btn-sm"><?= htmlspecialchars($currentUser['username']) ?></a>
                    <a href="logout.php" class="btn btn-sm btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm">Login</a>
                    <a href="register.php" class="btn btn-sm btn-outline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
