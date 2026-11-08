<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Route Guard: Admin role only
requireAdminLogin();

$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';
$error = '';
$success = '';

// 1. DELETE ACTION
if ($action === 'delete') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM schemes WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: schemes.php?success=Scheme+deleted+successfully");
            exit;
        } catch (Exception $e) {
            $error = 'Failed to delete scheme: ' . $e->getMessage();
        }
    }
}

// Capture success messages
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// 2. ADD & EDIT PROCESSORS (Strict English Version)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $state = trim($_POST['state'] ?? 'All');
    $description = trim($_POST['description'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    
    $min_age = intval($_POST['min_age'] ?? 0);
    $max_age = intval($_POST['max_age'] ?? 120);
    $gender_eligibility = $_POST['gender_eligibility'] ?? 'All';
    $max_income = floatval($_POST['max_income'] ?? 99999999);
    
    $allowed_occupations = trim($_POST['allowed_occupations'] ?? 'All');
    $allowed_education = trim($_POST['allowed_education'] ?? 'All');
    
    $docs_required = trim($_POST['docs_required'] ?? '');
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $official_link = trim($_POST['official_link'] ?? '#');

    if (empty($name) || empty($category) || empty($description)) {
        $error = 'Mandatory name, category, and description fields are missing.';
    } else {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO schemes (
                        name, category, state, description, benefits, 
                        min_age, max_age, gender_eligibility, max_income, 
                        allowed_occupations, allowed_education, docs_required, 
                        deadline, official_link
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $name, $category, $state, $description, $benefits,
                    $min_age, $max_age, $gender_eligibility, $max_income,
                    $allowed_occupations, $allowed_education, $docs_required,
                    $deadline, $official_link
                ]);
                
                header("Location: schemes.php?success=Scheme+added+successfully");
                exit;
                
            } elseif ($action === 'edit') {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("
                    UPDATE schemes SET 
                        name = ?, category = ?, state = ?, description = ?, benefits = ?, 
                        min_age = ?, max_age = ?, gender_eligibility = ?, max_income = ?, 
                        allowed_occupations = ?, allowed_education = ?, docs_required = ?, 
                        deadline = ?, official_link = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $name, $category, $state, $description, $benefits,
                    $min_age, $max_age, $gender_eligibility, $max_income,
                    $allowed_occupations, $allowed_education, $docs_required,
                    $deadline, $official_link, $id
                ]);
                
                header("Location: schemes.php?success=Scheme+updated+successfully");
                exit;
            }
        } catch (Exception $e) {
            $error = 'Operation failed: ' . $e->getMessage();
        }
    }
}

// 3. RETRIEVE DATA FOR FORM IF EDIT
$scheme_data = null;
if ($action === 'edit') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
        $stmt->execute([$id]);
        $scheme_data = $stmt->fetch();
        
        if (!$scheme_data) {
            header("Location: schemes.php?error=Scheme+not+found");
            exit;
        }
    }
}

