<?php
require_once __DIR__ . '/includes/header.php';

$scheme_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($scheme_id <= 0) {
    header("Location: schemes.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
    $stmt->execute([$scheme_id]);
    $scheme = $stmt->fetch();

    if (!$scheme) {
        die("<div class='container' style='padding:80px; text-align:center;'><h2>Scheme Not Found</h2><p style='color:var(--text-muted); margin-top:10px;'>The requested government scheme does not exist or has been deleted.</p><br><a href='schemes.php' class='btn btn-primary'>Back to Schemes</a></div>");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Check bookmark state
$isSaved = false;
if (isUserLoggedIn()) {
    $chk = $pdo->prepare("SELECT 1 FROM saved_schemes WHERE user_id = ? AND scheme_id = ?");
    $chk->execute([$_SESSION['user_id'], $scheme_id]);
    $isSaved = (bool)$chk->fetchColumn();
}
?>

<!-- PRINT-ONLY CSS OVERLAYS FOR BEAUTIFUL PDF DOWNLOADS -->
<style>
@media print {
    header, footer, .alert-banner, .btn, .comparison-bar, .no-print {
        display: none !important;
    }
    body {
        background-color: white !important;
        color: black !important;
        font-size: 12pt;
        padding: 0;
        margin: 0;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    .print-header {
        display: block !important;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 30px;
        text-align: center;
    }
    .print-header h1 {
        font-size: 20pt;
        margin-bottom: 5px;
    }
    .print-card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin-bottom: 20px !important;
    }
    .scheme-badge {
        border: 1px solid #000 !important;
        color: black !important;
        background: transparent !important;
    }
    h2, h3 {
        page-break-after: avoid;
        color: black !important;
    }
}
.print-header {
    display: none;
}
.detail-grid {
    display: grid;
    grid-template-columns: 1.8fr 1.2fr;
    gap: 40px;
}
.detail-section-card {
    background-color: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--shadow);
}
.detail-section-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--primary);
    border-bottom: 2px solid rgba(15, 82, 186, 0.1);
    padding-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.criteria-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.criteria-item:last-child {
    border-bottom: none;
}
.criteria-item span {
    font-weight: 600;
}
.criteria-item strong {
    color: var(--text-muted);
}
</style>

<!-- PDF PRINTING HEADER -->
<div class="print-header">
    <h1>Smart Government Scheme Awareness Portal</h1>
    <p>Official Information Report | Generated on: <?php echo date('d-M-Y'); ?></p>
</div>

<div class="container section">
    
    <!-- Breadcrumbs / Action Header -->
    <div class="no-print detail-actions-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 40px;">
        <a href="schemes.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Schemes</a>
        
        <div style="display:flex; gap:12px;">
            <button class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-file-pdf"></i> Download Details as PDF
            </button>
            
            <button class="btn <?php echo $isSaved ? 'btn-secondary' : 'btn-primary'; ?>" id="detail-bookmark" onclick="toggleBookmarkDetails(<?php echo $scheme['id']; ?>)">
                <i class="<?php echo $isSaved ? 'fas' : 'far'; ?> fa-bookmark"></i> 
                <span id="bookmark-text"><?php echo $isSaved ? 'Unsave Scheme' : 'Save/Bookmark Scheme'; ?></span>
            </button>
        </div>
    </div>

    <!-- Scheme Main Title Block -->
    <div class="print-card detail-title-card" style="background-color:var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 40px; margin-bottom: 40px; box-shadow: var(--shadow); position:relative;">
        <span class="scheme-badge <?php echo getCategoryBadgeClass($scheme['category']); ?>" style="position:absolute; top: 25px; right: 40px;">
            <?php echo htmlspecialchars($scheme['category']); ?>
        </span>
        
        <h1 style="font-size: 36px; margin-bottom: 15px; color: var(--text); max-width: 80%;"><?php echo htmlspecialchars($scheme['name']); ?></h1>
        <p style="color:var(--text-muted); font-size:15px;"><i class="fas fa-map-marked-alt"></i> Scope: <strong><?php echo htmlspecialchars($scheme['state']); ?></strong></p>
    </div>

    <div class="detail-grid">
        
        <!-- Left Side: Main Text content -->
        <div class="detail-left">
            <!-- 1. Description -->
            <div class="detail-section-card">
                <h3 class="detail-section-title"><i class="fas fa-info-circle"></i> Scheme Description</h3>
                <p style="line-height: 1.8; text-align: justify; color:var(--text);"><?php echo nl2br(htmlspecialchars($scheme['description'])); ?></p>
            </div>

            <!-- 2. Benefits -->
            <div class="detail-section-card">
                <h3 class="detail-section-title"><i class="fas fa-gift"></i> Offered Benefits</h3>
                <p style="line-height: 1.8; text-align: justify; color:var(--text);"><?php echo nl2br(htmlspecialchars($scheme['benefits'])); ?></p>
            </div>

            <!-- 3. Documents required -->
            <div class="detail-section-card">
                <h3 class="detail-section-title"><i class="fas fa-file-invoice"></i> Required Documents</h3>
                <ul style="padding-left: 20px; line-height: 1.8; color:var(--text);">
                    <?php 
                        $docs = explode(',', $scheme['docs_required']);
                        foreach ($docs as $doc):
                    ?>
                        <li style="margin-bottom:8px;"><?php echo htmlspecialchars(trim($doc)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Right Side: Metadata / Side constraints and application link -->
        <div class="detail-right">
            <!-- 1. Criteria specifications card -->
            <div class="detail-section-card">
                <h3 class="detail-section-title"><i class="fas fa-check-double"></i> Rules & Constraints</h3>
                
                <div class="criteria-item">
                    <span>State Scope</span>
                    <strong><?php echo htmlspecialchars($scheme['state']); ?></strong>
                </div>
                <div class="criteria-item">
                    <span>Age Scope</span>
                    <strong><?php echo $scheme['min_age'] . ' - ' . $scheme['max_age']; ?> Years</strong>
                </div>
                <div class="criteria-item">
                    <span>Household Income</span>
                    <strong><?php echo $scheme['max_income'] > 5000000 ? 'No Limit' : 'Up to ' . formatRupees($scheme['max_income']); ?></strong>
                </div>
                <div class="criteria-item">
                    <span>Gender Requirements</span>
                    <strong><?php echo htmlspecialchars($scheme['gender_eligibility']); ?></strong>
                </div>
                <div class="criteria-item">
                    <span>Occupation Scope</span>
                    <strong><?php echo htmlspecialchars($scheme['allowed_occupations']); ?></strong>
                </div>
                <div class="criteria-item">
                    <span>Education Requirement</span>
                    <strong><?php echo htmlspecialchars($scheme['allowed_education']); ?></strong>
                </div>
            </div>

            <!-- 2. Application actions card -->
            <div class="detail-section-card" style="border-top:4px solid var(--secondary);">
                <h3 class="detail-section-title" style="color:var(--secondary);"><i class="fas fa-hourglass-half"></i> Application Deadline</h3>
                
                <p style="font-size: 20px; font-weight:700; margin-bottom: 24px;">
                    <?php 
                        if ($scheme['deadline']) {
                            echo date('d M, Y', strtotime($scheme['deadline']));
                        } else {
                            echo 'Ongoing / No strict deadline';
                        }
                    ?>
                </p>
                
                <a href="<?php echo htmlspecialchars($scheme['official_link']); ?>" target="_blank" class="btn btn-secondary no-print" style="width:100%; padding: 14px;">
                    <i class="fas fa-external-link-alt"></i> Apply on Official Site
                </a>
            </div>
        </div>

    </div>
</div>

<script>
function toggleBookmarkDetails(schemeId) {
    const btn = document.getElementById('detail-bookmark');
    const text = document.getElementById('bookmark-text');
    const isActive = btn.classList.contains('btn-secondary');

    fetch('/smart-gov-portal/api/save_scheme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            scheme_id: schemeId,
            action: isActive ? 'unsave' : 'save'
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
            const icon = btn.querySelector('i');
            if (data.bookmarked) {
                btn.className = 'btn btn-secondary';
                icon.className = 'fas fa-bookmark';
                text.textContent = "Unsave Scheme";
            } else {
                btn.className = 'btn btn-primary';
                icon.className = 'far fa-bookmark';
                text.textContent = "Save/Bookmark Scheme";
            }
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
