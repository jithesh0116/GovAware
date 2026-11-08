<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header("Location: dashboard.php");
    exit;
} elseif (isAdminLoggedIn()) {
    header("Location: admin/index.php");
    exit;
}

$citizen_error = '';
$admin_error = '';

// Check if a specific tab should be open by default
$default_role = isset($_GET['role']) && $_GET['role'] === 'admin' ? 'admin' : 'citizen';

// Handle login submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = trim($_POST['login_type'] ?? 'citizen');

    if ($login_type === 'citizen') {
        // 1. CITIZEN AUTHENTICATION
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $citizen_error = 'Email and Password are required.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_age'] = $user['age'];
                    $_SESSION['user_gender'] = $user['gender'];
                    $_SESSION['user_state'] = $user['state'];
                    $_SESSION['user_occupation'] = $user['occupation'];
                    $_SESSION['user_income'] = $user['annual_income'];
                    $_SESSION['user_education'] = $user['education'];
                    
                    $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'dashboard.php';
                    unset($_SESSION['redirect_url']);
                    
                    header("Location: " . $redirect);
                    exit;
                } else {
                    $citizen_error = 'Invalid email or password.';
                }
            } catch (Exception $e) {
                $citizen_error = 'Database error: ' . $e->getMessage();
            }
        }
    } elseif ($login_type === 'admin') {
        // 2. OFFICER / ADMIN AUTHENTICATION
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $admin_error = 'Username and Password are required.';
        } else {
            try {
                // Support logging in via EITHER Username OR Email for maximum convenience
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $admin = $stmt->fetch();

                if ($admin && $password === $admin['password']) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_user'] = $admin['username'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['is_admin'] = true;

                    header("Location: admin/index.php");
                    exit;
                } else {
                    $admin_error = 'Invalid officer credentials.';
                    $default_role = 'admin'; // Keep active tab on admin
                }
            } catch (Exception $e) {
                $admin_error = 'Database error: ' . $e->getMessage();
                $default_role = 'admin';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Tab switcher sliding aesthetics styling overrides -->
<style>
.tab-container {
    display: flex;
    background-color: var(--background);
    border: 1px solid var(--border);
    border-radius: 50px;
    padding: 4px;
    margin-bottom: 30px;
    position: relative;
}
.login-tab {
    flex: 1;
    padding: 10px 0;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    border-radius: 50px;
    color: var(--text-muted);
    transition: var(--transition);
    z-index: 2;
}
.login-tab.active {
    color: white;
}
.sliding-bg {
    position: absolute;
    top: 4px;
    left: 4px;
    bottom: 4px;
    width: calc(50% - 4px);
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: 50px;
    transition: var(--transition);
    z-index: 1;
}
.form-card {
    display: none;
}
.form-card.active {
    display: block;
    animation: fadeIn 0.4s ease-in-out;
}
</style>

<div class="container section flex-center" style="min-height: calc(100vh - 280px);">
    <div class="form-container" style="max-width: 480px; width: 100%;">
        
        <!-- Sliding Tabs Switcher -->
        <div class="tab-container">
            <div class="sliding-bg" id="sliding-bg" style="<?php echo $default_role === 'admin' ? 'transform: translateX(100%); background: linear-gradient(135deg, #3b82f6, #60a5fa);' : ''; ?>"></div>
            <div class="login-tab <?php echo $default_role === 'citizen' ? 'active' : ''; ?>" onclick="switchLoginTab('citizen')" id="tab-citizen">
                <i class="fas fa-user"></i> Citizen Login
            </div>
            <div class="login-tab <?php echo $default_role === 'admin' ? 'active' : ''; ?>" onclick="switchLoginTab('admin')" id="tab-admin">
                <i class="fas fa-user-shield"></i> Officer Login
            </div>
        </div>

        <!-- 1. CITIZEN LOGIN FORM CARD -->
        <div class="form-card <?php echo $default_role === 'citizen' ? 'active' : ''; ?>" id="card-citizen">
            <h2 class="form-title" style="font-size:24px;">Citizen Sign In</h2>
            <p class="form-subtitle" style="margin-bottom: 20px;">Access saved schemes & profile demographics</p>

            <?php if (!empty($citizen_error)): ?>
                <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $citizen_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="login_type" value="citizen">
                
                <div class="form-group">
                    <label class="form-label" for="citizen-email">Email Address</label>
                    <input type="email" id="citizen-email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="email">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" for="citizen-password">Password</label>
                    <input type="password" id="citizen-password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Citizen Login
                </button>
            </form>

            <p style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-muted);">
                Don't have an account? 
                <a href="register.php" style="font-weight: 600; color: var(--primary);">Register here</a>
            </p>
        </div>

        <!-- 2. OFFICER/ADMIN LOGIN FORM CARD -->
        <div class="form-card <?php echo $default_role === 'admin' ? 'active' : ''; ?>" id="card-admin">
            <h2 class="form-title" style="font-size:24px;">Officer Authentication</h2>
            <p class="form-subtitle" style="margin-bottom: 20px;">Portal administration control panel login</p>

            <?php if (!empty($admin_error)): ?>
                <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $admin_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="login_type" value="admin">
                
                <div class="form-group">
                    <label class="form-label" for="admin-username">Officer Username</label>
                    <input type="text" id="admin-username" name="username" class="form-control" placeholder="e.g. admin" required autocomplete="username">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" for="admin-password">Officer Password</label>
                    <input type="password" id="admin-password" name="password" class="form-control" placeholder="Security key" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-unlock"></i> Officer Login
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-muted);">
                Administrative portal access only.
            </p>
        </div>

    </div>
</div>

<script>
/**
 * Tab switcher sliding background and card animations
 */
function switchLoginTab(role) {
    const bg = document.getElementById('sliding-bg');
    const tabCitizen = document.getElementById('tab-citizen');
    const tabAdmin = document.getElementById('tab-admin');
    
    const cardCitizen = document.getElementById('card-citizen');
    const cardAdmin = document.getElementById('card-admin');

    if (role === 'admin') {
        bg.style.transform = 'translateX(100%)';
        bg.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
        
        tabCitizen.classList.remove('active');
        tabAdmin.classList.add('active');
        
        cardCitizen.classList.remove('active');
        cardAdmin.classList.add('active');
    } else {
        bg.style.transform = 'translateX(0)';
        bg.style.background = 'linear-gradient(135deg, var(--primary), var(--primary-light))';
        
        tabAdmin.classList.remove('active');
        tabCitizen.classList.add('active');
        
        cardAdmin.classList.remove('active');
        cardCitizen.classList.add('active');
    }
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
