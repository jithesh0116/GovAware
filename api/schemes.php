<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    // 1. Check if specific IDs are requested (For side-by-side comparison)
    if (isset($_GET['ids'])) {
        $raw_ids = explode(',', $_GET['ids']);
        $ids = array_filter(array_map('intval', $raw_ids));
        
        if (empty($ids)) {
            echo json_encode([]);
            exit;
        }

        // Generate placeholders e.g., ?,?,?
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM schemes WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // 2. Otherwise support advanced filter queries
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $state = isset($_GET['state']) ? trim($_GET['state']) : '';
    $income = isset($_GET['income']) && $_GET['income'] !== '' ? floatval($_GET['income']) : null;
    $occupation = isset($_GET['occupation']) ? trim($_GET['occupation']) : '';
    $age = isset($_GET['age']) && $_GET['age'] !== '' ? intval($_GET['age']) : null;
    $gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';

    $query = "SELECT * FROM schemes WHERE 1=1";
    $params = [];

    if ($category !== '') {
        $query .= " AND category = ?";
        $params[] = $category;
    }

    if ($state !== '') {
        $query .= " AND (state = ? OR state = 'All')";
        $params[] = $state;
    }

    if ($income !== null) {
        $query .= " AND max_income >= ?";
        $params[] = $income;
    }

    if ($age !== null) {
        $query .= " AND min_age <= ? AND max_age >= ?";
        $params[] = $age;
        $params[] = $age;
    }

    if ($gender !== '' && $gender !== 'All') {
        $query .= " AND (gender_eligibility = ? OR gender_eligibility = 'All')";
        $params[] = $gender;
    }

    if ($occupation !== '' && $occupation !== 'All') {
        $query .= " AND (allowed_occupations = 'All' OR FIND_IN_SET(?, allowed_occupations) > 0)";
        $params[] = $occupation;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    echo json_encode($stmt->fetchAll());

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database operation failed: ' . $e->getMessage()]);
}
?>
