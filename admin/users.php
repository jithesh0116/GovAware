<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Route Guard: Admin role only
requireAdminLogin();

$success = '';
$error = '';

// Handle Delete User request
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            header("Location: users.php?success=Citizen+account+deleted+successfully");
            exit;
        } catch (Exception $e) {
            $error = 'Failed to delete citizen record: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// Fetch all registered citizens
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $citizens = $stmt->fetchAll();
} catch (Exception $e) {
    $citizens = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Portal - Manage Citizens</title>
    
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
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Manage Citizens</a></li>
                <li><a href="feedback.php"><i class="fas fa-comment-dots"></i> Citizen Feedbacks</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn"></i> Scheme Announcements</a></li>
                <li style="margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
                    <a href="/smart-gov-portal/index.php" target="_blank" style="color: #10b981;"><i class="fas fa-external-link-alt"></i> Go to Public Portal</a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-main">
            
            <div style="margin-bottom: 30px;">
                <h1 style="color:white; font-size: 28px;">Registered Citizen Accounts</h1>
                <p style="color:#94a3b8; margin-top:5px;">Review profile demographics entered during account registration.</p>
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

            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Citizen Details</th>
                            <th>Demographics</th>
                            <th>Location</th>
                            <th>Professional Profile</th>
                            <th>Income</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citizens)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">No citizens have registered accounts on the portal yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citizens as $cit): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cit['name']); ?></strong><br>
                                        <span style="color:#94a3b8; font-size:12px;"><?php echo htmlspecialchars($cit['email']); ?></span>
                                    </td>
                                    <td><?php echo $cit['age']; ?> Yrs / <?php echo htmlspecialchars($cit['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($cit['state']); ?></td>
                                    <td>
                                        <strong>Occ:</strong> <?php echo htmlspecialchars($cit['occupation']); ?><br>
                                        <span style="color:#94a3b8; font-size:11px;">Edu: <?php echo htmlspecialchars($cit['education']); ?></span>
                                    </td>
                                    <td>₹<?php echo number_format($cit['annual_income']); ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($cit['created_at'])); ?></td>
                                    <td>
                                        <a href="users.php?delete=<?php echo $cit['id']; ?>" class="admin-action-btn btn-delete" title="Delete Account" onclick="return confirm('Are you sure you want to permanently delete this citizen\'s account?')"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

        </main>
        
    </div>

</body>
</html>
