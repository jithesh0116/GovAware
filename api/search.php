<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Search scheme names by pattern matching (Strict English Version)
    $stmt = $pdo->prepare("
        SELECT id, name, category 
        FROM schemes 
        WHERE name LIKE ? 
        LIMIT 6
    ");
    
    $searchPattern = "%$query%";
    $stmt->execute([$searchPattern]);
    $results = $stmt->fetchAll();

    $suggestions = [];
    foreach ($results as $row) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'category' => $row['category']
        ];
    }

    echo json_encode($suggestions);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed: ' . $e->getMessage()]);
}
?>
