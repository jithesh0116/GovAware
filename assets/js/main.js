// Core JavaScript Functionality for GovAware Portal

document.addEventListener('DOMContentLoaded', () => {
    // Implement micro-interactions & simple page fade-in
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.4s ease-in-out';
    
    // Trigger paint
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 50);

    // Dynamic Active Nav Links
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });

    // Mobile Drawer Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const drawerClose = document.getElementById('drawer-close');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');

    if (menuToggle && mobileDrawer && drawerOverlay) {
        menuToggle.addEventListener('click', () => {
            mobileDrawer.classList.add('active');
            drawerOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    }

    if (drawerClose && mobileDrawer && drawerOverlay) {
        const closeDrawer = () => {
            mobileDrawer.classList.remove('active');
            drawerOverlay.classList.remove('active');
            document.body.style.overflow = '';
        };
        drawerClose.addEventListener('click', closeDrawer);
        drawerOverlay.addEventListener('click', closeDrawer);
    }

    // Dynamic Active Drawer Links
    const drawerLinks = document.querySelectorAll('.drawer-links a');
    drawerLinks.forEach(link => {
        if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href'))) {
            drawerLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });

    // Filter Sidebar Toggle on Mobile
    const filterHeader = document.querySelector('.filter-toggle-header');
    const filterSidebar = document.querySelector('.filter-sidebar');
    
    if (filterHeader && filterSidebar) {
        // Collapsed by default on mobile
        if (window.innerWidth <= 768) {
            filterSidebar.classList.add('collapsed');
        }
        
        filterHeader.addEventListener('click', (e) => {
            if (e.target.closest('.clear-filters-link')) {
                return;
            }
            if (window.innerWidth <= 768) {
                filterSidebar.classList.toggle('collapsed');
            }
        });
    }
});

/**
 * Global helper to show loading spinners on dynamic sections
 */
function showSpinner(containerElement) {
    if (containerElement) {
        containerElement.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; min-height: 200px; width: 100%;">
                <span class="loader"></span>
            </div>
        `;
    }
}
