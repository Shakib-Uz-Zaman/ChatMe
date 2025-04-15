// Fix JS errors and improve performance

// Stop iOS from zooming on focus
const addInputTypesNoZoom = function() {
    // Create a style element
    const style = document.createElement('style');
    // WebKit hack
    style.appendChild(document.createTextNode(''));
    // Add the style element to the document
    document.head.appendChild(style);
    
    // Add CSS rules to prevent zooming - this is an ultra-aggressive approach
    style.sheet.insertRule(`
        input[type="text"],
        input[type="email"],
        input[type="url"],
        input[type="password"],
        input[type="search"],
        input[type="number"],
        input[type="tel"],
        textarea,
        select {
            font-size: 16px !important;
            transform: scale(1) !important;
            -webkit-transform: scale(1) !important;
            -webkit-text-size-adjust: 100% !important;
            text-size-adjust: 100% !important;
            touch-action: manipulation;
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            -moz-user-select: none;
        }
    `, 0);
    
    // Add CSS for desktop specifically
    style.sheet.insertRule(`
        @media (min-width: 992px) {
            input:focus, textarea:focus, select:focus {
                transform: translateZ(0) !important;
                -webkit-transform: translateZ(0) !important;
                transform: scale(1) !important;
                -webkit-transform: scale(1) !important;
            }
        }
    `, 1);
};

// Call the function before anything else
addInputTypesNoZoom();

