<?php require_once 'includes/header.php'; ?>
<?php $genres = getUniqueGenres(); ?>

<section class="randomizer-page">
    <h1>🎰 The Movie Slot Machine</h1>
    <p class="randomizer-subtitle">Can't decide? Let fate choose. Pick a genre (or go full random) and spin!</p>

    <div class="randomizer-controls">
        <label for="genre-filter">Filter by genre:</label>
        <select id="genre-filter">
            <option value="">🎲 All Genres</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-lg btn-lucky" id="spin-btn">🎰 SPIN</button>
    </div>

    <div class="slot-machine" id="slot-machine">
        <div class="slot-placeholder" id="slot-placeholder">
            <div class="slot-icon">🎬</div>
            <p>Press SPIN to find your next movie</p>
        </div>
        <div class="slot-reel" id="slot-reel" style="display:none;"></div>
    </div>

    <div id="slot-result-actions" style="display:none;margin-top:25px;"></div>
</section>

<?php require_once 'includes/footer.php'; ?>