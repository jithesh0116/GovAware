<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Check citizen login
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required. Please login first.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$scheme_id = isset($data['scheme_id']) ? intval($data['scheme_id']) : null;
$action = isset($data['action']) ? trim($data['action']) : '';

if (!$scheme_id || !in_array($action, ['save', 'unsave'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters. Scheme ID and action are required.']);
    exit;
}

try {
    if ($action === 'save') {
        // Attempt to insert bookmark
        $stmt = $pdo->prepare("INSERT IGNORE INTO saved_schemes (user_id, scheme_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $scheme_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Scheme saved successfully.', 'bookmarked' => true]);
    } else {
        // Attempt to remove bookmark
        $stmt = $pdo->prepare("DELETE FROM saved_schemes WHERE user_id = ? AND scheme_id = ?");
        $stmt->execute([$user_id, $scheme_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Scheme unsaved successfully.', 'bookmarked' => false]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
