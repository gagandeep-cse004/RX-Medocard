// Set active class on navbar items based on current URL
document.addEventListener('DOMContentLoaded', function() {
    // Adjust navbar based on screen size on page load
    adjustNavbarForScreenSize();
    // Get current URL path
    const currentPath = window.location.pathname.toLowerCase();

    // Get all nav links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    // Loop through nav links and add active class if URL matches
    navLinks.forEach(link => {
        const href = link.getAttribute('href').toLowerCase();

        // Skip the Get Started button
        if (link.classList.contains('get-started-btn')) {
            return;
        }

        // Check if current path matches the link's href
        if (currentPath === href ||
            (href !== '/' && currentPath.startsWith(href)) ||
            (currentPath === '/' && href === '/home') ||
            (currentPath === '/home' && href === '/')) {

            // Add active class to parent li element
            link.parentElement.classList.add('active');
        }
    });

    // Handle mobile menu collapse when clicking nav items
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    // Close mobile menu when clicking on nav items that are not page navigation links
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            // Only close the menu for anchor links (links that start with #)
            // Don't close for page navigation links
            const href = this.getAttribute('href');
            if (href && href.startsWith('#') && window.innerWidth < 1101 && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (navbarCollapse.classList.contains('show') &&
            !navbarCollapse.contains(event.target) &&
            !navbarToggler.contains(event.target)) {
            navbarToggler.click();
        }
    });
});

// Add scroll effect to navbar
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header-modern');

    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
        header.classList.add('header-scrolled');

        // Also update logo size when scrolled
        const logoImage = document.querySelector('.logo-image');
        const logoText = document.querySelector('.logo-text');
        const logoTextWrapper = document.querySelector('.logo-text-wrapper');

        if (logoImage) logoImage.classList.add('scrolled');
        if (logoText) logoText.classList.add('scrolled');
        if (logoTextWrapper) logoTextWrapper.classList.add('scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
        header.classList.remove('header-scrolled');

        // Reset logo size when at top
        const logoImage = document.querySelector('.logo-image');
        const logoText = document.querySelector('.logo-text');
        const logoTextWrapper = document.querySelector('.logo-text-wrapper');

        if (logoImage) logoImage.classList.remove('scrolled');
        if (logoText) logoText.classList.remove('scrolled');
        if (logoTextWrapper) logoTextWrapper.classList.remove('scrolled');
    }
});

// Handle window resize events
window.addEventListener('resize', function() {
    // Reset mobile menu on window resize to desktop
    if (window.innerWidth >= 992) {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse.classList.contains('show')) {
            document.querySelector('.navbar-toggler').click();
        }
    }

    // Adjust navbar on resize
    adjustNavbarForScreenSize();
});

// Function to adjust navbar based on screen size
function adjustNavbarForScreenSize() {
    const header = document.querySelector('.header-modern');
    const navbar = document.querySelector('.navbar');
    const logoImage = document.querySelector('.logo-image');
    const logoText = document.querySelector('.logo-text');
    const logoTextWrapper = document.querySelector('.logo-text-wrapper');

    if (window.innerWidth < 576) {
        // Extra small devices
        navbar.style.padding = '0.5rem 0';
    } else if (window.innerWidth < 768) {
        // Small devices
        navbar.style.padding = '0.75rem 0';
    } else {
        // Medium and larger devices
        navbar.style.padding = '0.5rem 0';
    }

    // Call once on load
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
        header.classList.add('header-scrolled');

        // Also update logo size when scrolled
        if (logoImage) logoImage.classList.add('scrolled');
        if (logoText) logoText.classList.add('scrolled');
        if (logoTextWrapper) logoTextWrapper.classList.add('scrolled');
    }
}
