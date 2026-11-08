<?php
// Global Footer Template for Smart Government Portal (Strict English Version)
?>

    <footer>
        <div class="container footer-grid">
            <div class="footer-column">
                <h3><i class="fas fa-landmark"></i> GovAware Portal</h3>
                <p style="margin-top: 15px; font-size: 14px; line-height: 1.8; color: #cbd5e1;">Bridging the gap between citizens and government welfare initiatives with smart digital solutions.</p>
            </div>
            
            <div class="footer-column">
                <h3>Quick Navigation</h3>
                <ul class="footer-links">
                    <li><a href="/smart-gov-portal/index.php">Home</a></li>
                    <li><a href="/smart-gov-portal/about.php">About Us</a></li>
                    <li><a href="/smart-gov-portal/schemes.php">Government Schemes</a></li>
                    <li><a href="/smart-gov-portal/eligibility.php">Eligibility Checker</a></li>
                    <li><a href="/smart-gov-portal/contact.php">Feedback & Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Important Links</h3>
                <ul class="footer-links">
                    <li><a href="https://india.gov.in" target="_blank">National Portal of India</a></li>
                    <li><a href="https://karnataka.gov.in" target="_blank">Karnataka State Portal</a></li>
                    <li><a href="https://sevasindhu.karnataka.gov.in" target="_blank">Seva Sindhu Services</a></li>
                    <li><a href="/smart-gov-portal/admin/login.php"><i class="fas fa-user-shield"></i> Officer Login</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Contact Support</h3>
                <p style="font-size: 14px; margin-bottom: 12px; color: #cbd5e1;"><i class="fas fa-map-marker-alt"></i> Vidhana Soudha, Bengaluru, Karnataka - 560001</p>
                <p style="font-size: 14px; margin-bottom: 12px; color: #cbd5e1;"><i class="fas fa-envelope"></i> support-govportal@karnataka.gov.in</p>
                <p style="font-size: 14px; color: #cbd5e1;"><i class="fas fa-phone-alt"></i> +91 80 1234 5678</p>
            </div>
        </div>
        
        <div class="container" style="text-align: center; border-top: 1px solid #1e293b; padding-top: 20px; font-size: 13px; color: #94a3b8;">
            © <?php echo date('Y'); ?> GovAware Portal. Designed for MCA Full Stack Project.
        </div>
    </footer>

    <!-- STICKY COMPARISON FLOATING MANAGER BAR -->
    <div class="comparison-bar" id="comparison-bar">
        <div class="comparison-bar-info">
            <i class="fas fa-balance-scale"></i> <span id="comparison-count">0</span> / 3 Schemes selected
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary btn-sm" onclick="showComparisonModal()">Compare</button>
            <button class="btn btn-outline btn-sm" onclick="clearComparison()">Clear All</button>
        </div>
    </div>

    <!-- COMPARISON DIALOG MODAL -->
    <div class="modal-overlay" id="comparison-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-balance-scale"></i> Compare Schemes</h2>
                <button class="modal-close" onclick="closeComparisonModal()">✕</button>
            </div>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Select up to 3 schemes to compare side-by-side</p>
            
            <div style="overflow-x: auto;">
                <table class="comparison-table" id="comparison-table-content">
                    <!-- Loaded dynamically via JS -->
                </table>
            </div>
        </div>
    </div>

    <!-- Core JavaScript assets -->
    <script src="/smart-gov-portal/assets/js/main.js?v=1.0.5"></script>
    <script src="/smart-gov-portal/assets/js/dark-mode.js?v=1.0.5"></script>
    <script src="/smart-gov-portal/assets/js/search.js?v=1.0.5"></script>
    <script src="/smart-gov-portal/assets/js/comparison.js?v=1.0.5"></script>
</body>
</html>
