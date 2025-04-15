// Preload pages for faster navigation
function preloadPage(url) {
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    document.head.appendChild(link);
}

// Function to make page transitions faster
function setupFastNavigation() {
    // Preload common pages
    preloadPage('home.php');
    preloadPage('profile.php');
    
    // Add event listeners to navigation links for faster transitions
    document.querySelectorAll('a').forEach(link => {
        if (link.href.includes('home.php') || 
            link.href.includes('profile.php')) {
            
            link.addEventListener('touchstart', function() {
                preloadPage(this.href);
            });
            
            link.addEventListener('mouseenter', function() {
                preloadPage(this.href);
            });
        }
    });
}

// Function to check network connectivity and update online status
function updateNetworkStatus() {
    // Check if user is online
    const isOnline = navigator.onLine;
    
    // Get network information if available
    let networkType = 'unknown';
    if (navigator.connection) {
        networkType = navigator.connection.type || 
                      navigator.connection.effectiveType || 
                      'unknown';
    }
    
    // Update server with current network status
    fetch('/api/users.php?action=status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: isOnline ? 'online' : 'offline',
            network_type: networkType,
            timestamp: Date.now()
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Network status updated:', data);
    })
    .catch(error => {
        console.error('Error updating network status:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const chatList = document.getElementById('chat-list');
    const userSearch = document.getElementById('user-search');

    const menuButton = document.getElementById('navbar-menu-button');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const appLayout = document.getElementById('app-layout');
    
    // Setup network status monitoring
    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    
    // Initial network status check
    updateNetworkStatus();
    
    // Set up regular network status update (every 60 seconds)
    setInterval(updateNetworkStatus, 60000);
    
    // State variables
    let currentUsers = [];
    let selectedUserId = null;
    let fullscreenChatActive = false;
    let currentFullscreenUserId = null;
    let unreadMessages = {};
    let lastMessageTimestamp = {};
    
    // Exact sample message preview texts directly from the image
    const exactSampleMessages = [
        "received?",
        "How are you?",
        "tastes amazing!",
        "I will come here next time",
        "where are you from?",
        "when will it be ready?",
        "Have you spoken to the delivery...",
        "Received"
    ];
    
    // Exact sample time labels directly from the image
    const exactSampleTimes = ["1m", "3m", "3m", "12m", "12m", "1h", "2h", "8h"];
    
    // Exact sample unread counts directly from the image
    const exactSampleUnread = [0, 0, 0, 0, 0, 0, 0, 0];
    
    // Sample user data to match the image exactly
    const sampleUsers = [
        { name: "Nguyen" },
        { name: "Miles" },
        { name: "Flores" },
        { name: "Black" },
        { name: "Henry" },
        { name: "Cooper" },
        { name: "Black" },
        { name: "Flores" }
    ];
    
    // Initialize - load users
    initializeApp();
    
    function initializeApp() {
        showLoadingSpinner();
        
        // Setup fast navigation between pages
        setupFastNavigation();
        
        // Add message listener for updating chat previews
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'newMessage' && event.data.userId && event.data.message) {
                updateChatPreview(event.data.userId, event.data.message);
                playMessageSound('received');
            }
        });
        
        // Setup sidebar toggle
        setupSidebarToggle();
        
        // Load users from API with current timestamp
        fetch(`/api/users.php?timestamp=${Date.now()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.users) {
                    currentUsers = data.data.users;
                    renderChatList();
                    hideLoadingSpinner();
                    setupEventListeners();
                } else {
                    console.error('Error loading users: Invalid response format', data);
                    showErrorState('Unable to load users. Please try again later.');
                    hideLoadingSpinner();
                    
                    // For now, let's use sample data to show the UI
                    currentUsers = [];
                    for (let i = 0; i < 8; i++) {
                        currentUsers.push({
                            id: i + 1,
                            display_name: sampleUsers[i % sampleUsers.length].name
                        });
                    }
                    renderChatList();
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                showErrorState('Unable to load users. Please try again later.');
                hideLoadingSpinner();
                
                // For now, let's use sample data to show the UI
                currentUsers = [];
                for (let i = 0; i < 8; i++) {
                    currentUsers.push({
                        id: i + 1,
                        display_name: sampleUsers[i % sampleUsers.length].name
                    });
                }
                renderChatList();
            });
    }
    
    function setupSidebarToggle() {
        // Mobile sidebar toggle
        if (menuButton) {
            menuButton.addEventListener('click', function() {
                if (appLayout) {
                    appLayout.classList.toggle('sidebar-visible');
                    // Menu button clicked, sidebar toggle
                }
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                if (appLayout) {
                    appLayout.classList.remove('sidebar-visible');
                    // Overlay clicked, sidebar hidden
                }
            });
        }
    }
    
    function setupEventListeners() {
        // Search input event listener
        if (userSearch) {
            userSearch.addEventListener('input', function(e) {
                loadUsers(e.target.value);
            });
            
            // Listen for custom search event from home-fix.js
            window.addEventListener('searchUsers', function(e) {
                if (e.detail && e.detail.searchTerm !== undefined) {
                    loadUsers(e.detail.searchTerm);
                }
            });
            
            // Handle search clear button (X inside search input)
            const searchClearBtn = document.getElementById('search-clear');
            if (searchClearBtn) {
                searchClearBtn.addEventListener('click', function() {
                    userSearch.value = '';
                    userSearch.focus();
                    loadUsers('');
                });
            }
            
            // Handle search close button ("বাদ" button)
            const searchCloseBtn = document.getElementById('search-close-btn');
            if (searchCloseBtn) {
                searchCloseBtn.addEventListener('click', function() {
                    userSearch.value = '';
                    userSearch.blur();
                    loadUsers('');
                });
            }
        }
        
        // User menu in sidebar
        const sidebarUserMenu = document.getElementById('sidebar-user-menu');
        if (sidebarUserMenu) {
            sidebarUserMenu.addEventListener('click', function() {
                showToast('User menu options coming soon!');
            });
        }
        
        // Add event delegation for chat-item ripple effect
        if (chatList) {
            chatList.addEventListener('click', function(e) {
                // Find the closest chat-item parent
                const chatItem = e.target.closest('.chat-item');
                if (!chatItem) return;
                
                // Remove any existing ripple animations
                const existingRipples = document.querySelectorAll('.chat-ripple');
                existingRipples.forEach(ripple => ripple.remove());
                
                // Create a ripple element
                const ripple = document.createElement('span');
                ripple.className = 'chat-ripple';
                
                // Position the ripple at the click point
                const rect = chatItem.getBoundingClientRect();
                ripple.style.left = (e.clientX - rect.left) + 'px';
                ripple.style.top = (e.clientY - rect.top) + 'px';
                
                // Add the ripple to the chat-item
                chatItem.appendChild(ripple);
                
                // Remove ripple after animation completes
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
    }
    
    function showLoadingSpinner() {
        if (chatList) {
            chatList.innerHTML = '<div class="loading-spinner"></div>';
        }
    }
    
    function hideLoadingSpinner() {
        if (chatList) {
            const loadingSpinners = chatList.querySelectorAll('.loading-spinner');
            loadingSpinners.forEach(spinner => spinner.remove());
        }
    }
    
    function showErrorState(message) {
        const errorEl = document.createElement('div');
        errorEl.className = 'empty-state';
        errorEl.innerHTML = `
            <div class="empty-state-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="empty-state-text">Oops!</div>
            <div class="empty-state-subtext">${message}</div>
        `;
        if (chatList) {
            chatList.innerHTML = '';
            chatList.appendChild(errorEl);
        }
    }
    
    function showToast(message) {
        // Create a toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
        
        // Create the toast
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div class="toast-message">${message}</div>
            <button class="toast-close"><i class="fas fa-times"></i></button>
        `;
        toastContainer.appendChild(toast);
        
        // Add the show class to make it visible
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        // Setup close button
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        });
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
    
    // Make loadUsers available globally so it can be used from home.php
    window.loadUsers = function(searchQuery = '') {
        // We removed all debugging logs here for production
        
        if (searchQuery) {
            // First attempt client-side filtering with display_name having higher priority
            const filteredUsers = currentUsers.filter(user => {
                // First check display_name (higher priority)
                if (user.display_name && user.display_name.toLowerCase().includes(searchQuery.toLowerCase())) {
                    return true;
                }
                // Then check username (lower priority)
                if (user.username && user.username.toLowerCase().includes(searchQuery.toLowerCase())) {
                    return true;
                }
                return false;
            });
            
            // Sort results to prioritize display_name matches over username matches
            filteredUsers.sort((a, b) => {
                const aDisplayMatch = a.display_name && a.display_name.toLowerCase().includes(searchQuery.toLowerCase());
                const bDisplayMatch = b.display_name && b.display_name.toLowerCase().includes(searchQuery.toLowerCase());
                
                if (aDisplayMatch && !bDisplayMatch) return -1; // a comes first
                if (!aDisplayMatch && bDisplayMatch) return 1;  // b comes first
                return 0; // keep original order
            });
            renderChatList(filteredUsers);
            
            // If we have API-based search, also use that
            if (searchQuery.length >= 2) { // Only search with 2+ characters
                fetch(`/api/users.php?search=${encodeURIComponent(searchQuery)}&timestamp=${Date.now()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && data.data.users) {
                            // Update with fresh API results
                            // Only do this if the search term is still the same (user hasn't changed it)
                            const currentSearchTerm = document.getElementById('search-input')?.value || 
                                                     document.getElementById('user-search')?.value || '';
                            if (currentSearchTerm.toLowerCase() === searchQuery.toLowerCase()) {
                                renderChatList(data.data.users);
                            }
                        }
                    })
                    .catch(error => {
                        // Handle search errors silently in production
                        // Show fallback client-side results only
                        console.error('Error searching users:', error);
                    });
            }
        } else {
            // If search is empty, reload all users from API
            // Add current timestamp to check which users are online
            fetch(`/api/users.php?timestamp=${Date.now()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.users) {
                        currentUsers = data.data.users;
                        renderChatList();
                    }
                })
                .catch(error => {
                    // Handle reloading errors silently in production
                    // Do not show error messages to the user
                    console.error('Error reloading users:', error);
                    renderChatList(); // Fallback to current users if API fails
                });
        }
    }
    
    // Keep local reference for internal use
    function loadUsers(searchQuery = '') {
        // Only call the global loadUsers function
        window.loadUsers(searchQuery);
    }
    
    function renderChatList(users = currentUsers) {
        // Clear the chat list
        if (!chatList) return;
        chatList.innerHTML = '';
        
        // Check if there are no users
        if (!users || users.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'empty-state';
            emptyState.innerHTML = `
                <div class="empty-state-icon"><i class="fas fa-users"></i></div>
                <div class="empty-state-text">No Users Found</div>
                <div class="empty-state-subtext">Try a different search term or check back later.</div>
            `;
            chatList.appendChild(emptyState);
            return;
        }
        
        // Loop through users and create chat items
        users.forEach((user, index) => {
            if (index >= 8) return;

            const sampleIndex = index % sampleUsers.length;
            let displayName = sampleUsers[sampleIndex].name;
            
            // Use user's display_name if available
            if (user.display_name) {
                displayName = user.display_name.split(" ")[0]; // Use first part of name only
            }
            
            // Get message preview, time, and unread count for this user
            let messagePreview = "No messages yet";
            let timeLabel = ""; 
            let unreadCount = 0;
            
            // Get unread count from user data (comes from API)
            if (user.unread_count) {
                unreadCount = user.unread_count;
            }
            
            // Set unread count from unreadMessages object if available (for new messages)
            if (unreadMessages[user.id]) {
                unreadCount += unreadMessages[user.id];
            }
            
            // Use actual last message if available from API
            if (user.last_message) {
                messagePreview = user.last_message.text;
                
                // Format the timestamp
                const messageDate = new Date(user.last_message.timestamp * 1000);
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                
                if (messageDate >= today) {
                    // Today - show time only with AM/PM
                    const hours = messageDate.getHours();
                    const minutes = messageDate.getMinutes().toString().padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    const displayHours = hours % 12 || 12;
                    timeLabel = `${displayHours}:${minutes} ${ampm}`;
                } else if (messageDate >= yesterday) {
                    // Yesterday
                    timeLabel = "Yesterday";
                } else {
                    // Older - show date
                    timeLabel = `${messageDate.getDate()}/${messageDate.getMonth() + 1}`;
                }
            }
            
            // Create avatar color from the image
            const colors = ['#E5AA9D', '#97D5E0', '#F498C2', '#A0D1B0', '#8A9FF1', '#F1C38A', '#B298F1', '#BB9F8A'];
            let avatarColor = colors[sampleIndex];
            
            // Use user's avatar_color if available
            if (user.avatar_color) {
                avatarColor = user.avatar_color;
            }
            
            const chatItem = document.createElement('div');
            chatItem.className = unreadCount > 0 ? 'chat-item unread' : 'chat-item';
            chatItem.dataset.userId = user.id;
            
            // Use the first letter of the display name for avatar
            const initialChar = displayName.charAt(0).toUpperCase();
            
            // Check if user is online
            const isOnline = user.online === true;
            
            chatItem.innerHTML = `
                <a href="profile.php?id=${user.id}" class="chat-avatar" style="text-decoration: none; display: block;" onclick="event.stopPropagation();">
                    <div class="chat-avatar-initial" style="background-color: ${avatarColor}">
                        ${initialChar}
                    </div>
                    <div class="status-indicator ${isOnline ? 'online' : ''}"></div>
                </a>
                <div class="chat-info">
                    <div class="chat-name" style="${unreadCount > 0 ? 'font-weight:700 !important; color:#000000 !important;' : ''}">${displayName}</div>
                    <div class="chat-preview" style="${unreadCount > 0 ? 'font-weight:600 !important; color:#000000 !important;' : ''}">${messagePreview}</div>
                </div>
                <div class="chat-meta">
                    <div class="chat-time">${timeLabel}</div>
                    ${unreadCount > 0 ? `<div class="chat-unread">${unreadCount}</div>` : ''}
                </div>
            `;
            
            // Add click event listener to open chat
            chatItem.addEventListener('click', (e) => {
                // Prevent any default animations or styles
                chatItem.style.transform = 'none';
                chatItem.style.transition = 'none';
                
                // Mark as read if it was unread
                if (chatItem.classList.contains('unread')) {
                    chatItem.classList.remove('unread');
                    
                    // Remove inline styles
                    const chatName = chatItem.querySelector('.chat-name');
                    const chatPreview = chatItem.querySelector('.chat-preview');
                    
                    if (chatName) chatName.removeAttribute('style');
                    if (chatPreview) chatPreview.removeAttribute('style');
                    
                    // Reset unread count for this user
                    if (unreadMessages[user.id]) {
                        unreadMessages[user.id] = 0;
                    }
                    
                    // Also reset server-side unread messages
                    fetch('/api/messages.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'mark_read',
                            user_id: user.id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error marking messages as read:', data.message);
                        }
                        
                        // Update the user object
                        if (user.unread_count) {
                            user.unread_count = 0;
                        }
                    })
                    .catch(error => {
                        console.error('Error marking messages as read:', error);
                    });
                }
                
                // Open the fullscreen chat
                openFullscreenChat(user);
            });
            
            chatList.appendChild(chatItem);
        });
    }
});
    
