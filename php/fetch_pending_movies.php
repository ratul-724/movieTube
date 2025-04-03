<?php
// fetch_pending_movies.php
session_start();
require_once 'config_db.php';

// Set proper headers first
header('Content-Type: application/json');

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in'])) {
//     echo json_encode([
//         'success' => false,
//         'message' => 'Access denied',
//         'movies' => []
//     ]);
//     exit;
// }

try {
    $stmt = $conn->prepare("SELECT * FROM pending_movies ORDER BY submission_date DESC");
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