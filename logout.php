<?php
// Secure session destroy script
require_once __DIR__ . '/includes/auth.php';

logout();

// Redirect back to Home page
header("Location: index.php");
exit;
?>
