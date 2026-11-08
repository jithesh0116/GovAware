<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Handle Registration POST Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $state = $_POST['state'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $income = floatval($_POST['income'] ?? 0);
    $education = $_POST['education'] ?? '';

    // Server-side validation (Strict English Version)
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || $age <= 0 || empty($gender) || empty($state) || empty($occupation) || $income < 0 || empty($education)) {
        $error = 'All fields are mandatory.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email is already registered!';
            } else {
                // Securely hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert = $pdo->prepare("
                    INSERT INTO users (name, email, password, age, gender, state, occupation, annual_income, education)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $insert->execute([$name, $email, $hashed_password, $age, $gender, $state, $occupation, $income, $education]);
                
                $success = 'Registration successful! You can now log in.';
            }
        } catch (Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container section">
    <div class="form-container">
        <h2 class="form-title">Create Account</h2>
        <p class="form-subtitle">Join GovAware to save schemes and track eligibility</p>
        
        <?php if (!empty($error)): ?>
            <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background-color: #D1FAE5; border: 1px solid #6EE7B7; color: #065F46; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <div style="margin-top: 8px;">
                    <a href="login.php" class="btn btn-secondary btn-sm" style="font-size:12px; padding: 4px 12px;">Log In Now</a>
                </div>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="" onsubmit="return validateRegistrationForm()">
            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Ramesh Kumar" required>
                <div class="form-feedback" id="name-feedback">Name is required.</div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="ramesh@gmail.com" required>
                <div class="form-feedback" id="email-feedback">Enter a valid email.</div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                <div class="form-feedback" id="password-feedback">Must be at least 6 characters.</div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <div class="form-feedback" id="confirm-feedback">Passwords do not match.</div>
            </div>

            <!-- Age & Gender Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" id="age" name="age" min="1" max="120" class="form-control" placeholder="e.g. 28" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- State -->
            <div class="form-group">
                <label class="form-label" for="state">State of Residence</label>
                <select id="state" name="state" class="form-control" required>
                    <option value="Karnataka">Karnataka</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Tamil Nadu">Tamil Nadu</option>
                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Kerala">Kerala</option>
                </select>
            </div>

            <!-- Occupation -->
            <div class="form-group">
                <label class="form-label" for="occupation">Occupation</label>
                <select id="occupation" name="occupation" class="form-control" required>
                    <option value="Farmer">Farmer</option>
                    <option value="Laborer">Laborer</option>
                    <option value="Student">Student</option>
                    <option value="Self-Employed">Self Employed</option>
                    <option value="Business Owner">Business Owner</option>
                    <option value="Housewife">Housewife</option>
                    <option value="Unemployed">Unemployed</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Annual Income -->
            <div class="form-group">
                <label class="form-label" for="income">Annual Household Income (₹)</label>
                <input type="number" id="income" name="income" class="form-control" placeholder="e.g. 150000" required>
            </div>

            <!-- Education -->
            <div class="form-group">
                <label class="form-label" for="education">Highest Education Level</label>
                <select id="education" name="education" class="form-control" required>
                    <option value="Below 10th">Below 10th</option>
                    <option value="10th Pass">10th Pass</option>
                    <option value="12th Pass">12th Pass</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Graduate">Graduate (B.A/B.Sc/B.E/etc.)</option>
                    <option value="Post-Graduate">Post-Graduate (M.A/M.Tech/M.C.A/etc.)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fas fa-user-plus"></i> Register Account
            </button>
        </form>

        <p style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-muted);">
            Already have an account? 
            <a href="login.php" style="font-weight: 600; color: var(--primary);">Log In</a>
        </p>
    </div>
</div>

<script>
function validateRegistrationForm() {
    let isValid = true;

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    const nameFeedback = document.getElementById('name-feedback');
    const emailFeedback = document.getElementById('email-feedback');
    const passwordFeedback = document.getElementById('password-feedback');
    const confirmFeedback = document.getElementById('confirm-feedback');

    nameFeedback.style.display = 'none';
    emailFeedback.style.display = 'none';
    passwordFeedback.style.display = 'none';
    confirmFeedback.style.display = 'none';

    if (name.length < 2) {
        nameFeedback.style.display = 'block';
        isValid = false;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        emailFeedback.style.display = 'block';
        isValid = false;
    }

    if (password.length < 6) {
        passwordFeedback.style.display = 'block';
        isValid = false;
    }

    if (password !== confirm) {
        confirmFeedback.style.display = 'block';
        isValid = false;
    }

    return isValid;
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