// Enhanced Search functionality with simplified design
document.addEventListener('DOMContentLoaded', function() {
    // Force fix on searchIcon styling
    setTimeout(() => {
        const searchIcon = document.getElementById('search-icon');
        if (searchIcon) {
            searchIcon.style.color = '#a9adb5';
            // Check if there's text in the search field
            const userSearch = document.getElementById('user-search');
            if (userSearch && userSearch.value.trim() !== '') {
                searchIcon.style.display = 'none';
            } else {
                searchIcon.style.display = 'flex';
            }
        }
    }, 100);
    const userSearch = document.getElementById('user-search');
    const searchClear = document.getElementById('search-clear');
    
    // Setup search clear functionality
    if (searchClear && userSearch) {
        // Show/hide clear button based on input content
        userSearch.addEventListener('input', function() {
            const searchIcon = document.getElementById('search-icon');
            
            if (this.value.trim() !== '') {
                searchClear.style.display = 'flex';
                // Hide search icon when typing
                if (searchIcon) {
                    searchIcon.style.display = 'none';
                }
            } else {
                searchClear.style.display = 'none';
                // Don't show search icon when empty but still has focus
                if (searchIcon) {
                    // Keep search icon hidden if input still has focus
                    if (document.activeElement === userSearch) {
                        searchIcon.style.display = 'none';
                    } else {
                        searchIcon.style.display = 'flex';
                        searchIcon.style.color = '#a9adb5';
                    }
                }
            }
            
            // Add a slight delay to prevent excessive API calls during typing
            clearTimeout(this.searchTimeout);
            
            this.searchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                window.dispatchEvent(new CustomEvent('searchUsers', { detail: { searchTerm } }));
            }, 300);
        });
        
        // Clear search when clear icon is clicked
        searchClear.addEventListener('click', function() {
            userSearch.value = '';
            this.style.display = 'none';
            userSearch.focus();
            
            // Reset search results to show all users
            window.dispatchEvent(new CustomEvent('searchUsers', { detail: { searchTerm: '' } }));
            
            // Keep search icon hidden because input will still have focus
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'none';
            }
        });
    }
    
    // Focus the search field automatically if it's available
    if (userSearch) {
        // Check the initial state of the search input
        if (userSearch.value.trim() !== '') {
            if (searchClear) searchClear.style.display = 'flex';
            
            // Initially hide the search icon if there's text
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'none';
            }
        } else {
            // Make sure search icon is displayed when search is empty
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'flex';
                // Set the color to ensure it's correct
                searchIcon.style.color = '#a9adb5';
            }
        }
        
        // Define preventZoom function in a broader scope
        let preventZoom = function(e) {
            if(e.touches && e.touches.length > 1) {
                e.preventDefault();
            }
        };
        
        // Enhanced animation when typing
        userSearch.addEventListener('focus', function() {
            const wrapper = this.closest('.search-wrapper');
            if (wrapper) {
                wrapper.style.transition = 'all 0.2s ease-in-out';
            }
            // Hide the search icon whenever input is focused, regardless of content
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'none';
            }
            
            // Completely block zooming
            document.body.style.touchAction = 'manipulation';
            document.documentElement.style.touchAction = 'manipulation';
            
            // Add focus specific styling to prevent zoom
            wrapper.style.transform = 'translateZ(0)';
            this.style.transform = 'translateZ(0)';
            
            // Set viewport with very strict settings
            const viewportMeta = document.querySelector('meta[name="viewport"]');
            if (viewportMeta) {
                viewportMeta.setAttribute('data-original-content', viewportMeta.getAttribute('content'));
                viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0');
            }
            
            // Install extremely aggressive zoom prevention
            document.addEventListener('touchmove', preventZoom, { passive: false });
            document.addEventListener('gesturestart', preventZoom, { passive: false });
            document.addEventListener('gesturechange', preventZoom, { passive: false });
            document.addEventListener('gestureend', preventZoom, { passive: false });
            
            // Apply additional browser-specific fixes for desktop mode
            if (window.innerWidth >= 992) { // Desktop mode
                // Create a fullscreen overlay with high z-index that will capture and prevent zoom gestures
                const overlay = document.createElement('div');
                overlay.id = 'zoom-prevention-overlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.zIndex = '9998'; // High z-index but below modal dialogs
                overlay.style.backgroundColor = 'transparent';
                overlay.style.pointerEvents = 'none';
                overlay.style.touchAction = 'none';
                document.body.appendChild(overlay);
                
                // Create a hole in the overlay for the search input
                const searchRect = this.getBoundingClientRect();
                const hole = document.createElement('div');
                hole.id = 'search-field-hole';
                hole.style.position = 'absolute';
                hole.style.top = (searchRect.top - 5) + 'px';
                hole.style.left = (searchRect.left - 5) + 'px';
                hole.style.width = (searchRect.width + 10) + 'px';
                hole.style.height = (searchRect.height + 10) + 'px';
                hole.style.pointerEvents = 'auto';
                overlay.appendChild(hole);
            }
        });
        
        // Show search icon again when input loses focus and is empty
        userSearch.addEventListener('blur', function() {
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon && this.value.trim() === '') {
                searchIcon.style.display = 'flex';
            }
            
            // Restore original viewport settings
            const viewportMeta = document.querySelector('meta[name="viewport"]');
            if (viewportMeta && viewportMeta.hasAttribute('data-original-content')) {
                viewportMeta.setAttribute('content', viewportMeta.getAttribute('data-original-content'));
            }
            
            // Remove the overlay we added
            const overlay = document.getElementById('zoom-prevention-overlay');
            if (overlay) {
                document.body.removeChild(overlay);
            }
            
            // Clean up event listeners
            document.removeEventListener('touchmove', preventZoom, { passive: false });
            document.removeEventListener('gesturestart', preventZoom, { passive: false });
            document.removeEventListener('gesturechange', preventZoom, { passive: false });
            document.removeEventListener('gestureend', preventZoom, { passive: false });
        });
        
        // Fix for clearing text: keep search icon hidden even when text is deleted
        userSearch.addEventListener('input', function() {
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                // Keep search icon hidden as long as input has focus
                searchIcon.style.display = 'none';
            }
        });
    }
});

// Fix click events on mobile
document.addEventListener('touchstart', function() {
    // This empty handler enables :active CSS states on mobile
}, false);

