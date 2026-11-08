<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Accept JSON payload or standard POST variables
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$age = isset($data['age']) && $data['age'] !== '' ? intval($data['age']) : null;
$gender = isset($data['gender']) ? trim($data['gender']) : '';
$state = isset($data['state']) ? trim($data['state']) : '';
$occupation = isset($data['occupation']) ? trim($data['occupation']) : '';
$income = isset($data['income']) && $data['income'] !== '' ? floatval($data['income']) : null;
$education = isset($data['education']) ? trim($data['education']) : '';

// Validation
if ($age === null || empty($gender) || empty($state) || empty($occupation) || $income === null || empty($education)) {
    http_response_code(400);
    echo json_encode(['error' => 'All eligibility check parameters are mandatory.']);
    exit;
}

try {
    // Query schemes database matching age, gender, state, and income limit
    $query = "
        SELECT * FROM schemes 
        WHERE min_age <= :age_min AND max_age >= :age_max
          AND (gender_eligibility = 'All' OR gender_eligibility = :gender)
          AND (state = 'All' OR state = :state)
          AND max_income >= :income
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'age_min' => $age,
        'age_max' => $age,
        'gender' => $gender,
        'state' => $state,
        'income' => $income
    ]);
    
    $allMatches = $stmt->fetchAll();
    
    // Now filter occupations and education matches dynamically in PHP
    $eligibleSchemes = [];

    foreach ($allMatches as $scheme) {
        $occEligible = false;
        $eduEligible = false;

        // Verify occupation
        if (empty($scheme['allowed_occupations']) || strtolower($scheme['allowed_occupations']) === 'all') {
            $occEligible = true;
        } else {
            $allowedOccs = array_map('strtolower', array_map('trim', explode(',', $scheme['allowed_occupations'])));
            if (in_array(strtolower($occupation), $allowedOccs)) {
                $occEligible = true;
            }
        }

        // Verify education
        if (empty($scheme['allowed_education']) || strtolower($scheme['allowed_education']) === 'all') {
            $eduEligible = true;
        } else {
            $allowedEdus = array_map('strtolower', array_map('trim', explode(',', $scheme['allowed_education'])));
            if (in_array(strtolower($education), $allowedEdus)) {
                $eduEligible = true;
            }
        }

        if ($occEligible && $eduEligible) {
            $eligibleSchemes[] = $scheme;
        }
    }

    echo json_encode($eligibleSchemes);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Eligibility engine error: ' . $e->getMessage()]);
}
?>
