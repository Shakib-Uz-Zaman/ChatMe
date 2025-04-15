/**
 * Stand-alone ripple effect implementation 
 * This provides a direct implementation of the ripple effect without dependencies
 */
document.addEventListener('DOMContentLoaded', function() {
    // Create a style element for ripple animation
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes rippleEffect {
            0% {
                transform: scale(0);
                opacity: 0.8;
            }
            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Function to add ripple effect
    function addRippleEffect(element, event, isTouch = false) {
        // Check if a ripple already exists (to prevent multiple ripples)
        const existingRipple = element.querySelector('span[style*="rippleEffect"]');
        if (existingRipple) {
            return; // Don't create another ripple if one already exists
        }
        
        // Get coordinates
        const rect = element.getBoundingClientRect();
        let x, y;
        
        if (isTouch) {
            x = event.touches[0].clientX - rect.left;
            y = event.touches[0].clientY - rect.top;
        } else {
            x = event.clientX - rect.left;
            y = event.clientY - rect.top;
        }
        
        // Create ripple element
        const ripple = document.createElement('span');
        
        // Calculate the size of the ripple
        // Should be at least as large as the element
        const size = Math.max(rect.width, rect.height) * 2;
        
        // Position and style the ripple
        ripple.style.cssText = `
            position: absolute;
            top: ${y - size/2}px;
            left: ${x - size/2}px;
            width: ${size}px;
            height: ${size}px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            transform: scale(0);
            z-index: 9999;
            pointer-events: none;
            animation: rippleEffect 0.9s linear;
        `;
        
        // Ensure element can contain the ripple
        if (getComputedStyle(element).position === 'static') {
            element.style.position = 'relative';
        }
        element.style.overflow = 'hidden';
        
        // Add ripple to element
        element.appendChild(ripple);
        
        // Remove the ripple after animation completes
        setTimeout(() => {
            if (ripple && ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 900);
    }

    // Function to check if an element is a menu button (for both home and profile pages)
    function isMenuButton(element) {
        return element.classList.contains('navbar-menu-button') || 
               element.id === 'navbar-menu-button' ||
               element.id === 'menu-button';
    }

    // Apply to chat items
    const chatList = document.getElementById('chat-list');
    if (chatList) {
        // Add click handler for ripple
        chatList.addEventListener('mousedown', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                addRippleEffect(chatItem, e);
            }
        });
        
        // Add touch handler for mobile
        chatList.addEventListener('touchstart', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                addRippleEffect(chatItem, e, true);
            }
        });
    }
    
    // No ripple effect for search wrapper as requested
    
    // No ripple effect for navbar menu button as requested
    
    // Apply to all nav-link items
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(navLink => {
        // Add click handler for ripple
        navLink.addEventListener('mousedown', function(e) {
            addRippleEffect(navLink, e);
        });
        
        // Add touch handler for mobile
        navLink.addEventListener('touchstart', function(e) {
            addRippleEffect(navLink, e, true);
        });
    });
    
    // Apply to profile action buttons
    const profileActionBtns = document.querySelectorAll('.profile-action-btn');
    profileActionBtns.forEach(btn => {
        // Skip menu buttons (both in home and profile pages)
        if (isMenuButton(btn)) {
            return;
        }
        
        // Add click handler for ripple
        btn.addEventListener('mousedown', function(e) {
            addRippleEffect(btn, e);
        });
        
        // Add touch handler for mobile
        btn.addEventListener('touchstart', function(e) {
            addRippleEffect(btn, e, true);
        });
    });
    
    // Apply to logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        // Add click handler for ripple
        logoutBtn.addEventListener('mousedown', function(e) {
            addRippleEffect(logoutBtn, e);
        });
        
        // Add touch handler for mobile
        logoutBtn.addEventListener('touchstart', function(e) {
            addRippleEffect(logoutBtn, e, true);
        });
    }
    
    // Apply to share button
    const shareProfileBtn = document.getElementById('share-profile-btn');
    if (shareProfileBtn) {
        // Add click handler for ripple
        shareProfileBtn.addEventListener('mousedown', function(e) {
            addRippleEffect(shareProfileBtn, e);
        });
        
        // Add touch handler for mobile
        shareProfileBtn.addEventListener('touchstart', function(e) {
            addRippleEffect(shareProfileBtn, e, true);
        });
    }
    
    // Apply to other share profile button (for other users' profiles)
    const shareProfileOtherBtn = document.getElementById('share-profile-other-btn');
    if (shareProfileOtherBtn) {
        // Add click handler for ripple
        shareProfileOtherBtn.addEventListener('mousedown', function(e) {
            addRippleEffect(shareProfileOtherBtn, e);
        });
        
        // Add touch handler for mobile
        shareProfileOtherBtn.addEventListener('touchstart', function(e) {
            addRippleEffect(shareProfileOtherBtn, e, true);
        });
    }
    
    // Apply to user-menu
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        // Add click handler for ripple
        userMenu.addEventListener('mousedown', function(e) {
            addRippleEffect(userMenu, e);
        });
        
        // Add touch handler for mobile
        userMenu.addEventListener('touchstart', function(e) {
            addRippleEffect(userMenu, e, true);
        });
    }
});