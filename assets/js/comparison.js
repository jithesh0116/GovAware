// Side-by-Side Government Scheme Comparison Tool (Strict English Version)

// Initialize comparison session on load
let comparisonList = JSON.parse(sessionStorage.getItem('comparisonList')) || [];

document.addEventListener('DOMContentLoaded', () => {
    updateComparisonBar();
});

/**
 * Toggle a scheme in the comparison checklist
 */
function toggleCompare(schemeId, schemeName) {
    const index = comparisonList.indexOf(schemeId);
    
    if (index > -1) {
        // Remove from list
        comparisonList.splice(index, 1);
    } else {
        // Add to list, capping at 3
        if (comparisonList.length >= 3) {
            alert('You can only compare a maximum of 3 schemes at a time.');
            return;
        }
        comparisonList.push(schemeId);
    }

    sessionStorage.setItem('comparisonList', JSON.stringify(comparisonList));
    updateComparisonBar();
    updateCompareButtons();
}

/**
 * Update the visibility and count on the sticky comparison tray
 */
function updateComparisonBar() {
    const bar = document.getElementById('comparison-bar');
    const countSpan = document.getElementById('comparison-count');
    
    if (!bar) return;

    if (comparisonList.length > 0) {
        countSpan.textContent = comparisonList.length;
        bar.classList.add('active');
    } else {
        bar.classList.remove('active');
    }
}

/**
 * Sync page buttons with selected values
 */
function updateCompareButtons() {
    document.querySelectorAll('.btn-compare-toggle').forEach(btn => {
        const id = parseInt(btn.getAttribute('data-id'));
        if (comparisonList.includes(id)) {
            btn.innerHTML = '<i class="fas fa-minus"></i> Remove Compare';
            btn.classList.replace('btn-outline', 'btn-secondary');
        } else {
            btn.innerHTML = '<i class="fas fa-plus"></i> Compare';
            btn.classList.replace('btn-secondary', 'btn-outline');
        }
    });
}

/**
 * Empty the current comparison list
 */
function clearComparison() {
    comparisonList = [];
    sessionStorage.setItem('comparisonList', JSON.stringify(comparisonList));
    updateComparisonBar();
    updateCompareButtons();
}

/**
 * Build comparison matrix and reveal modal
 */
function showComparisonModal() {
    if (comparisonList.length === 0) return;

    const modal = document.getElementById('comparison-modal');
    const table = document.getElementById('comparison-table-content');
    
    if (!modal || !table) return;

    table.innerHTML = '<tr><td style="text-align:center; padding: 40px;"><span class="loader"></span></td></tr>';
    modal.classList.add('active');

    // Fetch schemes data dynamically using AJAX
    fetch(`/smart-gov-portal/api/schemes.php?ids=${comparisonList.join(',')}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                table.innerHTML = '<tr><td style="text-align:center; padding: 20px;">No schemes found to compare.</td></tr>';
                return;
            }

            // Fields to compare (Strict English Version)
            const rows = [
                { label: 'Scheme Name', key: 'name' },
                { label: 'Category', key: 'category' },
                { label: 'State Scope', key: 'state' },
                { label: 'Description', key: 'description' },
                { label: 'Benefits', key: 'benefits' },
                { label: 'Max Age Limit', key: 'max_age' },
                { label: 'Max Income Limit', key: 'max_income', format: (val) => val ? '₹' + parseFloat(val).toLocaleString('en-IN') : 'No Limit' },
                { label: 'Required Documents', key: 'docs_required' },
                { label: 'Application Deadline', key: 'deadline', format: (val) => val ? val : 'N/A' }
            ];

            let html = '';
            
            rows.forEach((row, rowIndex) => {
                html += `<tr><th>${row.label}</th>`;
                
                data.forEach(scheme => {
                    let val = scheme[row.key];

                    if (row.format) {
                        val = row.format(val);
                    }

                    // For the name row, include a view link
                    if (row.key === 'name') {
                        html += `<td><strong>${val}</strong><br><a href="/smart-gov-portal/scheme-detail.php?id=${scheme.id}" class="btn btn-outline" style="padding: 4px 8px; font-size:11px; margin-top:8px;" target="_blank"><i class="fas fa-external-link-alt"></i> View details</a></td>`;
                    } else {
                        html += `<td>${val}</td>`;
                    }
                });

                html += '</tr>';
            });

            table.innerHTML = html;
        })
        .catch(err => {
            console.error('Error rendering comparison details:', err);
            table.innerHTML = '<tr><td style="text-align:center; padding: 20px; color:#EF4444;">Failed to load schemes for comparison.</td></tr>';
        });
}

function closeComparisonModal() {
    const modal = document.getElementById('comparison-modal');
    if (modal) modal.classList.remove('active');
}
