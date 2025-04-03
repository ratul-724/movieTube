<?php
header('Content-Type: application/json');

// Database configuration
$config = [
    'servername' => "localhost",
    'username' => "root",
    'password' => "",
    'dbname' => "movie-tube"
];

$response = ['success' => false, 'message' => ''];

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response['message'] = "Invalid request method";
    echo json_encode($response);
    exit;
}

try {
    // Create database connection
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Validate required fields
    $required = ['title', 'release_year', 'drive_link', 'poster_link', 'movie_type', 'quality', 'language'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: " . $field);
        }
    }

    // Process and sanitize data
    $title = sanitizeInput($conn, $_POST['title']);
    $description = sanitizeInput($conn, $_POST['description'] ?? '');
    $release_year = (int)$_POST['release_year'];
    
    // Process duration
    $hours = isset($_POST['hours']) ? (int)$_POST['hours'] : 0;
    $minutes = isset($_POST['minutes']) ? (int)$_POST['minutes'] : 0;
    $duration_formatted = formatDuration($hours, $minutes);
    $duration_minutes = ($hours * 60) + $minutes;
    
    $drive_link = filter_var($_POST['drive_link'], FILTER_SANITIZE_URL);
    $poster_link = filter_var($_POST['poster_link'], FILTER_SANITIZE_URL);
    $trailer_link = !empty($_POST['trailer_link']) ? filter_var($_POST['trailer_link'], FILTER_SANITIZE_URL) : null;
    
    $movie_type = sanitizeInput($conn, $_POST['movie_type']);
    $quality = sanitizeInput($conn, $_POST['quality']);
    
    // Process genres
    if (empty($_POST['genres']) || !is_array($_POST['genres'])) {
        throw new Exception("Please select at least one genre");
    }
    $genres = implode(", ", array_map(function($genre) use ($conn) {
        return sanitizeInput($conn, $genre);
    }, $_POST['genres']));
    
    $directors = sanitizeInput($conn, $_POST['directors'] ?? '');
    $cast = sanitizeInput($conn, $_POST['cast'] ?? '');
    $language = sanitizeInput($conn, $_POST['language']);

    // Prepare and execute SQL
    $stmt = $conn->prepare("INSERT INTO `pending_movies` (
        `title`, `description`, `release_year`, `duration_formatted`, `duration_minutes`,
        `drive_link`, `poster_link`, `trailer_link`, `movie_type`, `quality`,
        `genres`, `directors`, `cast`, `language`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "ssisississssss",
        $title, $description, $release_year, $duration_formatted, $duration_minutes,
        $drive_link, $poster_link, $trailer_link, $movie_type, $quality,
        $genres, $directors, $cast, $language
    );
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Movie submitted successfully! It will be reviewed soon.';
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    echo json_encode($response);
    exit;
}

function sanitizeInput($conn, $input) {
    return $conn->real_escape_string(htmlspecialchars(trim($input)));
}

function formatDuration($hours, $minutes) {
    $duration = '';
    if ($hours > 0) {
        $duration .= $hours . 'h ';
    }
    if ($minutes > 0) {
        $duration .= $minutes . 'min';
    }
    return trim($duration);
}
?>