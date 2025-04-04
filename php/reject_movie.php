<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config_db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

$movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
if (!$movieId) {
    http_response_code(400);
    $response['message'] = 'Invalid movie ID';
    echo json_encode($response);
    exit;
}

try {
    $db = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $db->prepare("DELETE FROM pending_movies WHERE id = ?");
    $stmt->execute([$movieId]);

    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
        $response['message'] = 'Movie rejected successfully';
    } else {
        throw new Exception('Movie not found or already deleted');
    }
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>