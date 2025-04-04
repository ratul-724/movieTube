<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Movies - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-darker: #050505;
            --text-main: #ffffff;
            --text-secondary: #aaaaaa;
            --accent-red: #ff0000;
            --accent-dark-red: #cc0000;
        }
        
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        
        .movie-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .movie-card {
        background-color: #111;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid var(--accent-red);
        transition: transform 0.3s;

        }

        
        .movie-id-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            z-index: 2;
            }
        
        .movie-header {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #333;
            position: relative; /* For ID positioning */
            }
        
        .movie-poster {
            width: 100px; /* Smaller poster */
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
            }
        
        .movie-title {
            font-size: 1.3rem; /* Smaller title */
            color: var(--accent-red);
            margin-bottom: 8px;
            }
            
        .movie-meta {
            gap: 8px;
            margin-bottom: 8px;
            }
        
        .meta-badge {
            padding: 3px 8px;
            font-size: 0.8rem;
            }
        
        .movie-body {
            padding: 15px;
            }
        
        .section-title {
            font-size: 1.1rem;
        }
        
        .movie-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .genre-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .genre-badge {
            background-color: #333;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .no-movies {
            text-align: center;
            padding: 50px;
            background-color: #111;
            border-radius: 8px;
        }
        
        .no-movies i {
            font-size: 3rem;
            color: var(--accent-red);
            margin-bottom: 20px;
        }
        
        .media-link {
            color: var(--accent-red);
            text-decoration: none;
        }
        
        .media-link:hover {
            text-decoration: underline;
        }

        .movie-actions {
            display: flex;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid #333;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }

        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background-color: #138496;
        }
    </style>
