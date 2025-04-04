<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config_db.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Invalid request method');
    }

    $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
    if (!$movieId) {
        http_response_code(400);
        throw new Exception('Invalid movie ID');
    }

    $db = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Start transaction
    $db->beginTransaction();

    // Fetch movie from pending_movies
    $stmt = $db->prepare("SELECT * FROM pending_movies WHERE id = ?");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        throw new Exception('Movie not found in pending_movies');
    }

    // Remove the 'id' column if it's auto-incremented in approved_movies
    unset($movie['id']);

    // Insert into approved_movies
    $columns = implode(', ', array_keys($movie));
    $placeholders = implode(', ', array_fill(0, count($movie), '?'));
    $insertStmt = $db->prepare("INSERT INTO approved_movies ($columns) VALUES ($placeholders)");
    $insertStmt->execute(array_values($movie));

    // Delete from pending_movies
    $deleteStmt = $db->prepare("DELETE FROM pending_movies WHERE id = ?");
    $deleteStmt->execute([$movieId]);

    // Commit transaction
    $db->commit();

    $response['success'] = true;
    $response['message'] = 'Movie approved successfully';
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    error_log("Approval Error: " . $e->getMessage());
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>