// 4. RETRIEVE ALL SCHEMES FOR DEFAULT LIST
try {
    $stmt = $pdo->query("SELECT * FROM schemes ORDER BY id DESC");
    $all_schemes = $stmt->fetchAll();
} catch (Exception $e) {
    $all_schemes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Portal - Manage Schemes</title>
    
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
                <li><a href="schemes.php" class="active"><i class="fas fa-folder-open"></i> Manage Schemes</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Manage Citizens</a></li>
                <li><a href="feedback.php"><i class="fas fa-comment-dots"></i> Citizen Feedbacks</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn"></i> Scheme Announcements</a></li>
                <li style="margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
                    <a href="/smart-gov-portal/index.php" target="_blank" style="color: #10b981;"><i class="fas fa-external-link-alt"></i> Go to Public Portal</a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-main">
            
            <!-- Alert Banners -->
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

            <!-- ACTION: LIST ALL SCHEMES -->
            <?php if ($action === 'list'): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px;">
                    <h1 style="color:white; font-size: 28px;">Government Schemes Registry</h1>
                    <a href="schemes.php?action=add" class="btn btn-secondary"><i class="fas fa-plus-circle"></i> Add New Scheme</a>
                </div>

                <div class="admin-card">
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Scheme Name</th>
                                <th>Category</th>
                                <th>Age Range</th>
                                <th>Max Income</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_schemes)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px;">No schemes registered in system database.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($all_schemes as $sch): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($sch['name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($sch['category']); ?></td>
                                        <td><?php echo $sch['min_age'] . ' - ' . $sch['max_age']; ?> Years</td>
                                        <td><?php echo $sch['max_income'] > 5000000 ? 'No Limit' : '₹' . number_format($sch['max_income']); ?></td>
                                        <td><?php echo $sch['deadline'] ? date('d-M-Y', strtotime($sch['deadline'])) : 'Ongoing'; ?></td>
                                        <td>
                                            <a href="schemes.php?action=edit&id=<?php echo $sch['id']; ?>" class="admin-action-btn btn-edit" title="Edit Scheme"><i class="fas fa-edit"></i></a>
                                            <a href="schemes.php?action=delete&id=<?php echo $sch['id']; ?>" class="admin-action-btn btn-delete" title="Delete Scheme" onclick="return confirm('Are you sure you want to permanently delete this scheme?')"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>

            <!-- ACTION: ADD / EDIT SCHEME FORM -->
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <div style="margin-bottom: 30px;">
                    <h1 style="color:white; font-size: 28px;"><?php echo $action === 'add' ? 'Add New Government Scheme' : 'Edit Government Scheme'; ?></h1>
                    <p style="color:#94a3b8; margin-top:5px;">Populate scheme parameters for citizen search.</p>
                </div>

                <div class="admin-card">
                    <form method="POST" action="">
                        
                        <!-- Scheme Name -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Scheme Name*</label>
                            <input type="text" name="name" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" value="<?php echo htmlspecialchars($scheme_data['name'] ?? ''); ?>" required>
                        </div>

                        <!-- Category -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Category*</label>
                            <input type="text" name="category" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. Agriculture" value="<?php echo htmlspecialchars($scheme_data['category'] ?? ''); ?>" required>
                        </div>

                        <!-- Description English -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Full Description*</label>
                            <textarea name="description" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" rows="4" required><?php echo htmlspecialchars($scheme_data['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Benefits English -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Offered Benefits</label>
                            <textarea name="benefits" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" rows="3"><?php echo htmlspecialchars($scheme_data['benefits'] ?? ''); ?></textarea>
                        </div>

                        <!-- ELIGIBILITY FIELDS -->
                        <h3 style="color:white; margin: 30px 0 15px; border-bottom: 1px solid #24324f; padding-bottom: 8px;"><i class="fas fa-calculator"></i> Eligibility Rule Settings</h3>
                        
                        <div class="admin-form-grid admin-form-grid-3">
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Min Age (Years)</label>
                                <input type="number" name="min_age" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" value="<?php echo intval($scheme_data['min_age'] ?? 0); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Max Age (Years)</label>
                                <input type="number" name="max_age" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" value="<?php echo intval($scheme_data['max_age'] ?? 120); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Gender Scope</label>
                                <select name="gender_eligibility" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;">
                                    <option value="All" <?php echo ($scheme_data['gender_eligibility'] ?? 'All') === 'All' ? 'selected' : ''; ?>>All (Everyone)</option>
                                    <option value="Male" <?php echo ($scheme_data['gender_eligibility'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($scheme_data['gender_eligibility'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="admin-form-grid admin-form-grid-3">
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Max Household Annual Income (₹)</label>
                                <input type="number" name="max_income" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. 300000" value="<?php echo floatval($scheme_data['max_income'] ?? 99999999); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">State Coverage</label>
                                <input type="text" name="state" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. Karnataka or All" value="<?php echo htmlspecialchars($scheme_data['state'] ?? 'All'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Application Deadline</label>
                                <input type="date" name="deadline" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" value="<?php echo $scheme_data['deadline'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="admin-form-grid admin-form-grid-2">
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Allowed Occupations (Comma-separated list or 'All')</label>
                                <input type="text" name="allowed_occupations" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. Farmer,Laborer,Unemployed" value="<?php echo htmlspecialchars($scheme_data['allowed_occupations'] ?? 'All'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: #94a3b8;">Allowed Education Levels (Comma-separated list or 'All')</label>
                                <input type="text" name="allowed_education" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="e.g. 10th Pass,12th Pass,Diploma" value="<?php echo htmlspecialchars($scheme_data['allowed_education'] ?? 'All'); ?>">
                            </div>
                        </div>

                        <!-- Documents Required -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="color: #94a3b8;">Required Documents (Comma-separated)*</label>
                            <input type="text" name="docs_required" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="Aadhaar card, Bank passbook" value="<?php echo htmlspecialchars($scheme_data['docs_required'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 30px;">
                            <label class="form-label" style="color: #94a3b8;">Official External Application Link</label>
                            <input type="url" name="official_link" class="form-control" style="background-color: #0b0f19; border-color: #24324f; color: white;" placeholder="https://website.gov.in" value="<?php echo htmlspecialchars($scheme_data['official_link'] ?? '#'); ?>">
                        </div>

                        <div style="display:flex; gap: 15px;">
                            <button type="submit" class="btn btn-secondary" style="flex-grow:1;"><i class="fas fa-save"></i> Save Government Scheme</button>
                            <a href="schemes.php" class="btn btn-outline" style="border-color:#24324f; color:white !important;">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </main>
        
    </div>

</body>
</html>
