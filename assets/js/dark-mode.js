// Dark/Light Theme Handler

document.addEventListener('DOMContentLoaded', () => {
    const themeToggles = [
        document.getElementById('theme-toggle'),
        document.getElementById('theme-toggle-mobile')
    ].filter(el => el !== null);

    if (themeToggles.length === 0) return;

    const htmlElement = document.documentElement;
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Apply the saved theme on load
    htmlElement.setAttribute('data-theme', currentTheme);
    themeToggles.forEach(toggle => updateToggleIcon(toggle, currentTheme));

    // Add click event listener to the toggle buttons
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const activeTheme = htmlElement.getAttribute('data-theme');
            const newTheme = activeTheme === 'light' ? 'dark' : 'light';
            
            // Update attribute and persistence
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            themeToggles.forEach(t => updateToggleIcon(t, newTheme));
        });
    });
});

/**
 * Changes toggle icon state based on theme setting
 */
function updateToggleIcon(button, theme) {
    const icon = button.querySelector('i');
    if (theme === 'dark') {
        icon.className = 'fas fa-sun';
        button.style.borderColor = '#24324F';
    } else {
        icon.className = 'fas fa-moon';
        button.style.borderColor = '#E2E8F0';
    }
}
