document.addEventListener('DOMContentLoaded', function() {
    // User dropdown menu
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenuButton && userDropdown) {
        let timeout;
        
        // Show dropdown on click
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            userDropdown.classList.toggle('show');
        });
        
        // Handle mouseenter for the button
        userMenuButton.addEventListener('mouseenter', function() {
            clearTimeout(timeout);
            userDropdown.classList.add('show');
        });
        
        // Handle mouseenter for the dropdown itself to keep it open
        userDropdown.addEventListener('mouseenter', function() {
            clearTimeout(timeout);
        });
        
        // Handle mouseleave with delay
        const handleMouseLeave = function() {
            timeout = setTimeout(function() {
                userDropdown.classList.remove('show');
            }, 300); // 300ms delay before hiding
        };
        
        userMenuButton.addEventListener('mouseleave', handleMouseLeave);
        userDropdown.addEventListener('mouseleave', handleMouseLeave);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
