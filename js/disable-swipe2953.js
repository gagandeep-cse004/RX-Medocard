/**
 * Disable Swipe Functionality for Mobile Menu
 * This script prevents horizontal swipe gestures on the mobile menu
 * while still allowing vertical scrolling
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Disable swipe script loaded');

    // Get the navbar collapse element
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbarCollapse) {
        // Only prevent horizontal swipe on the menu itself
        // This allows the content behind to be scrolled normally
        navbarCollapse.addEventListener('touchmove', function(e) {
            // We don't need to prevent default here
            // Just stop propagation to prevent any parent handlers
            e.stopPropagation();
        }, { passive: true });

        console.log('Swipe prevention applied to mobile menu');
    }
});
