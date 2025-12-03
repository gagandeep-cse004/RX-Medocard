/**
 * Mobile Menu JavaScript for RX MEDO CARD
 * This file provides enhanced functionality for the mobile menu
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Mobile menu script loaded');

    // Get elements
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    const navbarToggler = document.querySelector('.navbar-toggler');

    // We don't need to disable touch events here anymore
    // The CSS changes and disable-swipe.js handle this now

    // Close mobile menu when clicking the close button
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function() {
            console.log('Mobile menu close button clicked');
            if (navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.classList.add('collapsed');
                navbarToggler.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Enhance mobile menu links with animation delay
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach((link, index) => {
        // Add animation delay for staggered appearance
        link.style.transitionDelay = (index * 0.05) + 's';

        // Add click handler for mobile menu items
        link.addEventListener('click', function() {
            if (window.innerWidth < 992 && !this.getAttribute('href').startsWith('#')) {
                // Add loading animation for page navigation
                this.classList.add('nav-link-loading');

                // Optional: Add a subtle loading indicator
                const loadingIcon = document.createElement('span');
                loadingIcon.className = 'nav-link-loading-indicator';
                loadingIcon.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
                this.appendChild(loadingIcon);
            }
        });
    });

    // We don't need to add the mobile-menu-open class to the body anymore
    // as we want the content to be visible and scrollable behind the menu

    // Just observe the navbar collapse for other purposes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                // We can use this to log menu state changes
                console.log('Menu state changed:', navbarCollapse.classList.contains('show') ? 'open' : 'closed');
            }
        });
    });

    if (navbarCollapse) {
        observer.observe(navbarCollapse, { attributes: true });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992 &&
            navbarCollapse.classList.contains('show') &&
            !navbarCollapse.contains(event.target) &&
            !navbarToggler.contains(event.target)) {

            navbarToggler.click();
        }
    });

    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992 && navbarCollapse.classList.contains('show')) {
            navbarToggler.click();
        }
    });
});
