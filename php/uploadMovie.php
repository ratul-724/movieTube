<?php
header('Content-Type: application/json');

// Error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/upload_errors.log');

// Database config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Movie-Tube');

$response = [
    'success' => false,
    'message' => '',
    'debug' => []
];

function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check for empty required fields
    $required = ['title', 'release_year', 'duration', 'movie_type', 'rating', 'language'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate files
    if (empty($_FILES['movie_file']['tmp_name'])) {
        throw new Exception('Movie file is required');
    }
    if (empty($_FILES['poster_image']['tmp_name'])) {
        throw new Exception('Poster image is required');
    }

    // File validation
    $allowedMovieTypes = ['mp4', 'mkv', 'mov', 'avi'];
    $allowedImageTypes = ['jpg', 'jpeg', 'png'];
    $maxFileSize = 5000 * 1024 * 1024; // 5GB

    $movieFile = $_FILES['movie_file'];
    $posterFile = $_FILES['poster_image'];

    // Validate movie file
    $movieExt = strtolower(pathinfo($movieFile['name'], PATHINFO_EXTENSION));
    if (!in_array($movieExt, $allowedMovieTypes)) {
        throw new Exception('Invalid movie file type');
    }
    if ($movieFile['size'] > $maxFileSize) {
        throw new Exception('Movie file exceeds maximum size');
    }

    // Validate poster image
    $imageInfo = getimagesize($posterFile['tmp_name']);
    if (!$imageInfo) {
        throw new Exception('Invalid poster image');
    }
    $posterExt = strtolower(pathinfo($posterFile['name'], PATHINFO_EXTENSION));
    if (!in_array($posterExt, $allowedImageTypes)) {
        throw new Exception('Invalid poster image type');
    }
    if ($posterFile['size'] > 10 * 1024 * 1024) { // 10MB
        throw new Exception('Poster image exceeds maximum size');
    }

    // Prepare upload directory
    $uploadDir = __DIR__ . '/../uploads/temp/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filenames
    $movieFilename = 'movie_' . uniqid() . '.' . $movieExt;
    $posterFilename = 'poster_' . uniqid() . '.' . $posterExt;
    $moviePath = $uploadDir . $movieFilename;
    $posterPath = $uploadDir . $posterFilename;

    // Move uploaded files
    if (!move_uploaded_file($movieFile['tmp_name'], $moviePath)) {
        throw new Exception('Failed to save movie file');
    }
    if (!move_uploaded_file($posterFile['tmp_name'], $posterPath)) {
        unlink($moviePath); // Clean up movie file if poster fails
        throw new Exception('Failed to save poster image');
    }

    // Database connection
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Prepare data
    $title = $db->real_escape_string(cleanInput($_POST['title']));
    $description = $db->real_escape_string(cleanInput($_POST['description'] ?? ''));
    $releaseYear = (int)$_POST['release_year'];
    $duration = (int)$_POST['duration'];
    $movieType = $db->real_escape_string(cleanInput($_POST['movie_type']));
    $rating = $db->real_escape_string(cleanInput($_POST['rating']));
    $genres = isset($_POST['genres']) ? json_encode($_POST['genres']) : '[]';
    $directors = $db->real_escape_string(cleanInput($_POST['directors'] ?? ''));
    $cast = $db->real_escape_string(cleanInput($_POST['cast'] ?? ''));
    $language = $db->real_escape_string(cleanInput($_POST['language']));
    $trailerLink = $db->real_escape_string(cleanInput($_POST['trailer_link'] ?? ''));
    $uploadTime = date('Y-m-d H:i:s');

    // Insert record
    $query = "INSERT INTO pending_movies (
        title, description, release_year, duration, 
        movie_type, rating, genres, directors, cast, 
        language, trailer_link, movie_file_path, poster_path, 
        upload_time, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $db->error);
    }

    $stmt->bind_param(
        'ssiissssssssss',
        $title, $description, $releaseYear, $duration,
        $movieType, $rating, $genres, $directors, $cast,
        $language, $trailerLink, $movieFilename, $posterFilename,
        $uploadTime
    );

    if (!$stmt->execute()) {
        unlink($moviePath);
        unlink($posterPath);
        throw new Exception('Database insert failed: ' . $stmt->error);
    }

    $response['success'] = true;
    $response['message'] = 'Movie submitted successfully';
    $response['debug']['movie_id'] = $stmt->insert_id;

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
    $response['debug']['error'] = $e->getMessage();
    $response['debug']['trace'] = $e->getTraceAsString();
}

echo json_encode($response);
?>