// Handle touch/click outside search bar to close it
document.addEventListener('click', function(event) {
    const userSearch = document.getElementById('user-search');
    const searchWrapper = document.getElementById('search-wrapper');
    
    // Check if click is outside the search bar
    if (userSearch && searchWrapper && !searchWrapper.contains(event.target)) {
        // Remove focus from search input
        userSearch.blur();
        
        // Check if search is empty, then make search icon visible again
        if (userSearch.value.trim() === '') {
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'flex';
            }
        }
    }
});

// Same for touch events on mobile
document.addEventListener('touchstart', function(event) {
    const userSearch = document.getElementById('user-search');
    const searchWrapper = document.getElementById('search-wrapper');
    
    // Check if touch is outside the search bar
    if (userSearch && searchWrapper && !searchWrapper.contains(event.target)) {
        // Remove focus from search input
        userSearch.blur();
        
        // Check if search is empty, then make search icon visible again
        if (userSearch.value.trim() === '') {
            const searchIcon = document.getElementById('search-icon');
            if (searchIcon) {
                searchIcon.style.display = 'flex';
            }
        }
    }
});

// Fix performance issues
document.addEventListener('scroll', function() {
    // Use debounced scroll handler for better performance
    if (!this.scrollTimeout) {
        this.scrollTimeout = setTimeout(() => {
            // Actual scroll handler code here
            this.scrollTimeout = null;
        }, 100);
    }
}, { passive: true });

// Fix back navigation
window.addEventListener('popstate', function(event) {
    // Handle back button navigation
    if (document.getElementById('fullscreen-chat')) {
        // If a chat is open, close it instead of navigating back
        document.getElementById('fullscreen-chat').remove();
        event.preventDefault();
        return false;
    }
});

// Add prefetch for better navigation performance
function prefetchCommonResources() {
    const resources = [
        'css/styles.css',
        'css/home-custom.css', 
        'js/home.js',
        'js/home-fix.js'
    ];
    
    resources.forEach(url => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    });
}

