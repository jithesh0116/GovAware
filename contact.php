<?php
require_once __DIR__ . '/includes/header.php';

$error = '';
$success = '';

// Handle Contact POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All form fields are mandatory.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $success = 'Thank you! Your feedback has been submitted successfully.';
        } catch (Exception $e) {
            $error = 'Failed to submit feedback: ' . $e->getMessage();
        }
    }
}
?>

<section class="hero" style="padding: 50px 0;">
    <div class="container" style="text-align: center;">
        <h1 class="hero-title" style="font-size: 38px;">Feedback & Contact Us</h1>
        <p class="hero-subtitle" style="margin-top: 10px;">Have questions or suggestions about the portal? Send us a message.</p>
    </div>
</section>

<div class="container section grid-cols-2">
    
    <!-- LEFT SIDE: CONTACT FORM -->
    <div class="admin-card" style="margin-bottom:0;">
        <h3 style="font-size: 20px; margin-bottom: 24px; color: var(--primary);"><i class="fas fa-paper-plane"></i> Submit Feedback</h3>
        
        <?php if (!empty($error)): ?>
            <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background-color: #D1FAE5; border: 1px solid #6EE7B7; color: #065F46; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="name">Your Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Anand Gowda" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Your Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="anand@gmail.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="subject">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Schemes Eligibility Inquiry" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="message">Message / Feedback</label>
                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </form>
    </div>

    <!-- RIGHT SIDE: CONTACT INFO & MAP PLACEHOLDER -->
    <div style="display:flex; flex-direction:column; gap:30px;">
        <div class="admin-card" style="margin-bottom:0;">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: var(--secondary);"><i class="fas fa-building"></i> Office Coordinates</h3>
            
            <div style="display:flex; flex-direction:column; gap:16px; font-size:15px;">
                <div>
                    <strong>GovAware Secretariat</strong><br>
                    <span style="color:var(--text-muted);"><i class="fas fa-map-marker-alt"></i> Vidhana Soudha, Dr. Ambedkar Veedhi, Bengaluru - 560001</span>
                </div>
                <div>
                    <strong>Support Helpline</strong><br>
                    <span style="color:var(--text-muted);"><i class="fas fa-phone-alt"></i> +91 80 1234 5678</span>
                </div>
                <div>
                    <strong>Working Hours</strong><br>
                    <span style="color:var(--text-muted);"><i class="far fa-clock"></i> Mon - Sat: 10:00 AM - 5:30 PM (Sunday Closed)</span>
                </div>
            </div>
        </div>

        <!-- Modern visual map visual helper -->
        <div class="admin-card flex-center" style="margin-bottom:0; height:240px; background-color:rgba(15, 82, 186, 0.03); border:1px dashed var(--border); text-align:center;">
            <div>
                <i class="fas fa-map-marked-alt" style="font-size:48px; color:var(--primary); margin-bottom:12px;"></i>
                <h4>Map Location Visual</h4>
                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Vidhana Soudha area map placeholder</p>
            </div>
        </div>
    </div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
