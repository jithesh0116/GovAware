<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch the latest system notification/banner
try {
    $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 1");
    $latest_notification = $stmt->fetch();
} catch (Exception $e) {
    $latest_notification = null;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Government Scheme Awareness Portal</title>
    
    <!-- CSS styles with cache buster -->
    <link rel="stylesheet" href="/smart-gov-portal/assets/css/style.css?v=1.0.5">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Dynamic Notification / Ticker Banner -->
    <?php if ($latest_notification): ?>
    <div class="alert-banner">
        <div class="container alert-banner-flex">
            <div class="news-ticker">
                <span class="news-badge"><i class="fas fa-bullhorn"></i> New</span>
                <div class="ticker-scroll-container">
                    <a href="/smart-gov-portal/schemes.php" class="ticker-text">
                        <?php echo htmlspecialchars($latest_notification['title']); ?>: 
                        <span class="ticker-desc"><?php echo htmlspecialchars(truncateText($latest_notification['content'], 120)); ?></span>
                    </a>
                </div>
            </div>
            <button class="switch-btn" style="border: none; padding: 2px 8px; font-size: 11px;" onclick="this.parentElement.parentElement.remove()">✕</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Navigation Header -->
    <header>
        <div class="container navbar">
            <!-- Portal Logo -->
            <a href="/smart-gov-portal/index.php" class="logo">
                <i class="fas fa-landmark" style="font-size: 24px; color: var(--primary);"></i>
                <div class="logo-text">GovAware</div>
            </a>
            
            <!-- Navbar Navigation Links (Desktop Only) -->
            <ul class="nav-links desktop-nav">
                <li><a href="/smart-gov-portal/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/smart-gov-portal/about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="/smart-gov-portal/schemes.php" class="<?php echo $current_page == 'schemes.php' ? 'active' : ''; ?>">Government Schemes</a></li>
                <li><a href="/smart-gov-portal/eligibility.php" class="<?php echo $current_page == 'eligibility.php' ? 'active' : ''; ?>">Eligibility Checker</a></li>
                <li><a href="/smart-gov-portal/contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Feedback & Contact</a></li>
            </ul>

            <!-- Navigation Controls & Auth Buttons (Desktop Only) -->
            <div class="nav-actions desktop-nav">
                <!-- Theme Switcher Button -->
                <button class="switch-btn" id="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- User Session State -->
                <?php if (isUserLoggedIn()): ?>
                    <a href="/smart-gov-portal/dashboard.php" class="btn btn-outline"><i class="fas fa-user-circle"></i> Dashboard</a>
                    <a href="/smart-gov-portal/logout.php" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i></a>
                <?php elseif (isAdminLoggedIn()): ?>
                    <a href="/smart-gov-portal/admin/index.php" class="btn btn-secondary"><i class="fas fa-tools"></i> Admin Panel</a>
                    <a href="/smart-gov-portal/logout.php" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="/smart-gov-portal/login.php" class="btn btn-outline">Login</a>
                    <a href="/smart-gov-portal/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Navigation Controls (Mobile Only) -->
            <div class="mobile-nav-controls">
                <button class="switch-btn" id="theme-toggle-mobile" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-drawer" id="mobile-drawer">
        <div class="drawer-header">
            <div class="logo-text">
                <i class="fas fa-landmark"></i> GovAware
            </div>
            <button class="drawer-close" id="drawer-close" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="drawer-content">
            <ul class="drawer-links">
                <li><a href="/smart-gov-portal/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/smart-gov-portal/about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i> About Us</a></li>
                <li><a href="/smart-gov-portal/schemes.php" class="<?php echo $current_page == 'schemes.php' ? 'active' : ''; ?>"><i class="fas fa-graduation-cap"></i> Government Schemes</a></li>
                <li><a href="/smart-gov-portal/eligibility.php" class="<?php echo $current_page == 'eligibility.php' ? 'active' : ''; ?>"><i class="fas fa-clipboard-check"></i> Eligibility Checker</a></li>
                <li><a href="/smart-gov-portal/contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>"><i class="fas fa-comment-dots"></i> Feedback & Contact</a></li>
            </ul>
            <div class="drawer-actions">
                <?php if (isUserLoggedIn()): ?>
                    <a href="/smart-gov-portal/dashboard.php" class="btn btn-outline" style="width: 100%;"><i class="fas fa-user-circle"></i> Dashboard</a>
                    <a href="/smart-gov-portal/logout.php" class="btn btn-primary" style="width: 100%; margin-top: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php elseif (isAdminLoggedIn()): ?>
                    <a href="/smart-gov-portal/admin/index.php" class="btn btn-secondary" style="width: 100%;"><i class="fas fa-tools"></i> Admin Panel</a>
                    <a href="/smart-gov-portal/logout.php" class="btn btn-primary" style="width: 100%; margin-top: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="/smart-gov-portal/login.php" class="btn btn-outline" style="width: 100%;">Login</a>
                    <a href="/smart-gov-portal/register.php" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="drawer-overlay" id="drawer-overlay"></div>
