<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Route Guard: Citizens only
requireUserLogin();

// Now include header
require_once __DIR__ . '/includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch saved schemes for this citizen (Strict English Version)
try {
    $stmt = $pdo->prepare("
        SELECT s.* FROM schemes s
        JOIN saved_schemes ss ON s.id = ss.scheme_id
        WHERE ss.user_id = ?
        ORDER BY ss.saved_at DESC
    ");
    $stmt->execute([$user_id]);
    $saved_schemes = $stmt->fetchAll();
} catch (Exception $e) {
    $saved_schemes = [];
}
?>

<div class="container section">
    <div style="margin-bottom: 40px;">
        <h1>Welcome Citizen, <span style="color:var(--primary);"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</h1>
        <p style="color:var(--text-muted); margin-top:5px;">Manage your account details and view bookmarked welfare schemes</p>
    </div>

    <div class="dashboard-layout">
        
        <!-- SIDEBAR PROFILE DETAILS CARD -->
        <aside class="dashboard-sidebar">
            <div class="user-profile-summary">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                <h3 style="font-size: 18px; color: var(--text);"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top:4px;"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            </div>
            
            <h4 style="font-size: 15px; margin-bottom: 15px; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">My Profile Information</h4>
            
            <div style="display:flex; flex-direction:column; gap:12px; font-size:14px;">
                <div>
                    <span style="color:var(--text-muted);">State Scope:</span><br>
                    <strong><?php echo htmlspecialchars($_SESSION['user_state']); ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Age Group:</span><br>
                    <strong><?php echo $_SESSION['user_age']; ?> Years</strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Gender:</span><br>
                    <strong><?php echo htmlspecialchars($_SESSION['user_gender']); ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Occupation:</span><br>
                    <strong><?php echo htmlspecialchars($_SESSION['user_occupation']); ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Annual Income:</span><br>
                    <strong><?php echo formatRupees($_SESSION['user_income']); ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Education:</span><br>
                    <strong><?php echo htmlspecialchars($_SESSION['user_education']); ?></strong>
                </div>
            </div>
            
            <a href="logout.php" class="btn btn-outline" style="width:100%; margin-top:30px; border-color:#EF4444; color:#EF4444 !important;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </aside>

        <!-- MAIN DASHBOARD CONTENT AREA -->
        <main>
            <!-- Statistical Summary Banner -->
            <div class="admin-stat-grid" style="grid-template-columns: 1fr; margin-bottom: 40px;">
                <div class="admin-stat-card" style="margin-bottom:0;">
                    <div class="admin-stat-icon" style="background-color:rgba(16, 185, 129, 0.1); color:var(--secondary);">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="admin-stat-info">
                        <h3><?php echo count($saved_schemes); ?></h3>
                        <p>Bookmarked Schemes</p>
                    </div>
                </div>
            </div>

            <!-- Bookmarked Schemes List -->
            <h2 style="font-size:24px; margin-bottom:24px; border-bottom:1px solid var(--border); padding-bottom:12px;"><i class="fas fa-heart" style="color:#EF4444;"></i> My Bookmarked Schemes</h2>
            
            <?php if (empty($saved_schemes)): ?>
                <div class="admin-card" style="text-align: center; padding: 60px; border-style:dashed; border-width: 2px;">
                    <i class="far fa-folder-open" style="font-size: 48px; color: var(--text-muted); margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">No Saved Schemes</h3>
                    <p style="color: var(--text-muted); margin-bottom: 24px;">You have not bookmarked any government schemes yet.</p>
                    <a href="schemes.php" class="btn btn-primary"><i class="fas fa-search"></i> Explore Schemes</a>
                </div>
            <?php else: ?>
                <div class="schemes-grid dashboard-schemes-grid">
                    <?php foreach ($saved_schemes as $scheme): ?>
                        <div class="scheme-card" id="saved-card-<?php echo $scheme['id']; ?>">
                            <div class="scheme-card-header">
                                <span class="scheme-badge <?php echo getCategoryBadgeClass($scheme['category']); ?>">
                                    <?php echo htmlspecialchars($scheme['category']); ?>
                                </span>
                                <button class="scheme-bookmark active" onclick="removeDashboardBookmark(<?php echo $scheme['id']; ?>)">
                                    <i class="fas fa-trash-alt" style="color:var(--text-muted);"></i>
                                </button>
                            </div>
                            <div class="scheme-card-body">
                                <h3 class="scheme-card-title"><?php echo htmlspecialchars($scheme['name']); ?></h3>
                                <p class="scheme-card-desc"><?php echo htmlspecialchars($scheme['description']); ?></p>
                                
                                <ul class="scheme-meta-list">
                                    <li>
                                        <span>State:</span>
                                        <strong><?php echo htmlspecialchars($scheme['state']); ?></strong>
                                    </li>
                                    <li>
                                        <span>Age Group:</span>
                                        <strong><?php echo $scheme['min_age'] . ' - ' . $scheme['max_age']; ?> Yrs</strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="scheme-card-footer">
                                <a href="/smart-gov-portal/scheme-detail.php?id=<?php echo $scheme['id']; ?>" class="btn btn-primary" style="flex-grow:1;">Learn More</a>
                                <button class="btn btn-outline" style="border-color:#EF4444; color:#EF4444 !important;" onclick="removeDashboardBookmark(<?php echo $scheme['id']; ?>)">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
        
    </div>
</div>

<script>
function removeDashboardBookmark(schemeId) {
    if (!confirm("Are you sure you want to remove this bookmark?")) return;

    fetch('/smart-gov-portal/api/save_scheme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            scheme_id: schemeId,
            action: 'unsave'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const card = document.getElementById(`saved-card-${schemeId}`);
            if (card) {
                card.style.animation = 'fadeIn 0.3s reverse';
                setTimeout(() => {
                    card.remove();
                    if (document.querySelectorAll('.scheme-card').length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