</head>
<body >
<a href="../index.html" class="px-lg-5"><img src="../img/logo.png" alt="" height="50" class="z-1">
</a>

    <div class="movie-container">
        <h1 class="text-center mb-4"><i class="fas fa-clock me-2"></i> Pending Movies</h1>
        
        <div id="loading-spinner" class="text-center my-5">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading movies...</p>
        </div>
        
        <div id="error-message" class="alert alert-danger d-none"></div>
        
        <div id="no-movies" class="no-movies d-none">
            <i class="fas fa-film"></i>
            <h3>No Movies Pending Approval</h3>
            <p>There are currently no movies waiting for review.</p>
        </div>
        
        <div id="movie-grid" class="movie-grid row"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_pending_movies.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('loading-spinner').classList.add('d-none');
                    
                    if (data.success) {
                        if (data.movies.length === 0) {
                            document.getElementById('no-movies').classList.remove('d-none');
                        } else {
                            renderMovies(data.movies);
                        }
                    } else {
                        showError(data.message || 'Failed to load movies');
                    }
                })
                .catch(error => {
                    document.getElementById('loading-spinner').classList.add('d-none');
                    showError(error.message);
                });
            
            function renderMovies(movies) {
                const movieGrid = document.getElementById('movie-grid');
                movieGrid.innerHTML = ''; // Clear previous content

                movies.forEach(movie => {
                    const genres = movie.genres.split(', ').map(genre => 
                        `<span class="genre-badge">${escapeHtml(genre)}</span>`
                    ).join('');
                    
                    const movieCard = `
                    <div class="col-lg-6 mt-4">
                        <div class="movie-card h-100 ">
                            <div class="movie-header">
                            <div class="movie-id-badge">ID: ${movie.id}</div>
                                <img src="${escapeHtml(movie.poster_link)}" 
                                    alt="${escapeHtml(movie.title)} Poster" 
                                    class="movie-poster">
                                <div class="movie-header-content">
                                    <h2 class="movie-title">${escapeHtml(movie.title)}</h2>
                                    
                                    <div class="movie-meta">
                                        <span class="meta-badge">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            ${escapeHtml(movie.release_year)}
                                        </span>
                                        <span class="meta-badge">
                                            <i class="fas fa-clock me-2"></i>
                                            ${escapeHtml(movie.duration_formatted)}
                                        </span>
                                        <span class="meta-badge">
                                            <i class="fas fa-tag me-2"></i>
                                            ${escapeHtml(ucfirst(movie.movie_type))}
                                        </span>
                                        <span class="meta-badge">
                                            <i class="fas fa-film me-2"></i>
                                            ${escapeHtml(movie.quality)}
                                        </span>
                                        <span class="meta-badge">
                                            <i class="fas fa-language me-2"></i>
                                            ${escapeHtml(ucfirst(movie.language))}
                                        </span>
                                    </div>
                                    
                                    <div class="text-secondary">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Submitted on ${formatDate(movie.submission_date)}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="movie-body">
                                <div class="movie-section">
                                    <h3 class="section-title"><i class="fas fa-align-left me-2"></i> Description</h3>
                                    <p class="movie-description">
                                        ${escapeHtml(movie.description || 'No description provided')}
                                    </p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="movie-section">
                                            <h3 class="section-title"><i class="fas fa-tags me-2"></i> Genres</h3>
                                            <div class="genre-list">
                                                ${genres}
                                            </div>
                                        </div>
                                        
                                        <div class="movie-section">
                                            <h3 class="section-title"><i class="fas fa-users me-2"></i> Cast & Crew</h3>
                                            <p><strong>Directors:</strong> ${escapeHtml(movie.directors || 'Not specified')}</p>
                                            <p><strong>Cast:</strong> ${escapeHtml(movie.cast || 'Not specified')}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="movie-section">
                                            <h3 class="section-title"><i class="fas fa-link me-2"></i> Media Links</h3>
                                            <p>
                                                <strong><i class="fab fa-google-drive me-2"></i> Drive Link:</strong>
                                                <a href="${escapeHtml(movie.drive_link)}" target="_blank" class="media-link">
                                                    View on Google Drive
                                                </a>
                                            </p>
                                            ${movie.trailer_link ? `
                                            <p>
                                                <strong><i class="fab fa-youtube me-2"></i> Trailer:</strong>
                                                <a href="${escapeHtml(movie.trailer_link)}" target="_blank" class="media-link">
                                                    Watch Trailer
                                                </a>
                                            </p>
                                            ` : ''}
                                            <p>
                                                <strong><i class="fas fa-image me-2"></i> Poster:</strong>
                                                <a href="${escapeHtml(movie.poster_link)}" target="_blank" class="media-link">
                                                    View Full Poster
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="movie-actions ">
                                <button class="action-btn btn-approve" data-movie-id="${movie.id}">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="action-btn btn-reject" data-movie-id="${movie.id}">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <button class="action-btn btn-edit" data-movie-id="${movie.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    `;
                    
                    movieGrid.insertAdjacentHTML('beforeend', movieCard);
                });
            }
                    
            function showError(message) {
                const errorEl = document.getElementById('error-message');
                errorEl.textContent = message;
                errorEl.classList.remove('d-none');
            }
            
            function escapeHtml(unsafe) {
                if (!unsafe) return '';
                return unsafe
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
            
            function ucfirst(str) {
                if (!str) return '';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Add this inside your DOMContentLoaded event listener
// Add this inside your DOMContentLoaded event listener after renderMovies()


document.addEventListener('click', function (e) {
    const approveBtn = e.target.closest('.btn-approve');
    const rejectBtn = e.target.closest('.btn-reject');

    if (approveBtn) {
        const movieId = approveBtn.dataset.movieId;
        const movieCard = approveBtn.closest('.movie-card');
        handleMovieApproval(movieId, movieCard);
    }

    if (rejectBtn) {
        const movieId = rejectBtn.dataset.movieId;
        const movieCard = rejectBtn.closest('.movie-card');
        handleMovieRejection(movieId, movieCard);
    }
});

async function handleMovieApproval(movieId, movieCard) {
    if (!confirm('Are you sure you want to approve this movie?')) return;

    const approveBtn = movieCard.querySelector('.btn-approve');
    const originalText = approveBtn.innerHTML;

    approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing';
    approveBtn.disabled = true;

    try {
        const response = await fetch('approve_movie.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `movie_id=${movieId}`,
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Approval failed');
        }

        // Success animation
        movieCard.style.transition = 'all 0.3s';
        movieCard.style.opacity = '0';
        movieCard.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            movieCard.closest('.col-lg-6').remove();

            // Show empty state if no movies left
            if (document.querySelectorAll('.movie-card').length === 0) {
                document.getElementById('no-movies').classList.remove('d-none');
            }

            // Show success notification
            showNotification('Movie approved successfully!', 'success');
        }, 300);
    } catch (error) {
        console.error('Approval error:', error);
        showNotification(`Error: ${error.message}`, 'error');
    } finally {
        approveBtn.innerHTML = originalText;
        approveBtn.disabled = false;
    }
}

async function handleMovieRejection(movieId, movieCard) {
    if (!confirm('Are you sure you want to reject this movie?')) return;

    const rejectBtn = movieCard.querySelector('.btn-reject');
    const originalText = rejectBtn.innerHTML;

    rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing';
    rejectBtn.disabled = true;

    try {
        const response = await fetch('reject_movie.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `movie_id=${movieId}`,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Rejection failed');
        }

        // Success animation
        movieCard.style.transition = 'all 0.3s';
        movieCard.style.opacity = '0';
        movieCard.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            movieCard.closest('.col-lg-6').remove();

            // Show empty state if no movies left
            if (document.querySelectorAll('.movie-card').length === 0) {
                document.getElementById('no-movies').classList.remove('d-none');
            }

            // Show success notification
            showNotification('Movie rejected successfully!', 'success');
        }, 300);
        } catch (error) {
            console.error('Rejection error:', error);
            showNotification(`Error: ${error.message}`, 'error');
        } finally {
            rejectBtn.innerHTML = originalText;
            rejectBtn.disabled = false;
        }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} fixed-top mx-auto mt-3`;
    notification.style.maxWidth = '500px';
    notification.style.zIndex = '9999';
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
        });
    </script>
</body>
</html>