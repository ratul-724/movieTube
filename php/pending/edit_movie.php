<?php
require_once 'includes/config_db.php';


$id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $conn->real_escape_string($_POST['title']),
        'description' => $conn->real_escape_string($_POST['description']),
        'release_year' => intval($_POST['release_year']),
        'duration' => intval($_POST['duration']),
        'movie_type' => $conn->real_escape_string($_POST['movie_type']),
        'rating' => $conn->real_escape_string($_POST['rating']),
        'genres' => $conn->real_escape_string($_POST['genres']),
        'directors' => $conn->real_escape_string($_POST['directors']),
        'cast' => $conn->real_escape_string($_POST['cast']),
        'language' => $conn->real_escape_string($_POST['language']),
        'trailer_link' => $conn->real_escape_string($_POST['trailer_link']),
        'movie_file_path' => $conn->real_escape_string($_POST['movie_file_path']),
        'poster_path' => $conn->real_escape_string($_POST['poster_path'])
    ];
    
    $sql = "UPDATE pending_movies SET 
            title = '{$data['title']}',
            description = '{$data['description']}',
            release_year = {$data['release_year']},
            duration = {$data['duration']},
            movie_type = '{$data['movie_type']}',
            rating = '{$data['rating']}',
            genres = '{$data['genres']}',
            directors = '{$data['directors']}',
            cast = '{$data['cast']}',
            language = '{$data['language']}',
            trailer_link = '{$data['trailer_link']}',
            movie_file_path = '{$data['movie_file_path']}',
            poster_path = '{$data['poster_path']}'
            WHERE id = $id";
    
    $conn->query($sql);
    header("Location: pending_movies.php");
    exit();
}

// Get current movie data
$movie = $conn->query("SELECT * FROM pending_movies WHERE id = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Movie: <?= htmlspecialchars($movie['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], input[type="url"], textarea, select { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        textarea { height: 100px; resize: vertical; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .form-actions { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 10px 15px; text-decoration: none; border-radius: 4px; }
        .btn-save { background: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn-cancel { background: #f44336; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <h1>Edit Movie: <?= htmlspecialchars($movie['title']) ?></h1>
    
    <form method="post">
        <div class="form-group">
            <label for="title">Title*</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= htmlspecialchars($movie['description']) ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="release_year">Release Year*</label>
                <input type="number" id="release_year" name="release_year" 
                       min="1900" max="<?= date('Y') + 5 ?>" 
                       value="<?= $movie['release_year'] ?>" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (minutes)*</label>
                <input type="number" id="duration" name="duration" min="1" 
                       value="<?= $movie['duration'] ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="movie_type">Type*</label>
                <select id="movie_type" name="movie_type" required>
                    <option value="movie" <?= $movie['movie_type'] == 'movie' ? 'selected' : '' ?>>Movie</option>
                    <option value="tv-show" <?= $movie['movie_type'] == 'tv-show' ? 'selected' : '' ?>>TV Show</option>
                    <option value="documentary" <?= $movie['movie_type'] == 'documentary' ? 'selected' : '' ?>>Documentary</option>
                    <option value="short-film" <?= $movie['movie_type'] == 'short-film' ? 'selected' : '' ?>>Short Film</option>
                </select>
            </div>
            <div class="form-group">
                <label for="rating">Rating*</label>
                <select id="rating" name="rating" required>
                    <option value="G" <?= $movie['rating'] == 'G' ? 'selected' : '' ?>>G - General Audiences</option>
                    <option value="PG" <?= $movie['rating'] == 'PG' ? 'selected' : '' ?>>PG - Parental Guidance</option>
                    <option value="PG-13" <?= $movie['rating'] == 'PG-13' ? 'selected' : '' ?>>PG-13 - Parents Strongly Cautioned</option>
                    <option value="R" <?= $movie['rating'] == 'R' ? 'selected' : '' ?>>R - Restricted</option>
                    <option value="NC-17" <?= $movie['rating'] == 'NC-17' ? 'selected' : '' ?>>NC-17 - Adults Only</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="genres">Genres (comma separated)</label>
            <input type="text" id="genres" name="genres" value="<?= htmlspecialchars($movie['genres']) ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="directors">Directors (comma separated)</label>
                <input type="text" id="directors" name="directors" value="<?= htmlspecialchars($movie['directors']) ?>">
            </div>
            <div class="form-group">
                <label for="cast">Cast (comma separated)</label>
                <input type="text" id="cast" name="cast" value="<?= htmlspecialchars($movie['cast']) ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="language">Language*</label>
            <input type="text" id="language" name="language" value="<?= htmlspecialchars($movie['language']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="trailer_link">Trailer Link (URL)</label>
            <input type="url" id="trailer_link" name="trailer_link" value="<?= htmlspecialchars($movie['trailer_link']) ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="movie_file_path">Movie File Path*</label>
                <input type="text" id="movie_file_path" name="movie_file_path" 
                       value="<?= htmlspecialchars($movie['movie_file_path']) ?>" required>
            </div>
            <div class="form-group">
                <label for="poster_path">Poster Path*</label>
                <input type="text" id="poster_path" name="poster_path" 
                       value="<?= htmlspecialchars($movie['poster_path']) ?>" required>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-save">Save Changes</button>
            <a href="pending_movies.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</body>
</html>
<?php $conn->close(); ?>