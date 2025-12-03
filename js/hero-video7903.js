document.addEventListener('DOMContentLoaded', function() {
    // Get the video element
    const video = document.querySelector('.hero-background-video');

    // Ensure video is playing and muted
    video.muted = true;
    video.play().catch(error => {
        console.log('Auto-play was prevented:', error);
    });

    // Activate animation classes
    const animatedElements = document.querySelectorAll('.fade-in-hero');
    setTimeout(() => {
        animatedElements.forEach(element => {
            element.classList.add('active');
        });
    }, 100);

    // Ensure hero buttons are properly styled
    const heroButtons = document.querySelectorAll('.modern-hero-btn, .modern-hero-btn-outline');
    heroButtons.forEach(button => {
        // Force apply styles
        button.style.display = 'inline-block';
        button.style.textDecoration = 'none';
        button.style.position = 'relative';
        button.style.zIndex = '10';
        button.style.margin = '0 10px 15px 10px';

        if (button.classList.contains('modern-hero-btn')) {
            button.style.backgroundColor = '#ef476f';
            button.style.color = '#fff';
            button.style.border = '2px solid #ef476f';
        } else if (button.classList.contains('modern-hero-btn-outline')) {
            button.style.backgroundColor = 'transparent';
            button.style.color = '#fff';
            button.style.border = '2px solid #fff';
        }

        // Add click event to prevent default behavior if needed
        button.addEventListener('click', function(e) {
            // Allow normal navigation but ensure styles are maintained
            this.style.textDecoration = 'none';
        });

        // Add hover effect
        button.addEventListener('mouseenter', function() {
            if (this.classList.contains('modern-hero-btn')) {
                this.style.backgroundColor = '#e02c58';
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 10px 20px rgba(239, 71, 111, 0.3)';
            } else if (this.classList.contains('modern-hero-btn-outline')) {
                this.style.backgroundColor = '#fff';
                this.style.color = '#000';
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 10px 20px rgba(255, 255, 255, 0.2)';
            }
        });

        // Remove hover effect
        button.addEventListener('mouseleave', function() {
            if (this.classList.contains('modern-hero-btn')) {
                this.style.backgroundColor = '#ef476f';
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            } else if (this.classList.contains('modern-hero-btn-outline')) {
                this.style.backgroundColor = 'transparent';
                this.style.color = '#fff';
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    });
});
