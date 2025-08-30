/**
 * Theme Toggle Functionality
 * Handles switching between light and dark themes and saves preference to localStorage
 */

document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    
    // Check for saved user preference, if any, on load of the website
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    // Apply the saved theme
    if (savedTheme === 'dark') {
        html.classList.add('dark');
        html.setAttribute('data-theme', 'dark');
    } else {
        html.classList.remove('dark');
        html.setAttribute('data-theme', 'light');
    }
    
    // Toggle theme on button click
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            
            const isDark = html.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            html.setAttribute('data-theme', isDark ? 'dark' : 'light');
            
            // Dispatch a custom event that other scripts can listen for
            document.dispatchEvent(new CustomEvent('themeChange', { 
                detail: { theme: isDark ? 'dark' : 'light' } 
            }));
        });
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            
            // Toggle icon between menu and close
            const menuIcon = mobileMenuButton.querySelector('i');
            if (menuIcon) {
                if (isExpanded) {
                    menuIcon.classList.remove('bi-x-lg');
                    menuIcon.classList.add('bi-list');
                } else {
                    menuIcon.classList.remove('bi-list');
                    menuIcon.classList.add('bi-x-lg');
                }
            }
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (mobileMenu && !mobileMenu.contains(e.target) && 
            mobileMenuButton && !mobileMenuButton.contains(e.target) && 
            !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
            const menuIcon = mobileMenuButton.querySelector('i');
            if (menuIcon) {
                menuIcon.classList.remove('bi-x-lg');
                menuIcon.classList.add('bi-list');
            }
        }
    });
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Close mobile menu if open
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    const menuIcon = mobileMenuButton.querySelector('i');
                    if (menuIcon) {
                        menuIcon.classList.remove('bi-x-lg');
                        menuIcon.classList.add('bi-list');
                    }
                }
            }
        });
    });
});

// Helper function to check if dark mode is enabled
function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { isDarkMode };
}
