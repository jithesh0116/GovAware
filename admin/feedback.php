<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Route Guard: Admin role only
requireAdminLogin();

$success = '';
$error = '';

// Handle Delete Feedback request
if (isset($_GET['delete'])) {
    $feedback_id = intval($_GET['delete']);
    if ($feedback_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
            $stmt->execute([$feedback_id]);
            header("Location: feedback.php?success=Feedback+message+deleted+successfully");
            exit;
        } catch (Exception $e) {
            $error = 'Failed to delete message: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// Fetch all feedbacks
try {
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
    $feedbacks = $stmt->fetchAll();
} catch (Exception $e) {
    $feedbacks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Portal - Citizen Feedback</title>
    
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
                <li><a href="feedback.php" class="active"><i class="fas fa-comment-dots"></i> Citizen Feedbacks</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn"></i> Scheme Announcements</a></li>
                <li style="margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
                    <a href="/smart-gov-portal/index.php" target="_blank" style="color: #10b981;"><i class="fas fa-external-link-alt"></i> Go to Public Portal</a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-main">
            
            <div style="margin-bottom: 30px;">
                <h1 style="color:white; font-size: 28px;">Citizen Inquiries & Feedback</h1>
                <p style="color:#94a3b8; margin-top:5px;">Review contact messages and suggestions submitted by portal users.</p>
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

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php if (empty($feedbacks)): ?>
                    <div class="admin-card" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="far fa-comments" style="font-size: 48px; margin-bottom: 12px;"></i>
                        <p>No feedback messages have been received yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($feedbacks as $fb): ?>
                        <div class="admin-card" style="position: relative;">
                            <!-- Delete button -->
                            <a href="feedback.php?delete=<?php echo $fb['id']; ?>" class="admin-action-btn btn-delete" style="position: absolute; top: 24px; right: 24px;" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                            <h3 style="color: white; font-size: 18px; margin-bottom: 6px; padding-right: 40px;"><?php echo htmlspecialchars($fb['subject']); ?></h3>
                            
                            <p style="font-size: 13px; color: #94a3b8; margin-bottom: 15px;">
                                From: <strong><?php echo htmlspecialchars($fb['name']); ?></strong> (<?php echo htmlspecialchars($fb['email']); ?>) | 
                                Submitted: <em><?php echo date('d-M-Y h:i A', strtotime($fb['submitted_at'])); ?></em>
                            </p>
                            
                            <div style="background-color: #0b0f19; border: 1px solid #24324f; padding: 16px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #cbd5e1; white-space: pre-wrap;"><?php echo htmlspecialchars($fb['message']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>
        
    </div>

</body>
</html>
