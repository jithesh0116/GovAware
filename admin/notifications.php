<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Route Guard: Admin role only
requireAdminLogin();

$success = '';
$error = '';

// Handle Delete Notification
if (isset($_GET['delete'])) {
    $notif_id = intval($_GET['delete']);
    if ($notif_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->execute([$notif_id]);
            header("Location: notifications.php?success=Announcement+deleted+successfully");
            exit;
        } catch (Exception $e) {
            $error = 'Failed to delete announcement: ' . $e->getMessage();
        }
    }
}

// Handle Add Notification POST Submission (Strict English Version)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $scheme_id = !empty($_POST['scheme_id']) ? intval($_POST['scheme_id']) : null;

    if (empty($title) || empty($content)) {
        $error = 'All title and content fields are mandatory.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO notifications (title, content, scheme_id) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $scheme_id]);
            header("Location: notifications.php?success=New+announcement+published+successfully");
            exit;
        } catch (Exception $e) {
            $error = 'Failed to publish announcement: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// Fetch all active notifications
try {
    $stmt = $pdo->query("
        SELECT n.*, s.name AS scheme_name 
        FROM notifications n 
        LEFT JOIN schemes s ON n.scheme_id = s.id 
        ORDER BY n.created_at DESC
    ");
    $announcements = $stmt->fetchAll();
} catch (Exception $e) {
    $announcements = [];
}

// Fetch schemes list for dropdown association
try {
    $schemes_stmt = $pdo->query("SELECT id, name FROM schemes ORDER BY name ASC");
    $schemes = $schemes_stmt->fetchAll();
} catch (Exception $e) {
    $schemes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Portal - Scheme Announcements</title>
    
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
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Control Dashboard</a></li>
                <li><a href="schemes.php"><i class="fas fa-folder-open"></i> Manage Schemes</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Manage Citizens</a></li>
                <li><a href="feedback.php"><i class="fas fa-comment-dots"></i> Citizen Feedbacks</a></li>
                <li><a href="notifications.php" class="active"><i class="fas fa-bullhorn"></i> Scheme Announcements</a></li>
                <li style="margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
                    <a href="/smart-gov-portal/index.php" target="_blank" style="color: #10b981;"><i class="fas fa-external-link-alt"></i> Go to Public Portal</a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-main">
            
            <div style="margin-bottom: 30px;">
                <h1 style="color:white; font-size: 28px;">Scheme Alerts & Announcements</h1>
                <p style="color:#94a3b8; margin-top:5px;">Publish real-time updates regarding deadlines, additions, or modifications to welfare schemes.</p>
            </div>

            <!-- Banners -->
            <?php if (!empty($error)): ?>
                <div style="background-color: #7f1d1d; border: 1px solid #f87171; color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div style="background-color: #064e3b; border: 1px solid #34d399; color: #a7f3d0; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="admin-grid-layout">
                
                <!-- COLUMN 1: NEW ANNOUNCEMENT PUBLISHER -->
                <div class="admin-card">
                    <h2 style="font-size: 20px; color: white; margin-bottom: 20px;"><i class="fas fa-bullhorn"></i> Broadcast Alert Bulletin</h2>
                    
                    <form method="POST" action="">
                        <!-- Title -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Alert Title*</label>
                            <input type="text" name="title" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. Yuva Nidhi Registrations Open" required>
                        </div>

                        <!-- Content -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Description*</label>
                            <textarea name="content" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" rows="4" placeholder="Brief explanation..." required></textarea>
                        </div>

                        <!-- Scheme Linkage Association Dropdown -->
                        <div class="form-group" style="margin-bottom: 24px;">
                            <label class="form-label" style="color: #94a3b8;">Link to Government Scheme (Optional)</label>
                            <select name="scheme_id" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;">
                                <option value="">None (General Portal Alert)</option>
                                <?php foreach ($schemes as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-secondary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Publish Portal Broadcast</button>
                    </form>
                </div>

                <!-- COLUMN 2: ACTIVE BULLETINS INDEX -->
                <div>
                    <h2 style="font-size: 20px; color: white; margin-bottom: 20px;"><i class="fas fa-list"></i> Active Broadcast Alerts</h2>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php if (empty($announcements)): ?>
                            <div class="admin-card" style="text-align: center; padding: 30px; color: #94a3b8;">
                                <i class="fas fa-broadcast-tower" style="font-size: 32px; margin-bottom: 8px;"></i>
                                <p>No active bulletins published.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($announcements as $an): ?>
                                <div class="admin-card" style="position: relative; margin-bottom:0; padding: 20px;">
                                    <a href="notifications.php?delete=<?php echo $an['id']; ?>" class="admin-action-btn btn-delete" style="position: absolute; top: 16px; right: 16px; width: 24px; height: 24px; font-size:11px;" title="Delete Broadcast" onclick="return confirm('Are you sure you want to delete this broadcast?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>

                                    <h4 style="color: white; font-size: 14px; margin-bottom: 4px; padding-right: 25px;"><?php echo htmlspecialchars($an['title']); ?></h4>
                                    
                                    <p style="font-size: 12px; color: #94a3b8; margin: 10px 0; line-height: 1.5;"><?php echo htmlspecialchars(truncateText($an['content'], 120)); ?></p>
                                    
                                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:10px; color:#64748b; border-top:1px solid #1e293b; padding-top:8px; margin-top:8px;">
                                        <span><i class="far fa-calendar-alt"></i> <?php echo date('d-M-Y', strtotime($an['created_at'])); ?></span>
                                        <?php if ($an['scheme_name']): ?>
                                            <span style="color:#3b82f6;"><i class="fas fa-link"></i> <?php echo htmlspecialchars($an['scheme_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </main>
        
    </div>

</body>
</html>
