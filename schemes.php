<?php
require_once __DIR__ . '/includes/header.php';

// Capture dynamic search / filter params
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$state = trim($_GET['state'] ?? '');
$income = isset($_GET['income']) && $_GET['income'] !== '' ? floatval($_GET['income']) : '';
$occupation = trim($_GET['occupation'] ?? '');
$age = isset($_GET['age']) && $_GET['age'] !== '' ? intval($_GET['age']) : '';
$gender = trim($_GET['gender'] ?? '');

// Build the sql query
$query = "SELECT * FROM schemes WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_pattern = "%$search%";
    $params[] = $search_pattern;
    $params[] = $search_pattern;
}

if ($category !== '' && $category !== 'All') {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($state !== '' && $state !== 'All') {
    $query .= " AND (state = ? OR state = 'All')";
    $params[] = $state;
}

if ($income !== '') {
    $query .= " AND max_income >= ?";
    $params[] = $income;
}

if ($age !== '') {
    $query .= " AND min_age <= ? AND max_age >= ?";
    $params[] = $age;
    $params[] = $age;
}

if ($gender !== '' && $gender !== 'All') {
    $query .= " AND (gender_eligibility = ? OR gender_eligibility = 'All')";
    $params[] = $gender;
}

if ($occupation !== '' && $occupation !== 'All') {
    $query .= " AND (allowed_occupations = 'All' OR FIND_IN_SET(?, allowed_occupations) > 0)";
    $params[] = $occupation;
}

$query .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $schemes = $stmt->fetchAll();
} catch (Exception $e) {
    $schemes = [];
    $error = $e->getMessage();
}

// Get categories for filter lists
try {
    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM schemes");
    $categories = $cat_stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}
?>

<section class="hero" style="padding: 50px 0;">
    <div class="container" style="text-align: center;">
        <h1 class="hero-title" style="font-size: 38px;">Explore Government Schemes</h1>
        <p class="hero-subtitle" style="margin-top: 10px;">Filter, search, compare, and bookmark social welfare programs</p>
    </div>
</section>

<div class="container section">
    <div class="dashboard-layout">
        
        <!-- SIDEBAR FILTERS SYSTEM -->
        <aside class="filter-sidebar">
            <h3 style="font-size: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;" class="filter-toggle-header">
                <span><i class="fas fa-sliders-h"></i> Filter Schemes</span>
                <i class="fas fa-chevron-down filter-chevron" style="display: none;"></i>
                <a href="/smart-gov-portal/schemes.php" class="clear-filters-link" style="font-size: 12px; font-weight: 500; color:#EF4444;"><i class="fas fa-sync-alt"></i> Clear</a>
            </h3>
            
            <form method="GET" action="">
                <!-- Keyword search inside sidebar -->
                <div class="filter-group">
                    <label class="form-label">Keyword</label>
                    <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="e.g. Awas, Kisan">
                </div>

                <!-- Category -->
                <div class="filter-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="All">All</option>
                        <?php foreach ($categories as $cat): 
                            $val = $cat['category'];
                            $selected = ($category === $val) ? 'selected' : '';
                        ?>
                            <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($val); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- State -->
                <div class="filter-group">
                    <label class="form-label">State</label>
                    <select name="state" class="form-control">
                        <option value="All">All</option>
                        <option value="Karnataka" <?php echo $state === 'Karnataka' ? 'selected' : ''; ?>>Karnataka</option>
                        <option value="Maharashtra" <?php echo $state === 'Maharashtra' ? 'selected' : ''; ?>>Maharashtra</option>
                        <option value="Tamil Nadu" <?php echo $state === 'Tamil Nadu' ? 'selected' : ''; ?>>Tamil Nadu</option>
                        <option value="Andhra Pradesh" <?php echo $state === 'Andhra Pradesh' ? 'selected' : ''; ?>>Andhra Pradesh</option>
                    </select>
                </div>

                <!-- Max Income -->
                <div class="filter-group">
                    <label class="form-label">Max Income Limit (₹)</label>
                    <input type="number" name="income" class="form-control" value="<?php echo htmlspecialchars($income); ?>" placeholder="e.g. 200000">
                </div>

                <!-- Age -->
                <div class="filter-group">
                    <label class="form-label">Age Limit</label>
                    <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($age); ?>" placeholder="e.g. 25">
                </div>

                <!-- Gender -->
                <div class="filter-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="All">All</option>
                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Occupation -->
                <div class="filter-group">
                    <label class="form-label">Occupation</label>
                    <select name="occupation" class="form-control">
                        <option value="All">All</option>
                        <option value="Farmer" <?php echo $occupation === 'Farmer' ? 'selected' : ''; ?>>Farmer</option>
                        <option value="Laborer" <?php echo $occupation === 'Laborer' ? 'selected' : ''; ?>>Laborer</option>
                        <option value="Student" <?php echo $occupation === 'Student' ? 'selected' : ''; ?>>Student</option>
                        <option value="Self-Employed" <?php echo $occupation === 'Self-Employed' ? 'selected' : ''; ?>>Self Employed</option>
                        <option value="Unemployed" <?php echo $occupation === 'Unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </form>
        </aside>

        <!-- SCHEMES RESULTS GRID -->
        <main>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <p style="font-weight: 600; color: var(--text-muted);">
                    Showing <span style="color:var(--primary);"><?php echo count($schemes); ?></span> schemes matched
                </p>
            </div>

            <?php if (empty($schemes)): ?>
                <div class="admin-card" style="text-align: center; padding: 60px;">
                    <i class="fas fa-search-minus" style="font-size: 60px; color: var(--text-muted); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">No Matched Schemes</h3>
                    <p style="color: var(--text-muted);">No government schemes match your criteria.</p>
                </div>
            <?php else: ?>
                <div class="schemes-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <?php foreach ($schemes as $scheme): 
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
            <?php endif; ?>
        </main>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    updateCompareButtons();
});

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
