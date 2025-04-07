<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config_db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Number of movies per page
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM approved_movies ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$totalMovies = $conn->query("SELECT COUNT(*) AS total FROM approved_movies")->fetch_assoc()['total'];
$totalPages = ceil($totalMovies / $limit);

if ($result->num_rows > 0) {
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    echo json_encode([
        'success' => true,
        'movies' => $movies,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No movies found']);
}

$conn->close();
?>