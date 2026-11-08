// Smart Eligibility Wizard Controller (Strict English Version)

/**
 * Handle wizard step transitions (Cards toggles & Navigation tracker updates)
 */
function changeWizardStep(stepNumber) {
    // Basic validations for fields in active card before step-up
    if (stepNumber === 2) {
        const age = document.getElementById('el-age').value;
        if (!age || age <= 0) {
            alert('Please enter a valid age before moving forward.');
            return;
        }
    }
    if (stepNumber === 3) {
        const income = document.getElementById('el-income').value;
        if (income === '' || income < 0) {
            alert('Please enter a valid household annual income before moving forward.');
            return;
        }
    }

    // Toggle step cards
    document.querySelectorAll('.checker-step-card').forEach(card => card.classList.remove('active'));
    document.getElementById(`step-card-${stepNumber}`).classList.add('active');

    // Update trackers
    document.querySelectorAll('.step-node').forEach((node, idx) => {
        const currentIdx = idx + 1;
        node.className = 'step-node';
        if (currentIdx === stepNumber) {
            node.classList.add('active');
        } else if (currentIdx < stepNumber) {
            node.classList.add('completed');
        }
    });
}

/**
 * Handle asynchronous API calculation and render results
 */
function evaluateEligibility(event) {
    event.preventDefault();

    const resultsSection = document.getElementById('eligibility-results-section');
    const container = document.getElementById('matched-schemes-container');
    
    if (!resultsSection || !container) return;

    // Show loading spinner
    resultsSection.style.display = 'block';
    showSpinner(container);

    // Scroll smoothly to results
    resultsSection.scrollIntoView({ behavior: 'smooth' });

    // Collect variables
    const payload = {
        age: parseInt(document.getElementById('el-age').value),
        gender: document.getElementById('el-gender').value,
        state: document.getElementById('el-state').value,
        income: parseFloat(document.getElementById('el-income').value),
        occupation: document.getElementById('el-occupation').value,
        education: document.getElementById('el-education').value
    };

    // Query server API
    fetch('/smart-gov-portal/api/eligibility.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network error or invalid inputs.');
        }
        return response.json();
    })
    .then(schemes => {
        container.innerHTML = '';

        if (schemes.length === 0) {
            container.innerHTML = `
                <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 40px; border-style: dashed; border-width: 2px;">
                    <i class="fas fa-frown" style="font-size: 48px; color: var(--text-muted); margin-bottom: 15px;"></i>
                    <h4>No Eligible Schemes Found</h4>
                    <p style="color: var(--text-muted); font-size:14px; margin-top:8px;">
                        Try adjusting your annual income or parameters to see wider central welfare schemes.
                    </p>
                </div>
            `;
            return;
        }

        // Draw card templates dynamically (Strict English Version)
        schemes.forEach(scheme => {
            const name = scheme.name;
            const category = scheme.category;
            const description = scheme.description;
            
            // Format income label
            const incomeLimit = parseFloat(scheme.max_income);
            const incomeText = incomeLimit > 5000000 
                ? 'No Limit' 
                : '₹' + incomeLimit.toLocaleString('en-IN');

            const card = document.createElement('div');
            card.className = 'scheme-card';
            card.innerHTML = `
                <div class="scheme-card-header">
                    <span class="scheme-badge badge-primary">${category}</span>
                </div>
                <div class="scheme-card-body">
                    <h3 class="scheme-card-title">${name}</h3>
                    <p class="scheme-card-desc">${description}</p>
                    <ul class="scheme-meta-list">
                        <li>
                            <span>Scope:</span>
                            <strong>${scheme.state}</strong>
                        </li>
                        <li>
                            <span>Age Group:</span>
                            <strong>${scheme.min_age} - ${scheme.max_age} Years</strong>
                        </li>
                        <li>
                            <span>Max Income:</span>
                            <strong>${incomeText}</strong>
                        </li>
                    </ul>
                </div>
                <div class="scheme-card-footer">
                    <a href="/smart-gov-portal/scheme-detail.php?id=${scheme.id}" class="btn btn-primary" style="flex-grow:1;" target="_blank">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                </div>
            `;
            container.appendChild(card);
        });
    })
    .catch(err => {
        console.error('Eligibility engine error:', err);
        container.innerHTML = `
            <div class="admin-card" style="grid-column: 1 / -1; text-align: center; color: #EF4444; padding: 40px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 12px;"></i>
                <p>Failed to calculate eligibility details. Please check your system connections.</p>
            </div>
        `;
    });
}
