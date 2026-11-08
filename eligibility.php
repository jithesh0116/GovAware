<?php
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero" style="padding: 50px 0;">
    <div class="container" style="text-align: center;">
        <h1 class="hero-title" style="font-size: 38px;">Smart Eligibility Checker</h1>
        <p class="hero-subtitle" style="margin-top: 10px;">Check which central & state schemes you are eligible for in just 3 quick steps</p>
    </div>
</section>

<div class="container section">
    
    <!-- Step Tracking Nodes -->
    <div class="checker-steps">
        <div class="step-node active" id="node-1">1</div>
        <div class="step-node" id="node-2">2</div>
        <div class="step-node" id="node-3">3</div>
    </div>

    <!-- Smart Eligibility Wizard Form Wrapper -->
    <form id="eligibility-wizard-form" onsubmit="evaluateEligibility(event)">
        
        <!-- STEP 1: DEMOGRAPHICS -->
        <div class="checker-step-card active" id="step-card-1">
            <h3 style="margin-bottom: 24px; color: var(--primary);"><i class="fas fa-user"></i> Demographics</h3>
            
            <div class="form-group">
                <label class="form-label" for="el-age">Your Age (Years)</label>
                <input type="number" id="el-age" min="1" max="120" class="form-control" required placeholder="e.g. 25" value="<?php echo isUserLoggedIn() ? $_SESSION['user_age'] : ''; ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="el-gender">Gender</label>
                <select id="el-gender" class="form-control" required>
                    <option value="Male" <?php echo (isUserLoggedIn() && $_SESSION['user_gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isUserLoggedIn() && $_SESSION['user_gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isUserLoggedIn() && $_SESSION['user_gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="button" class="btn btn-primary" onclick="changeWizardStep(2)">Next Step <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- STEP 2: STATE & INCOME -->
        <div class="checker-step-card" id="step-card-2">
            <h3 style="margin-bottom: 24px; color: var(--primary);"><i class="fas fa-wallet"></i> Financials & Location</h3>
            
            <div class="form-group">
                <label class="form-label" for="el-state">State of Residence</label>
                <select id="el-state" class="form-control" required>
                    <option value="Karnataka" <?php echo (isUserLoggedIn() && $_SESSION['user_state'] === 'Karnataka') ? 'selected' : ''; ?>>Karnataka</option>
                    <option value="Maharashtra" <?php echo (isUserLoggedIn() && $_SESSION['user_state'] === 'Maharashtra') ? 'selected' : ''; ?>>Maharashtra</option>
                    <option value="Tamil Nadu" <?php echo (isUserLoggedIn() && $_SESSION['user_state'] === 'Tamil Nadu') ? 'selected' : ''; ?>>Tamil Nadu</option>
                    <option value="Andhra Pradesh" <?php echo (isUserLoggedIn() && $_SESSION['user_state'] === 'Andhra Pradesh') ? 'selected' : ''; ?>>Andhra Pradesh</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="el-income">Annual Household Income (₹)</label>
                <input type="number" id="el-income" class="form-control" required placeholder="e.g. 180000" value="<?php echo isUserLoggedIn() ? intval($_SESSION['user_income']) : ''; ?>">
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                <button type="button" class="btn btn-outline" onclick="changeWizardStep(1)"><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="button" class="btn btn-primary" onclick="changeWizardStep(3)">Next Step <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- STEP 3: OCCUPATION & QUALIFICATIONS -->
        <div class="checker-step-card" id="step-card-3">
            <h3 style="margin-bottom: 24px; color: var(--primary);"><i class="fas fa-graduation-cap"></i> Qualifications</h3>
            
            <div class="form-group">
                <label class="form-label" for="el-occupation">Your Primary Occupation</label>
                <select id="el-occupation" class="form-control" required>
                    <option value="Farmer" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Farmer') ? 'selected' : ''; ?>>Farmer</option>
                    <option value="Laborer" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Laborer') ? 'selected' : ''; ?>>Laborer</option>
                    <option value="Student" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Student') ? 'selected' : ''; ?>>Student</option>
                    <option value="Self-Employed" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Self-Employed') ? 'selected' : ''; ?>>Self Employed</option>
                    <option value="Business Owner" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Business Owner') ? 'selected' : ''; ?>>Business Owner</option>
                    <option value="Housewife" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Housewife') ? 'selected' : ''; ?>>Housewife</option>
                    <option value="Unemployed" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Unemployed') ? 'selected' : ''; ?>>Unemployed</option>
                    <option value="Other" <?php echo (isUserLoggedIn() && $_SESSION['user_occupation'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="el-education">Highest Education Level</label>
                <select id="el-education" class="form-control" required>
                    <option value="Below 10th" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === 'Below 10th') ? 'selected' : ''; ?>>Below 10th</option>
                    <option value="10th Pass" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === '10th Pass') ? 'selected' : ''; ?>>10th Pass</option>
                    <option value="12th Pass" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === '12th Pass') ? 'selected' : ''; ?>>12th Pass</option>
                    <option value="Diploma" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                    <option value="Graduate" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === 'Graduate') ? 'selected' : ''; ?>>Graduate</option>
                    <option value="Post-Graduate" <?php echo (isUserLoggedIn() && $_SESSION['user_education'] === 'Post-Graduate') ? 'selected' : ''; ?>>Post-Graduate</option>
                </select>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                <button type="button" class="btn btn-outline" onclick="changeWizardStep(2)"><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="submit" class="btn btn-secondary"><i class="fas fa-calculator"></i> Find Eligible Schemes</button>
            </div>
        </div>

    </form>

    <!-- ELIGIBLE RESULTS GRIDS (LOADED DYNAMICALLY VIA JS AJAX FETCH) -->
    <div id="eligibility-results-section" style="margin-top: 60px; display: none;">
        <h2 style="font-size: 28px; text-align: center; margin-bottom: 30px;" id="results-headline"><i class="fas fa-check-circle" style="color:var(--secondary);"></i> Eligible Schemes Matched</h2>
        
        <div class="schemes-grid" id="matched-schemes-container">
            <!-- Populated via eligibility.js script -->
        </div>
    </div>

</div>

<!-- Wizard interaction controller script include -->
<script src="/smart-gov-portal/assets/js/eligibility.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