// Add Modern Facebook-like Ripple Effect
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS for the ripple effect
    const rippleEffectStyle = document.createElement('style');
    rippleEffectStyle.textContent = `
        .chat-item {
            position: relative;
            overflow: hidden;
            transform: none !important;
            transition: none !important;
            animation: none !important;
        }
        .chat-item:active, .chat-item:hover {
            transform: none !important;
            transition: none !important;
            animation: none !important;
            background-color: transparent !important;
        }
        .chat-avatar-initial {
            transform: none !important;
            transition: none !important;
        }
        .chat-avatar-initial:active, .chat-avatar-initial:hover {
            transform: none !important;
            transition: none !important;
        }
        
        /* Enhanced ripple effect with stronger style enforcement */
        .ripple {
            position: absolute !important;
            background: rgba(0, 0, 0, 0.2) !important; /* Lighter color as requested */
            border-radius: 50% !important;
            transform: scale(0) !important;
            animation: ripple-animation-fix 0.9s linear !important; /* Slower animation as requested */
            pointer-events: none !important;
            z-index: 9999 !important; /* Super high z-index to ensure visibility */
            opacity: 1 !important;
        }
        
        @keyframes ripple-animation-fix {
            0% {
                transform: scale(0) !important;
                opacity: 0.8 !important;
            }
            100% {
                transform: scale(2.5) !important;
                opacity: 0 !important;
            }
        }
        
        /* Additional overrides to ensure chat items have no decorations */
        .chat-item {
            position: relative !important;
            overflow: hidden !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 14px 16px !important;
            box-shadow: none !important;
            border: none !important;
            background-color: var(--background-color, #ffffff) !important;
            transform: none !important;
            transition: none !important;
        }
        
        /* Completely disable any hover effects for chat items */
        .chat-item:hover {
            transform: none !important;
            transition: none !important;
            animation: none !important;
            background-color: transparent !important;
            border-radius: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
    `;
    document.head.appendChild(rippleEffectStyle);
    
    // Function to create and apply ripple effect
    function createRippleEffect(element, event, isTouch = false) {
        if (!element) return;
        
        // Force element to have relative positioning and overflow hidden
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        
        // Create ripple element with inline styles for maximum compatibility
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        
        // Apply all styles directly to ensure they work
        ripple.style.position = 'absolute';
        ripple.style.backgroundColor = 'rgba(0, 0, 0, 0.2)'; // Lighter color as requested
        ripple.style.borderRadius = '50%';
        ripple.style.pointerEvents = 'none';
        ripple.style.zIndex = '9999';
        ripple.style.opacity = '1';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple-animation-fix 0.9s linear'; // Slower animation as requested
        
        element.appendChild(ripple);
        
        // Get position
        const rect = element.getBoundingClientRect();
        
        // Calculate ripple size (make it much larger)
        const size = Math.max(rect.width, rect.height) * 2;
        
        // Set ripple position and size
        ripple.style.width = size + 'px';
        ripple.style.height = size + 'px';
        
        // Position the ripple where clicked or touched
        let clientX, clientY;
        if (isTouch) {
            const touch = event.touches[0];
            clientX = touch.clientX;
            clientY = touch.clientY;
        } else {
            clientX = event.clientX;
            clientY = event.clientY;
        }
        
        const offsetX = clientX - rect.left - size / 2;
        const offsetY = clientY - rect.top - size / 2;
        
        ripple.style.left = offsetX + 'px';
        ripple.style.top = offsetY + 'px';
        
        // Remove the ripple after animation completes
        setTimeout(() => {
            if (ripple && ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 900);
    }

    // Remove all previous hover/transition effects and add ripple to chat items
    const chatList = document.getElementById('chat-list');
    if (chatList) {
        // Capture phase for various mouse events to remove default effects
        ['mouseenter', 'mouseover'].forEach(function(eventType) {
            chatList.addEventListener(eventType, function(e) {
                const chatItem = e.target.closest('.chat-item');
                if (chatItem) {
                    // Force disable any hover effects
                    chatItem.style.backgroundColor = 'transparent';
                }
            }, true);
        });
        
        // Add ripple effect on mousedown
        chatList.addEventListener('mousedown', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                createRippleEffect(chatItem, e);
            }
        }, false);
        
        // For touch devices
        chatList.addEventListener('touchstart', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                createRippleEffect(chatItem, e, true);
            }
        }, false);
    }
    
    // Add ripple effect to search bar
    const searchWrapper = document.querySelector('.search-wrapper');
    if (searchWrapper) {
        // Add ripple effect on mousedown
        searchWrapper.addEventListener('mousedown', function(e) {
            createRippleEffect(searchWrapper, e);
        }, false);
        
        // For touch devices
        searchWrapper.addEventListener('touchstart', function(e) {
            createRippleEffect(searchWrapper, e, true);
        }, false);
    }
    
    // No ripple effect for navbar menu button as requested
    
    // Apply to all nav-link items
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(navLink => {
        // Add ripple effect on mousedown
        navLink.addEventListener('mousedown', function(e) {
            createRippleEffect(navLink, e);
        }, false);
        
        // For touch devices
        navLink.addEventListener('touchstart', function(e) {
            createRippleEffect(navLink, e, true);
        }, false);
    });
    
    // Apply to profile action buttons
    const profileActionBtns = document.querySelectorAll('.profile-action-btn');
    profileActionBtns.forEach(btn => {
        // Add ripple effect on mousedown
        btn.addEventListener('mousedown', function(e) {
            createRippleEffect(btn, e);
        }, false);
        
        // For touch devices
        btn.addEventListener('touchstart', function(e) {
            createRippleEffect(btn, e, true);
        }, false);
    });
    
    // Apply to user-menu
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        // Add ripple effect on mousedown
        userMenu.addEventListener('mousedown', function(e) {
            createRippleEffect(userMenu, e);
        }, false);
        
        // For touch devices
        userMenu.addEventListener('touchstart', function(e) {
            createRippleEffect(userMenu, e, true);
        }, false);
    }
});

// Execute prefetch on idle
if ('requestIdleCallback' in window) {
    requestIdleCallback(prefetchCommonResources);
} else {
    setTimeout(prefetchCommonResources, 1000);
}