<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Route Guard: Admin role only
requireAdminLogin();

// Fetch admin stats
try {
    $schemes_count = $pdo->query("SELECT COUNT(*) FROM schemes")->fetchColumn();
    $users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $feedback_count = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
    $notifications_count = $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
} catch (Exception $e) {
    $schemes_count = $users_count = $feedback_count = $notifications_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Portal - Dashboard</title>
    
    <link rel="stylesheet" href="/smart-gov-portal/assets/css/style.css">
    <link rel="stylesheet" href="/smart-gov-portal/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">

    <!-- Top Admin Header -->
    <header class="admin-header">
        <div class="admin-brand">
            <i class="fas fa-user-shield"></i> GovAware Officer Portal
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 14px; color: #94a3b8;"><i class="fas fa-user-circle"></i> Role: <strong><?php echo htmlspecialchars($_SESSION['admin_role']); ?></strong></span>
            <a href="/smart-gov-portal/logout.php" class="btn btn-outline" style="border-color:#ef4444; color:#ef4444 !important; padding:6px 12px; font-size:13px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="admin-layout">
        
        <!-- SIDE NAVIGATION BAR -->
        <aside class="admin-sidebar">
            <ul class="admin-menu">
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Control Dashboard</a></li>
                <li><a href="schemes.php"><i class="fas fa-folder-open"></i> Manage Schemes</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Manage Citizens</a></li>
                <li><a href="feedback.php"><i class="fas fa-comment-dots"></i> Citizen Feedbacks</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn"></i> Scheme Announcements</a></li>
                <li style="margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
                    <a href="/smart-gov-portal/index.php" target="_blank" style="color: #10b981;"><i class="fas fa-external-link-alt"></i> Go to Public Portal</a>
                </li>
            </ul>
        </aside>

        <!-- MAIN DASHBOARD CONTENT GRID -->
        <main class="admin-main">
            <div style="margin-bottom: 40px;">
                <h1 style="font-size: 32px; font-weight:800; color:white;">Officer Control Panel</h1>
                <p style="color: #94a3b8; margin-top:5px;">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong>. Oversee citizen registry and government schemes.</p>
            </div>

            <!-- Stats grid overview -->
            <div class="admin-stat-grid">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $schemes_count; ?></h3>
                        <p>Total Schemes</p>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $users_count; ?></h3>
                        <p>Registered Citizens</p>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $feedback_count; ?></h3>
                        <p>Inquiries / Feedbacks</p>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo $notifications_count; ?></h3>
                        <p>Active Alerts</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Shortcuts Layout -->
            <div class="admin-card">
                <h2 style="font-size: 20px; margin-bottom: 20px; color: white;">Quick Management Shortcuts</h2>
                
                <div class="admin-shortcut-grid">
                    <a href="schemes.php?action=add" class="btn btn-secondary" style="padding: 20px; flex-direction: column; font-size:16px;">
                        <i class="fas fa-plus-circle" style="font-size: 24px; margin-bottom: 8px;"></i> Add New Government Scheme
                    </a>
                    
                    <a href="notifications.php" class="btn btn-primary" style="padding: 20px; flex-direction: column; font-size:16px; background-color:#3b82f6;">
                        <i class="fas fa-broadcast-tower" style="font-size: 24px; margin-bottom: 8px;"></i> Broadcast Announcement
                    </a>
                    
                    <a href="feedback.php" class="btn btn-outline" style="padding: 20px; flex-direction: column; font-size:16px; border-color:#24324f; color:white !important;">
                        <i class="fas fa-inbox" style="font-size: 24px; margin-bottom: 8px; color:#f59e0b;"></i> Check Citizen Inquiries
                    </a>
                </div>
            </div>
        </main>
        
    </div>

</body>
</html>
