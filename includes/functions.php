<?php
// Shared Utility Functions (Strict English Version)

/**
 * Sanitize user input to prevent XSS attacks
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency in Indian Rupees (INR)
 */
function formatRupees($amount) {
    return '₹' . number_format($amount, 2, '.', ',');
}

/**
 * Shorten long descriptions safely
 */
function truncateText($text, $limit = 120) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

/**
 * Return Bootstrap Badge Class for Categories
 */
function getCategoryBadgeClass($category) {
    $category = strtolower(trim($category));
    switch ($category) {
        case 'agriculture':
            return 'badge-success';
        case 'education':
            return 'badge-primary';
        case 'healthcare':
            return 'badge-danger';
        case 'housing':
            return 'badge-warning';
        case 'social welfare':
            return 'badge-info';
        case 'self-employment':
            return 'badge-secondary';
        default:
            return 'badge-dark';
    }
}
?>
