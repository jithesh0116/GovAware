<?php
// Secure PDO Database Connection file for Smart Government Scheme Awareness Portal

$host = '127.0.0.1';
$db   = 'smart_gov_db';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log error. For a student project, provide a nice diagnostic response.
    die("<div style='font-family: sans-serif; padding: 20px; background: #fff5f5; border: 1px solid #ffc9c9; border-radius: 8px; margin: 40px auto; max-width: 600px;'>
        <h2 style='color: #c92a2a; margin-top: 0;'>Database Connection Failed!</h2>
        <p>Could not connect to the database <strong>smart_gov_db</strong> on host <strong>$host</strong>.</p>
        <p><strong>Common Fixes:</strong></p>
        <ol style='line-height: 1.6;'>
            <li>Ensure <strong>XAMPP</strong> (Apache & MySQL) is running.</li>
            <li>Import the SQL schema file located at <code>database/schema.sql</code> inside <strong>phpMyAdmin</strong>.</li>
            <li>Verify database credentials inside <code>includes/db.php</code>.</li>
        </ol>
        <p style='font-size: 13px; color: #868e96;'>Error detail: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>");
}
?>
