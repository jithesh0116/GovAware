<?php
require_once __DIR__ . '/includes/header.php';

// Fetch 3 random/featured schemes from db
try {
    $stmt = $pdo->query("SELECT * FROM schemes ORDER BY id DESC LIMIT 3");
    $featured_schemes = $stmt->fetchAll();
} catch (Exception $e) {
    $featured_schemes = [];
}

// Fetch recent 3 notifications from db
try {
    $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 3");
    $recent_notifications = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_notifications = [];
}

// Counts for statistical overview
try {
    $total_schemes = $pdo->query("SELECT COUNT(*) FROM schemes")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() + 12500; // Simulated offset
    $total_feedbacks = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn() + 450; 
} catch (Exception $e) {
    $total_schemes = 7;
    $total_users = 12500;
    $total_feedbacks = 450;
}
?>

<!-- Hero Banner Area -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-left">
            <h1 class="hero-title">Empowering Citizens Through Welfare Schemes</h1>
            <p class="hero-subtitle">Discover, check eligibility, and apply for central and state government programs seamlessly.</p>
            
            <!-- Global Autocomplete Search Input Form -->
            <form action="/smart-gov-portal/schemes.php" method="GET">
                <div class="search-box-container">
                    <input type="text" id="search-input" name="search" autocomplete="off" placeholder="Search schemes (e.g. Kisan, Scholarship)...">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>
            
            <div style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/smart-gov-portal/eligibility.php" class="btn btn-secondary"><i class="fas fa-clipboard-check"></i> Check Eligibility Now</a>
                <a href="/smart-gov-portal/schemes.php" class="btn btn-outline">View All Schemes</a>
            </div>
        </div>
        
        <div class="hero-right flex-center" style="position: relative;">
            <i class="fas fa-landmark" style="font-size: 240px; color: rgba(15, 82, 186, 0.08);"></i>
        </div>
    </div>
</section>

<!-- Numerical Analytics Panel -->
<section style="padding: 0 0 60px 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_schemes; ?></div>
                <div class="stat-label">Total Schemes Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_users); ?>+</div>
                <div class="stat-label">Citizens Helped</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">All India</div>
                <div class="stat-label">Supported States</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_users * 3); ?>+</div>
                <div class="stat-label">Successful Inquiries</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Schemes Grid Section -->
<section class="section" style="background-color: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 36px; margin-bottom: 10px;">Featured Government Schemes</h2>
            <p style="color: var(--text-muted);">Explore the most sought-after welfare schemes active today.</p>
        </div>
        
        <div class="schemes-grid">
            <?php foreach ($featured_schemes as $scheme): 
                $isSaved = false;
                if (isUserLoggedIn()) {
                    $chk = $pdo->prepare("SELECT 1 FROM saved_schemes WHERE user_id = ? AND scheme_id = ?");
                    $chk->execute([$_SESSION['user_id'], $scheme['id']]);
                    $isSaved = (bool)$chk->fetchColumn();
                }
            ?>
            <div class="scheme-card">
                <div class="scheme-card-header">
                    <span class="scheme-badge <?php echo getCategoryBadgeClass($scheme['category']); ?>">
                        <?php echo htmlspecialchars($scheme['category']); ?>
                    </span>
                    <button class="scheme-bookmark <?php echo $isSaved ? 'active' : ''; ?>" 
                            onclick="toggleSaveScheme(this, <?php echo $scheme['id']; ?>)">
                        <i class="<?php echo $isSaved ? 'fas' : 'far'; ?> fa-bookmark"></i>
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
                        <li>
                            <span>Max Income:</span>
                            <strong><?php echo $scheme['max_income'] > 5000000 ? 'No Limit' : formatRupees($scheme['max_income']); ?></strong>
                        </li>
                    </ul>
                </div>
                
                <div class="scheme-card-footer">
                    <a href="/smart-gov-portal/scheme-detail.php?id=<?php echo $scheme['id']; ?>" class="btn btn-primary" style="flex-grow: 1;">Learn More</a>
                    <button class="btn btn-outline btn-compare-toggle" data-id="<?php echo $scheme['id']; ?>" 
                            onclick="toggleCompare(<?php echo $scheme['id']; ?>, '<?php echo addslashes($scheme['name']); ?>')">
                        <i class="fas fa-plus"></i> Compare
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Smart Call to Action and Latest System Notifications -->
<section class="section">
    <div class="container grid-cols-2">
        
        <!-- Smart Eligibility Checker Card Banner -->
        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border-radius: var(--radius-lg); padding: 40px; display: flex; flex-direction: column; justify-content: center; box-shadow: var(--shadow);">
            <h3 style="font-size: 30px; margin-bottom: 15px; color: white;"><i class="fas fa-calculator"></i> Check Eligibility Now</h3>
            <p style="margin-bottom: 30px; color: rgba(255, 255, 255, 0.9); font-size: 15px; line-height: 1.7;">Confused about which schemes you qualify for? Use our smart tool to verify instantly!</p>
            <div>
                <a href="/smart-gov-portal/eligibility.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> Check Eligibility Now</a>
            </div>
        </div>

        <!-- Latest Updates / Notifications List -->
        <div class="admin-card" style="margin-bottom: 0;">
            <h3 style="font-size: 24px; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 12px;"><i class="fas fa-newspaper"></i> Latest Scheme Updates & News</h3>
            
            <?php if (empty($recent_notifications)): ?>
                <p style="color: var(--text-muted); font-style: italic;">No recent announcements</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($recent_notifications as $notif): ?>
                        <div style="border-left: 4px solid var(--secondary); padding-left: 15px;">
                            <h4 style="font-size: 16px; margin-bottom: 4px;"><?php echo htmlspecialchars($notif['title']); ?></h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 6px;"><?php echo htmlspecialchars($notif['content']); ?></p>
                            <span style="font-size: 11px; color: var(--text-muted); font-weight: 500;">
                                <i class="far fa-calendar-alt"></i> Published on: <?php echo date('M d, Y', strtotime($notif['created_at'])); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<script>
function toggleSaveScheme(button, schemeId) {
    fetch('/smart-gov-portal/api/save_scheme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            scheme_id: schemeId,
            action: button.classList.contains('active') ? 'unsave' : 'save'
        })
    })
    .then(res => {
        if (res.status === 401) {
            alert('Please login to bookmark schemes.');
            window.location.href = '/smart-gov-portal/login.php';
            throw new Error('Unauthorized');
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const icon = button.querySelector('i');
            if (data.bookmarked) {
                button.classList.add('active');
                icon.className = 'fas fa-bookmark';
            } else {
                button.classList.remove('active');
                icon.className = 'far fa-bookmark';
            }
        } else {
            alert(data.error || 'Operation failed.');
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
