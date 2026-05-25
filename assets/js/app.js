document.addEventListener('DOMContentLoaded', function () {

    const starRating = document.getElementById('star-rating');
    if (starRating) {
        const stars = starRating.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-value');

        stars.forEach(function (star) {
            star.addEventListener('click', function () {
                const value = parseInt(this.dataset.value);
                ratingInput.value = value;
                stars.forEach(function (s, i) {
                    s.textContent = i < value ? '★' : '☆';
                    s.classList.toggle('active', i < value);
                });
            });

            star.addEventListener('mouseenter', function () {
                const value = parseInt(this.dataset.value);
                stars.forEach(function (s, i) {
                    s.textContent = i < value ? '★' : '☆';
                });
            });

            star.addEventListener('mouseleave', function () {
                const activeVal = parseInt(ratingInput.value || 0);
                stars.forEach(function (s, i) {
                    s.textContent = i < activeVal ? '★' : '☆';
                });
            });
        });
    }

    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msgDiv = document.getElementById('review-message');

            fetch('/api/add_review.php', {
                method: 'POST',
                body: formData
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        msgDiv.innerHTML = '<div class="form-success">' + data.message + '</div>';
                        reviewForm.reset();
                        document.getElementById('rating-value').value = 0;
                        document.querySelectorAll('#star-rating .star').forEach(function (s) {
                            s.textContent = '☆';
                            s.classList.remove('active');
                        });
                        if (data.html) {
                            document.getElementById('reviews-container').innerHTML = data.html;
                        }
                        setTimeout(function () { msgDiv.innerHTML = ''; }, 3000);
                    } else {
                        msgDiv.innerHTML = '<div class="form-error">' + data.message + '</div>';
                    }
                })
                .catch(function () {
                    msgDiv.innerHTML = '<div class="form-error">Something went wrong.</div>';
                });
        });
    }

    const watchlistBtn = document.getElementById('watchlist-btn');
    if (watchlistBtn) {
        watchlistBtn.addEventListener('click', function () {
            const movieId = this.dataset.movieId;
            const inWatchlist = this.dataset.inWatchlist === '1';

            fetch('/api/toggle_watchlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'movie_id=' + movieId + '&remove=' + (inWatchlist ? '1' : '0')
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        if (data.in_watchlist) {
                            watchlistBtn.textContent = '♥ In Your Watchlist';
                            watchlistBtn.classList.add('btn-danger');
                            watchlistBtn.dataset.inWatchlist = '1';
                        } else {
                            watchlistBtn.textContent = '♡ Add to Watchlist';
                            watchlistBtn.classList.remove('btn-danger');
                            watchlistBtn.dataset.inWatchlist = '0';
                        }
                    }
                });
        });
    }

    const spinBtn = document.getElementById('spin-btn');
    if (spinBtn) {
        spinBtn.addEventListener('click', spinSlotMachine);
    }

    let isSpinning = false;

    function spinSlotMachine() {
        if (isSpinning) return;
        isSpinning = true;

        const genre = document.getElementById('genre-filter').value;
        const slotMachine = document.getElementById('slot-machine');
        const slotReel = document.getElementById('slot-reel');
        const slotPlaceholder = document.getElementById('slot-placeholder');
        const resultActions = document.getElementById('slot-result-actions');

        spinBtn.disabled = true;
        spinBtn.textContent = '⏳ Spinning...';
        slotPlaceholder.style.display = 'none';
        slotReel.style.display = 'block';
        slotReel.innerHTML = '';
        resultActions.style.display = 'none';
        slotMachine.classList.remove('result-reveal');
        slotMachine.classList.add('spinning');

            fetch('/api/random_movie.php?genre=' + encodeURIComponent(genre))
            .then(function (res) { return res.json(); })
            .then(function (movies) {
                if (!movies || movies.length === 0) {
                    slotMachine.classList.remove('spinning');
                    slotPlaceholder.style.display = 'block';
                    slotPlaceholder.innerHTML = '<div class="slot-icon">😕</div><p>No movies found. Try a different genre!</p>';
                    spinBtn.disabled = false;
                    spinBtn.textContent = '🎰 SPIN';
                    isSpinning = false;
                    return;
                }

                let counter = 0;
                const totalSpins = 20 + Math.floor(Math.random() * 15);
                let intervalTime = 80;

                function cycleReel() {
                    const idx = counter % movies.length;
                    const movie = movies[idx];
                    const initial = movie.title ? movie.title.charAt(0).toUpperCase() : '?';
                    const year = movie.release_year || '';
                    const rating = movie.vote_average || '';

                    const posterHtml = movie.poster_path
                        ? '<img src="' + escapeHtml(movie.poster_path) + '" alt="' + escapeHtml(movie.title) + '" style="width:100%;height:100%;object-fit:cover;">'
                        : '<div class="slot-poster-letter">' + initial + '</div>';

                    slotReel.innerHTML =
                        '<div class="slot-card">' +
                        '<div class="slot-poster" style="position:relative;">' + posterHtml + '</div>' +
                        '<div class="slot-info">' +
                        '<h2>' + escapeHtml(movie.title) + '</h2>' +
                        '<div class="slot-meta">' +
                        '<span>' + year + '</span>' +
                        '<span class="slot-rating">★ ' + rating + '</span>' +
                        '</div>' +
                        '<span class="slot-genre">' + escapeHtml(movie.genre) + '</span>' +
                        '</div>' +
                        '</div>';

                    counter++;

                    if (counter < totalSpins) {
                        intervalTime = Math.min(300, 80 + counter * 8);
                        setTimeout(cycleReel, intervalTime);
                    } else {
                        slotMachine.classList.remove('spinning');
                        slotMachine.classList.add('result-reveal');

                        const finalMovie = movies[(counter - 1) % movies.length];
                        const finalInitial = finalMovie.title ? finalMovie.title.charAt(0).toUpperCase() : '?';
                        const finalId = finalMovie.id || 0;

                        const finalPosterHtml = finalMovie.poster_path
                            ? '<img src="' + escapeHtml(finalMovie.poster_path) + '" alt="' + escapeHtml(finalMovie.title) + '" style="width:100%;height:100%;object-fit:cover;">'
                            : '<div class="slot-poster-letter">' + finalInitial + '</div>';

                        slotReel.innerHTML =
                            '<div class="slot-card">' +
                            '<div class="slot-poster" style="position:relative;">' + finalPosterHtml + '</div>' +
                            '<div class="slot-info">' +
                            '<h2>' + escapeHtml(finalMovie.title) + '</h2>' +
                            '<div class="slot-meta">' +
                            '<span>' + (finalMovie.release_year || '') + '</span>' +
                            '<span class="slot-rating">★ ' + (finalMovie.vote_average || '') + '</span>' +
                            '</div>' +
                            '<span class="slot-genre">' + escapeHtml(finalMovie.genre) + '</span>' +
                            '</div>' +
                            '</div>';

                        resultActions.style.display = 'block';
                        resultActions.innerHTML =
                            '<a href="movie.php?id=' + finalId + '" class="btn">View Details</a>' +
                            '<button class="btn btn-outline" onclick="document.getElementById(\'spin-btn\').click()">Spin Again</button>';

                        spinBtn.disabled = false;
                        spinBtn.textContent = '🎰 SPIN AGAIN';
                        isSpinning = false;
                    }
                }

                cycleReel();
            })
            .catch(function () {
                slotMachine.classList.remove('spinning');
                slotPlaceholder.style.display = 'block';
                slotPlaceholder.innerHTML = '<div class="slot-icon">⚠️</div><p>Something went wrong.</p>';
                spinBtn.disabled = false;
                spinBtn.textContent = '🎰 SPIN';
                isSpinning = false;
            });
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
});