<?php
require_once 'includes/config_db.php';

// Handle actions
if (isset($_GET['action'])) {
    $id = intval($_GET['id']);
    
    switch ($_GET['action']) {
        case 'delete':
            $conn->query("DELETE FROM pending_movies WHERE id = $id");
            break;
        case 'approve':
            // Move to approved table (you'll need to create this)
            $movie = $conn->query("SELECT * FROM pending_movies WHERE id = $id")->fetch_assoc();
            $conn->query("INSERT INTO movies 
                         (title, description, release_year, duration, movie_type, rating, genres, 
                          directors, cast, language, trailer_link, movie_file_path, poster_path) 
                         VALUES (
                            '{$conn->real_escape_string($movie['title'])}',
                            '{$conn->real_escape_string($movie['description'])}',
                            {$movie['release_year']},
                            {$movie['duration']},
                            '{$conn->real_escape_string($movie['movie_type'])}',
                            '{$conn->real_escape_string($movie['rating'])}',
                            '{$conn->real_escape_string($movie['genres'])}',
                            '{$conn->real_escape_string($movie['directors'])}',
                            '{$conn->real_escape_string($movie['cast'])}',
                            '{$conn->real_escape_string($movie['language'])}',
                            '{$conn->real_escape_string($movie['trailer_link'])}',
                            '{$conn->real_escape_string($movie['movie_file_path'])}',
                            '{$conn->real_escape_string($movie['poster_path'])}'
                         )");
            $conn->query("DELETE FROM pending_movies WHERE id = $id");
            break;
    }
    
    header("Location: pending_movies.php");
    exit();
}

// Fetch all pending movies
$movies = $conn->query("SELECT * FROM pending_movies ORDER BY upload_time DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Movies Review</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 100%; overflow-x: auto; }
        h1 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; position: sticky; top: 0; }
        tr:hover { background-color: #f5f5f5; }
        .action-btns { display: flex; gap: 5px; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 14px; }
        .btn-edit { background: #4CAF50; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-approve { background: #2196F3; color: white; }
        .btn-notes { background: #ff9800; color: white; }
        .no-movies { padding: 20px; text-align: center; color: #666; }
        .status-pending { color: #ff9800; }
        .short-text { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pending Movies Review</h1>
        
        <?php if ($movies->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Year</th>
                        <th>Duration</th>
                        <th>Type</th>
                        <th>Rating</th>
                        <th>Genres</th>
                        <th>Directors</th>
                        <th>Cast</th>
                        <th>Language</th>
                        <th>Trailer</th>
                        <th>Movie File</th>
                        <th>Poster</th>
                        <th>Uploaded</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($movie = $movies->fetch_assoc()): ?>
                    <tr>
                        <td><?= $movie['id'] ?></td>
                        <td><?= htmlspecialchars($movie['title']) ?></td>
                        <td class="short-text" title="<?= htmlspecialchars($movie['description']) ?>">
                            <?= htmlspecialchars($movie['description']) ?>
                        </td>
                        <td><?= $movie['release_year'] ?></td>
                        <td><?= $movie['duration'] ?> min</td>
                        <td><?= htmlspecialchars($movie['movie_type']) ?></td>
                        <td><?= htmlspecialchars($movie['rating']) ?></td>
                        <td class="short-text"><?= htmlspecialchars($movie['genres']) ?></td>
                        <td class="short-text"><?= htmlspecialchars($movie['directors']) ?></td>
                        <td class="short-text"><?= htmlspecialchars($movie['cast']) ?></td>
                        <td><?= htmlspecialchars($movie['language']) ?></td>
                        <td class="short-text">
                            <?php if (!empty($movie['trailer_link'])): ?>
                                <a href="<?= htmlspecialchars($movie['trailer_link']) ?>" target="_blank">View</a>
                            <?php endif; ?>
                        </td>
                        <td class="short-text">
                            <?php if (!empty($movie['movie_file_path'])): ?>
                                <a href="<?= htmlspecialchars($movie['movie_file_path']) ?>">Link</a>
                            <?php endif; ?>
                        </td>
                        <td class="short-text">
                            <?php if (!empty($movie['poster_path'])): ?>
                                <a href="<?= htmlspecialchars($movie['poster_path']) ?>">View</a>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($movie['upload_time'])) ?></td>
                        <td class="status-pending"><?= htmlspecialchars($movie['status']) ?></td>
                        <td class="short-text" title="<?= htmlspecialchars($movie['review_notes']) ?>">
                            <?= htmlspecialchars($movie['review_notes']) ?>
                        </td>
                        <td>
                        <div class="action-btns">
    <!-- Edit Button -->
    <a href="edit_movie.php?id=<?= htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8') ?>" 
       class="btn btn-edit"
       title="Edit movie details">
       Edit
    </a>
    
    <!-- Delete Button -->
    <form method="POST" action="pending_movies.php" style="display: inline;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" 
                class="btn btn-delete" 
                onclick="return confirm('Are you sure you want to permanently delete this movie?')"
                title="Delete movie permanently">
                Delete
        </button>
    </form>
    
    <!-- Approve Button -->
    <form method="POST" action="pending_movies.php" style="display: inline;">
        <input type="hidden" name="action" value="approve">
        <input type="hidden" name="id" value="<?= htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" 
                class="btn btn-approve"
                onclick="return confirm('Approve this movie and move it to the main collection?')"
                title="Approve and publish movie">
                Approve
        </button>
    </form>
</div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-movies">
                <p>No pending movies found.</p>
                <a href="upload.php">Upload a new movie</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>