<?php
// fetch_pending_movies.php
session_start();
require_once 'config_db.php';

// Set proper headers first
header('Content-Type: application/json');


try {
    $stmt = $conn->prepare("SELECT * FROM pending_movies ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'message' => '',
        'movies' => $movies
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'movies' => []
    ]);
    http_response_code(500);
}

exit;