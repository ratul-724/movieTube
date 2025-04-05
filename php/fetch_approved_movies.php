<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config_db.php';

// Fetch movies
$sql = "SELECT * FROM approved_movies ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    echo json_encode(['success' => true, 'movies' => $movies]);
} else {
    echo json_encode(['success' => false, 'message' => 'No movies found']);
}

$conn->close();
?>