/**
 * Fullscreen chat functionality for the messenger app
 * Handles opening, closing, sending and rendering messages in fullscreen chat mode
 */

// Global variables for fullscreen chat
let fullscreenChatActive = false;
let currentFullscreenUserId = null;
let unreadMessages = {};
let currentUserId = null; // Will store the current logged-in user ID
let longPressTimer = null;
let longPressTimeout = 500; // Time in milliseconds to trigger long press
let longPressTouchMoved = false; // Flag to detect if touch moved during press
let activeMessageElement = null; // Tracks which message is being long-pressed
let messageOptionsVisible = false; // Track if options menu is currently visible
let touchStartPosition = { x: 0, y: 0 }; // Track initial touch position
let messageDrafts = {}; // Object to store message drafts by user ID
let draftSaveTimeout = null; // Timeout for debouncing draft saves
const DRAFT_SAVE_DELAY = 500; // Milliseconds to wait before saving draft after typing

// Get the current user ID when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get current user ID from the meta tag (added by PHP)
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (userIdMeta) {
        currentUserId = userIdMeta.getAttribute('content');
        
        // Load saved drafts from localStorage
        loadAllDraftsFromStorage();
    }
});

/**
 * Save all message drafts to localStorage
 */
function saveAllDraftsToStorage() {
    if (currentUserId) {
        const storageKey = `chat_drafts_${currentUserId}`;
        localStorage.setItem(storageKey, JSON.stringify(messageDrafts));
    }
}

/**
 * Load all message drafts from localStorage
 */
function loadAllDraftsFromStorage() {
    if (currentUserId) {
        const storageKey = `chat_drafts_${currentUserId}`;
        const savedDrafts = localStorage.getItem(storageKey);
        
        if (savedDrafts) {
            try {
                messageDrafts = JSON.parse(savedDrafts);
                console.log('Loaded message drafts:', messageDrafts);
            } catch (e) {
                console.error('Error parsing saved drafts:', e);
                messageDrafts = {};
            }
        }
    }
}

/**
 * Save a draft message for a specific user
 * @param {string|number} userId - The ID of the user to save draft for
 * @param {string} message - The draft message text
 */
function saveMessageDraft(userId, message) {
    // Only save if there's content
    if (message && message.trim()) {
        messageDrafts[userId] = message;
    } else {
        // If empty, remove any existing draft
        delete messageDrafts[userId];
    }
    
    // Save to localStorage
    saveAllDraftsToStorage();
}

/**
 * Get a draft message for a specific user
 * @param {string|number} userId - The ID of the user
 * @returns {string} The draft message or empty string if none exists
 */
function getMessageDraft(userId) {
    return messageDrafts[userId] || '';
}

/**
 * Opens the fullscreen chat for a specific user
 * @param {Object} user - The user to chat with
 */
function openFullscreenChat(user) {
    fullscreenChatActive = true;
    currentFullscreenUserId = user.id;
    
    // Immediately fetch current user data to make sure we have the latest avatar color
    fetch('/api/users.php?current=true')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user && data.user.avatar_color) {
                // Update meta tag with latest color
                const currentUserColorMeta = document.querySelector('meta[name="user-avatar-color"]');
                if (currentUserColorMeta) {
                    currentUserColorMeta.setAttribute('content', data.user.avatar_color);
                }
                
                // Also update any existing sent messages with the new color
                const sentMessages = document.querySelectorAll('.message-sent');
                if (sentMessages.length > 0) {
                    sentMessages.forEach(message => {
                        message.style.backgroundColor = data.user.avatar_color;
                        message.style.color = '#FFFFFF'; // Set text color to white for better contrast
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error fetching current user data:', error);
        });
    
    // Create fullscreen chat container
    const fullscreenChat = document.createElement('div');
    fullscreenChat.className = 'chat-fullscreen';
    fullscreenChat.id = 'fullscreen-chat';
    fullscreenChat.setAttribute('data-user-id', user.id); // Add user ID as data attribute for tracking
    fullscreenChat.setAttribute('data-avatar-color', user.avatar_color || "#8A9FF1"); // Store avatar color for message bubbles
    
    // Create an inner container for positioning reaction selector and options in desktop mode
    const innerContainer = document.createElement('div');
    innerContainer.id = 'fullscreen-chat-container';
    innerContainer.className = 'fullscreen-chat-container';
    
    // Listen for viewport resize due to keyboard appearance
    setupViewportAdjustment();
    
    const displayName = user.display_name || "User";
    const avatarColor = user.avatar_color || "#8A9FF1";
    const initialChar = displayName.charAt(0).toUpperCase();
    
    // Create the header, messages container, and input area directly in the inner container
    innerContainer.innerHTML = `
        <div class="fullscreen-header">
            <div class="fullscreen-back" id="fullscreen-back-button" style="margin-left: 7px;">
                <div class="back-content">
                    <i class="fas fa-arrow-left" style="margin-right: 0; position: relative; top: 1px; font-size: 18px;"></i>
                </div>
                <!-- Add an extra hidden transparent overlay to increase click area -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: transparent; z-index: 1002;"></div>
            </div>
            <div class="fullscreen-title">${displayName}</div>
            <div class="fullscreen-actions">
                <i class="action-icon fas fa-phone"></i>
                <i class="action-icon fas fa-video"></i>
            </div>
        </div>
        <div class="fullscreen-messages" id="fullscreen-messages"></div>
        <div class="input-area">
            <div class="message-input-container">
                <textarea id="message-input" class="message-input" placeholder="Type a message..." rows="1"></textarea>
                <div class="input-tools">
                    <i class="input-tool-icon far fa-smile"></i>
                    <i class="input-tool-icon fas fa-paperclip"></i>
                </div>
            </div>
            <div class="send-button" id="send-button">
                <i class="send-icon fas fa-arrow-right"></i>
            </div>
        </div>
    `;
    
    // Add the inner container to the fullscreen chat
    fullscreenChat.appendChild(innerContainer);
    
    // Add the fullscreen chat to the document body
    document.body.appendChild(fullscreenChat);
    
    // Mark as read in local storage
    unreadMessages[user.id] = 0;
    
    // Mark as read on server
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
        
        // Update the user object if it has unread_count
        if (user.unread_count) {
            user.unread_count = 0;
        }
    })
    .catch(error => {
        console.error('Error marking messages as read:', error);
    });
    
    // Add event listeners
    
    // Add back button event listener with improved handling
    const backButton = fullscreenChat.querySelector('#fullscreen-back-button');
    backButton.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any default behavior
        closeFullscreenChat();
    });
    
    // Adding the same listener to back-content for additional coverage
    const backContent = fullscreenChat.querySelector('.back-content');
    if (backContent) {
        backContent.addEventListener('click', function(e) {
            e.preventDefault();
            closeFullscreenChat();
        });
    }
    
    const messageInput = fullscreenChat.querySelector('#message-input');
    
    // Load any saved draft for this user
    const savedDraft = getMessageDraft(user.id);
    if (savedDraft) {
        messageInput.value = savedDraft;
    }
    
    // Add ripple effect to message input container
    const messageInputContainer = fullscreenChat.querySelector('.message-input-container');
    if (messageInputContainer) {
        // Add position relative to message input container to support ripple
        messageInputContainer.style.position = 'relative';
        messageInputContainer.style.overflow = 'hidden';
        
        // Add click handler for ripple
        messageInputContainer.addEventListener('mousedown', function(e) {
            addRippleToInput(messageInputContainer, e);
        });
        
        // Add touch handler for mobile
        messageInputContainer.addEventListener('touchstart', function(e) {
            addRippleToInput(messageInputContainer, e, true);
        });
    }
    
    // Auto-resize the textarea as user types without showing scrollbar
    // Also save draft while typing (with debounce)
    messageInput.addEventListener('input', function() {
        // Reset height to auto to get the correct scrollHeight
        this.style.height = 'auto';
        // Hide scrollbar
        this.style.overflow = 'hidden';
        // Set the new height based on scrollHeight
        this.style.height = (this.scrollHeight) + 'px';
        
        // Save draft with debounce
        if (draftSaveTimeout) {
            clearTimeout(draftSaveTimeout);
        }
        
        draftSaveTimeout = setTimeout(() => {
            saveMessageDraft(user.id, this.value);
            console.log(`Saved draft for user ${user.id}:`, this.value.substring(0, 20) + (this.value.length > 20 ? '...' : ''));
        }, DRAFT_SAVE_DELAY);
    });
    
    // Set initial height for any pre-populated content
    setTimeout(function() {
        messageInput.style.height = 'auto';
        messageInput.style.overflow = 'hidden';
        messageInput.style.height = (messageInput.scrollHeight) + 'px';
    }, 0);
    
    // Handle Enter key - now Enter creates new line instead of sending message
    messageInput.addEventListener('keypress', function(e) {
        // Enter key without Shift key no longer sends message, allow default newline behavior
        if (e.key === 'Enter' && !e.shiftKey) {
            // Don't prevent default (let it create a newline)
            // Update the textarea height after adding the newline
            setTimeout(() => {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            }, 0);
        }
    });
    
    // Add event listeners for input tool icons
    const emojiButton = fullscreenChat.querySelector('.input-tool-icon.fa-smile');
    if (emojiButton) {
        emojiButton.addEventListener('click', function() {
            // Future emoji picker functionality
            console.log('Emoji picker clicked');
        });
    }
    
    const attachmentButton = fullscreenChat.querySelector('.input-tool-icon.fa-paperclip');
    if (attachmentButton) {
        attachmentButton.addEventListener('click', function() {
            // Future attachment functionality
            console.log('Attachment button clicked');
        });
    }
    
    const microphoneButton = fullscreenChat.querySelector('.input-tool-icon.fa-microphone');
    if (microphoneButton) {
        microphoneButton.addEventListener('click', function() {
            // Future voice message functionality
            console.log('Microphone button clicked');
        });
    }
    
    const sendButton = fullscreenChat.querySelector('#send-button');
    
    // Add ripple effect to send button
    if (sendButton) {
        // Make sure send button has position relative
        sendButton.style.position = 'relative';
        sendButton.style.overflow = 'hidden';
        
        // Add click handler for ripple with custom color
        sendButton.addEventListener('mousedown', function(e) {
            addRippleToSendButton(sendButton, e);
        });
        
        // Add touch handler for mobile
        sendButton.addEventListener('touchstart', function(e) {
            addRippleToSendButton(sendButton, e, true);
        });
    }
    
    sendButton.addEventListener('click', function() {
        if (messageInput.value.trim()) {
            sendFullscreenMessage(user.id, messageInput.value.trim());
            messageInput.value = '';
            // Clear any saved draft for this user
            saveMessageDraft(user.id, '');
            // Reset height after clearing
            messageInput.style.height = 'auto';
        }
    });
    
    // Load messages
    loadFullscreenMessages(user.id);
    
    // Removed automatic focus to prevent keyboard from opening automatically
}

/**
 * Closes the fullscreen chat
 */
function closeFullscreenChat() {
    const fullscreenChat = document.getElementById('fullscreen-chat');
    if (fullscreenChat) {
        // Save any draft message before closing
        if (currentFullscreenUserId) {
            const messageInput = fullscreenChat.querySelector('#message-input');
            if (messageInput && messageInput.value.trim()) {
                // Save the draft
                saveMessageDraft(currentFullscreenUserId, messageInput.value);
                console.log(`Saved draft for user ${currentFullscreenUserId} on close:`, 
                    messageInput.value.substring(0, 20) + (messageInput.value.length > 20 ? '...' : ''));
            }
        }
        
        // Clear any pending draft save timeout
        if (draftSaveTimeout) {
            clearTimeout(draftSaveTimeout);
            draftSaveTimeout = null;
        }
        
        // Remove the chat from DOM
        fullscreenChat.remove();
        fullscreenChatActive = false;
        currentFullscreenUserId = null;
    }
}

/**
 * Loads messages for the fullscreen chat
 * @param {string|number} userId - The ID of the user to load messages for
 */
function loadFullscreenMessages(userId) {
    const messagesContainer = document.getElementById('fullscreen-messages');
    
    // Load users data to get display names for deletion messages
    let usersData = {};
    let avatarColors = {}; // Add map for avatar colors
    
    // First fetch users with async/await to ensure it completes before messages are loaded
    fetch('/api/users.php')
        .then(response => response.json())
        .then(userData => {
            console.log("Users API response:", userData);
            // Check the actual structure of the response and extract users
            if (userData.success && userData.data && userData.data.users) {
                // Create a map of user IDs to display names - using the correct path to users array
                userData.data.users.forEach(user => {
                    console.log("Adding user to map:", user.id, user.display_name || user.username);
                    usersData[user.id] = user.display_name || user.username;
                    avatarColors[user.id] = user.avatar_color || "#8A9FF1"; // Store avatar colors
                });
            }
            console.log("Complete users data map:", usersData);
            console.log("Complete avatar colors map:", avatarColors);
            
            // Then fetch messages after users data is properly loaded
            return fetch(`/api/messages.php?user_id=${userId}`);
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages) {
                const messages = data.messages.map(msg => {
                    const isSender = msg.sender_id == currentUserId;
                    const isDeletedForUser = (isSender && msg.deleted_for_sender) || 
                                            (!isSender && msg.deleted_for_recipient);
                    
                    // Skip this message if it's deleted just for the current user (not for everyone)
                    if (isDeletedForUser && !msg.deleted_for_everyone) return null;
                    
                    // Handle messages deleted for everyone
                    if (msg.deleted_for_everyone) {
                        // Get the username/display name of the person who deleted the message
                        // Always use the actual name instead of "Someone"
                        const deletedByUsername = usersData[msg.deleted_by_user_id] || "Someone";
                        
                        console.log('Deleted by user ID:', msg.deleted_by_user_id);
                        console.log('Users data:', usersData);
                        console.log('Deleted by username:', deletedByUsername);
                        
                        // Set 'you' text if the current user is the one who deleted the message
                        // Not based on the sender of the message
                        const deletedByText = msg.deleted_by_user_id == currentUserId ? 'you' : deletedByUsername;
                        
                        return {
                            id: msg.id,
                            sender: isSender ? 'me' : 'other',
                            sender_id: msg.sender_id,
                            recipient_id: msg.recipient_id,
                            isDeleted: true,
                            deletedBy: deletedByText,
                            time: new Date(msg.deletion_timestamp * 1000 || msg.timestamp * 1000)
                        };
                    }
                    
                    // Regular message
                    return {
                        id: msg.id,
                        sender: isSender ? 'me' : 'other',
                        sender_id: msg.sender_id,
                        recipient_id: msg.recipient_id,
                        text: msg.message,
                        time: new Date(msg.timestamp * 1000),
                        isDeleted: false,
                        read: msg.read === true, // Explicitly set read status from API
                        reactions: msg.reactions || [] // Include reactions data
                    };
                }).filter(msg => msg !== null); // Filter out null messages
                
                renderFullscreenMessages(userId, messages);
            } else {
                // If no messages or error, display empty chat
                renderFullscreenMessages(userId, []);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            // If error, display empty chat
            renderFullscreenMessages(userId, []);
        });
}

/**
 * Renders messages in the fullscreen chat
 * @param {string|number} userId - The ID of the user
 * @param {Array} messages - The messages to render
 */
function renderFullscreenMessages(userId, messages) {
    console.log('Rendering fullscreen messages');
    const messagesContainer = document.getElementById('fullscreen-messages');
    
    if (!messagesContainer) return;
    
    messagesContainer.innerHTML = '';
    
    let currentDate = null;
    let previousSender = null;
    let messageGroup = null;
    
    // Get current user's avatar color from meta tag with a dynamic fetch to ensure latest color
    let currentUserColor = "#0084FF"; // Default blue color
    
    // First check meta tag (for page initial load)
    const currentUserColorMeta = document.querySelector('meta[name="user-avatar-color"]');
    if (currentUserColorMeta) {
        currentUserColor = currentUserColorMeta.getAttribute('content');
    }
    
    // Then fetch latest user data to ensure we have the most up-to-date color
    fetch('/api/users.php?current=true')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user && data.user.avatar_color) {
                currentUserColor = data.user.avatar_color;
                
                // Also update the meta tag with the latest color
                if (currentUserColorMeta) {
                    currentUserColorMeta.setAttribute('content', currentUserColor);
                }
                
                // Update all sent message bubbles with the new color
                const sentMessages = document.querySelectorAll('.message-sent');
                sentMessages.forEach(message => {
                    message.style.backgroundColor = currentUserColor;
                    message.style.color = '#FFFFFF'; // Set text color to white for better contrast
                });
            }
        })
        .catch(error => {
            console.error('Error fetching current user data:', error);
        });
    
    // Get other user's avatar color
    let otherUserColor = "#F1F0F0"; // Default light gray
    const fullscreenChat = document.getElementById('fullscreen-chat');
    if (fullscreenChat && fullscreenChat.getAttribute('data-avatar-color')) {
        otherUserColor = fullscreenChat.getAttribute('data-avatar-color');
    }
    
    messages.forEach((message, index) => {
        const messageDate = new Date(message.time);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        // Check if the message date is today
        let formattedDate;
        if (messageDate.toDateString() === today.toDateString()) {
            formattedDate = 'Today';
        }
        // Check if the message date is yesterday
        else if (messageDate.toDateString() === yesterday.toDateString()) {
            formattedDate = 'Yesterday';
        }
        // Otherwise, use the full date format
        else {
            // Get day name for specific locales
            const weekday = messageDate.toLocaleDateString('en-US', { weekday: 'long' });
            const month = messageDate.toLocaleDateString('en-US', { month: 'long' });
            const day = messageDate.getDate();
            formattedDate = `${weekday}, ${month} ${day}`;
            
            // If the year is different from current year, add the year
            if (messageDate.getFullYear() !== today.getFullYear()) {
                formattedDate += `, ${messageDate.getFullYear()}`;
            }
        }
        
        // Add date header if this is a new date
        if (formattedDate !== currentDate) {
            currentDate = formattedDate;
            previousSender = null; // Reset previous sender on date change
            
            const dateHeader = document.createElement('div');
            dateHeader.className = 'date-header';
            dateHeader.innerHTML = `<span class="date-header-text">${formattedDate}</span>`;
            messagesContainer.appendChild(dateHeader);
        }
        
        // Check if we need to start a new message group
        if (message.sender !== previousSender) {
            previousSender = message.sender;
            
            // Create a new message row for this message
            const messageRow = document.createElement('div');
            
            // Important: Set alignment based on sender (me = sent = right aligned)
            if (message.sender === 'me') {
                messageRow.className = 'message-row message-row-sent';
                messageRow.style.justifyContent = 'flex-end';
                messageRow.style.alignItems = 'flex-end';
                messageRow.style.width = '100%';
            } else {
                messageRow.className = 'message-row message-row-received';
                messageRow.style.justifyContent = 'flex-start';
                messageRow.style.alignItems = 'flex-start';
                messageRow.style.width = '100%';
            }
            
            messagesContainer.appendChild(messageRow);
            messageGroup = messageRow;
        }
        
        // Create the message element
        const messageEl = document.createElement('div');
        
        // Store message ID as data attribute
        messageEl.setAttribute('data-message-id', message.id);
        messageEl.setAttribute('data-sender', message.sender);
        
        // Apply styles directly to ensure proper positioning
        if (message.sender === 'me') {
            messageEl.className = 'message message-sent';
            messageEl.style.float = 'right'; // Float to right edge
            messageEl.style.clear = 'both'; // Clear any floats
            messageEl.style.margin = '0'; // Reset margin
            messageEl.style.alignSelf = 'flex-end'; // Align to right
            messageEl.style.backgroundColor = currentUserColor; // Set my bubble color to profile color
            
            // Set text color based on the bubble color's brightness
            if (isLightColor(currentUserColor)) {
                messageEl.classList.add('light-bubble');
                messageEl.style.color = '#000000'; // Set text color to black for light backgrounds
            } else {
                messageEl.classList.add('dark-bubble');
                messageEl.style.color = '#FFFFFF'; // Set text color to white for dark backgrounds
            }
            
            messageEl.style.border = 'none'; // Remove border
            messageEl.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.1)'; // Add subtle shadow
        } else {
            messageEl.className = 'message message-received';
            messageEl.style.float = 'left'; // Float to left edge
            messageEl.style.clear = 'both'; // Clear any floats
            messageEl.style.margin = '0'; // Reset margin
            messageEl.style.alignSelf = 'flex-start'; // Align to left
            messageEl.style.backgroundColor = otherUserColor; // Set other user's bubble color to their profile color
            
            // Set text color based on the bubble color's brightness
            if (isLightColor(otherUserColor)) {
                messageEl.classList.add('light-bubble');
                messageEl.style.color = '#000000'; // Set text color to black for light backgrounds
            } else {
                messageEl.classList.add('dark-bubble');
                messageEl.style.color = '#FFFFFF'; // Set text color to white for dark backgrounds
            }
            
            messageEl.style.border = 'none'; // Remove border
            messageEl.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.1)'; // Add subtle shadow
        }
        
        // Add additional classes for consecutive messages
        if (index > 0 && messages[index - 1].sender === message.sender) {
            messageEl.classList.add('consecutive-message');
        }
        
        // Create message wrapper
        const messageWrapper = document.createElement('div');
        messageWrapper.className = 'message-wrapper';
        
        // Apply proper alignment to wrapper as well
        if (message.sender === 'me') {
            messageWrapper.style.alignItems = 'flex-end'; // Align content to right
        } else {
            messageWrapper.style.alignItems = 'flex-start'; // Align content to left
            
            // Only add avatar if this is not a consecutive message or if this is the first message in a group
            if (!messageEl.classList.contains('consecutive-message') || index === 0 || messages[index - 1].sender !== message.sender) {
                // Create the avatar element
                const avatarEl = document.createElement('div');
                avatarEl.className = 'message-avatar';
                
                // Style the avatar
                avatarEl.style.width = '28px';
                avatarEl.style.height = '28px';
                avatarEl.style.borderRadius = '50%';
                avatarEl.style.backgroundColor = otherUserColor;
                avatarEl.style.color = '#FFFFFF';
                avatarEl.style.display = 'flex';
                avatarEl.style.alignItems = 'center';
                avatarEl.style.justifyContent = 'center';
                avatarEl.style.marginRight = '8px';
                avatarEl.style.fontWeight = 'bold';
                avatarEl.style.fontSize = '12px';
                avatarEl.style.flexShrink = '0';
                
                // Get the first letter of the display name for the avatar
                const displayName = document.querySelector('.fullscreen-title').textContent;
                const initialChar = displayName.charAt(0).toUpperCase();
                avatarEl.textContent = initialChar;
                
                // Add the avatar to the wrapper before the message
                messageWrapper.appendChild(avatarEl);
                
                // Add class to wrapper to indicate it has an avatar
                messageWrapper.classList.add('has-avatar');
            } else {
                // Add space equal to avatar width for consecutive messages for proper alignment
                const spacerEl = document.createElement('div');
                spacerEl.className = 'avatar-spacer';
                spacerEl.style.width = '28px';
                spacerEl.style.marginRight = '8px';
                spacerEl.style.flexShrink = '0';
                messageWrapper.appendChild(spacerEl);
                
                // Add padding-left to align with previous messages
                messageWrapper.classList.add('consecutive-wrapper');
            }
        }
        
        // Handle deleted message vs regular message differently
        if (message.isDeleted) {
            // This is a deleted message
            messageEl.classList.add('deleted-message');
            
            // Style for deleted message - smaller size and more compact
            messageEl.style.fontStyle = 'italic';
            messageEl.style.opacity = '0.7';
            messageEl.style.color = '#666';
            messageEl.style.fontSize = '12px'; // Smaller font size
            messageEl.style.padding = '4px 8px'; // Smaller padding for more compact bubble
            messageEl.style.whiteSpace = 'nowrap'; // Prevent line breaks
            messageEl.style.overflow = 'hidden'; // Prevent text from overflowing
            messageEl.style.textOverflow = 'ellipsis'; // Add ellipsis for overflowing text
            
            // Set text for deleted message
            if (message.deletedBy === 'you') {
                messageEl.textContent = 'You deleted a message';
            } else {
                messageEl.textContent = `${message.deletedBy} deleted a message`;
            }
            
            // Set smaller width for deletion messages
            messageEl.style.width = 'auto';
            messageEl.style.minWidth = '40px'; // Reduced from 60px
            messageEl.style.maxWidth = '200px'; // Fixed max width to prevent overflow
            messageEl.style.width = 'fit-content';
        } else {
            // Regular message
            // Convert URLs to clickable links before setting the text - pass the sender type
            const messageWithLinks = convertUrlsToLinks(message.text, message.sender);
            
            // Use innerHTML to render the HTML with links
            messageEl.innerHTML = messageWithLinks;
            
            // If the message is short (less than 30 characters), apply special bubble styling
            if (message.text.length < 30) {
                // Set width based on content but not exceeding bubble width
                messageEl.style.width = 'auto';
                messageEl.style.whiteSpace = 'nowrap';
                messageEl.style.wordBreak = 'normal';
                
                // Set reasonable min and max width for the bubble
                messageEl.style.minWidth = '60px';
                messageEl.style.maxWidth = '90%';
                messageEl.style.width = 'fit-content';
            }
        }
        
        // Add long press event listeners for message deletion
        addLongPressListeners(messageEl);
        
        // Add message element to wrapper first
        messageWrapper.appendChild(messageEl);
        
        // Always add time to every message (instead of only the last message in a sequence)
        // Create time element with read receipts
        const timeEl = document.createElement('div');
        timeEl.className = 'message-time-container';
        
        // Create time text
        const timeTextEl = document.createElement('span');
        timeTextEl.className = 'message-time';
        timeTextEl.textContent = formatTime(message.time);
        timeEl.appendChild(timeTextEl);
        
        // Add read receipts for sent messages
        if (message.sender === 'me') {
            // Create read receipt element
            const readReceiptEl = document.createElement('span');
            readReceiptEl.className = 'message-read-receipt';
            
            // Add read status icon (double check for read, single check for sent)
            console.log('Message read status:', message.id, message.read, message);
            if (message.read === true) {
                // Professional SVG double check icon for read messages (login button color)
                readReceiptEl.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 16 12" fill="none" style="display: inline-block; vertical-align: middle;">
                    <path d="M3.5 6.5L5.5 8.5L8.5 3.5" stroke="#404547" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.5 6.5L9.5 8.5L12.5 3.5" stroke="#404547" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
                readReceiptEl.title = 'Read';
                console.log('Setting double check for message:', message.id);
            } else {
                // Professional SVG single check for sent but unread messages
                readReceiptEl.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 12 12" fill="none" style="display: inline-block; vertical-align: middle;">
                    <path d="M3.5 6.5L5.5 8.5L8.5 3.5" stroke="#8E8E93" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
                readReceiptEl.title = 'Sent';
                console.log('Setting single check for message:', message.id);
            }
            
            // Add margin between time and receipt
            readReceiptEl.style.marginLeft = '4px';
            timeEl.appendChild(readReceiptEl);
        }
        
        // Add time below the message
        messageWrapper.appendChild(timeEl);
        
        // Add to message group
        messageGroup.appendChild(messageWrapper);
        
        // Add reactions to the message if any
        if (message.reactions && message.reactions.length > 0) {
            updateMessageReactions(message.id, message.reactions);
        }
    });
    
    // Scroll to bottom with a small delay to ensure rendering is complete
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 10);
}

/**
 * Sends a message in the fullscreen chat
 * @param {string|number} userId - The ID of the user to send the message to
 * @param {string} message - The message text
 */
function sendFullscreenMessage(userId, message) {
    // Create a new message
    const newMessage = {
        sender: 'me',
        text: message,
        time: new Date()
    };
    
    // Save the message to the server via API call
    fetch('/api/messages.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            recipient_id: userId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error saving message:', data.message);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
    });
    
    // Add it to the messages
    const messagesContainer = document.getElementById('fullscreen-messages');
    
    // Find the last message row for sent messages or create a new one
    let messageRow = messagesContainer.querySelector('.message-row-sent:last-child');
    let isNewRow = false;
    
    if (!messageRow) {
        messageRow = document.createElement('div');
        messageRow.className = 'message-row message-row-sent';
        // Apply direct styles for proper alignment
        messageRow.style.justifyContent = 'flex-end';
        messageRow.style.alignItems = 'flex-end';
        messageRow.style.width = '100%';
        messagesContainer.appendChild(messageRow);
        isNewRow = true;
    }
    
    // Create the message wrapper
    const messageWrapper = document.createElement('div');
    messageWrapper.className = 'message-wrapper';
    messageWrapper.style.alignItems = 'flex-end'; // Align content to right
    
    // Create the message element
    const messageEl = document.createElement('div');
    messageEl.className = 'message message-sent';
    messageEl.style.float = 'right'; // Float to right edge
    messageEl.style.clear = 'both'; // Clear any floats
    messageEl.style.margin = '0'; // Reset margin
    messageEl.style.alignSelf = 'flex-end'; // Align to right
    
    // Get current user's avatar color from meta tag with a dynamic fetch to ensure latest color
    let currentUserColor = "#0084FF"; // Default blue color
    
    // First check meta tag (for immediate display)
    const currentUserColorMeta = document.querySelector('meta[name="user-avatar-color"]');
    if (currentUserColorMeta) {
        currentUserColor = currentUserColorMeta.getAttribute('content');
    }
    
    // Apply initial avatar color to message bubble
    messageEl.style.backgroundColor = currentUserColor;
    
    // Then fetch latest user data to ensure we have the most up-to-date color
    fetch('/api/users.php?current=true')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user && data.user.avatar_color) {
                // Update with latest color from API
                currentUserColor = data.user.avatar_color;
                
                // Also update the meta tag with the latest color
                if (currentUserColorMeta) {
                    currentUserColorMeta.setAttribute('content', currentUserColor);
                }
                
                // Update this message bubble with the new color
                messageEl.style.backgroundColor = currentUserColor;
                
                // Set text color based on the bubble color's brightness
                if (isLightColor(currentUserColor)) {
                    messageEl.classList.add('light-bubble');
                    messageEl.style.color = '#000000'; // Set text color to black for light backgrounds
                } else {
                    messageEl.classList.add('dark-bubble');
                    messageEl.style.color = '#FFFFFF'; // Set text color to white for dark backgrounds
                }
            }
        })
        .catch(error => {
            console.error('Error fetching current user data:', error);
        });
    
    // Initial text color based on default bubble color (will be updated after API call)
    if (isLightColor(currentUserColor)) {
        messageEl.classList.add('light-bubble');
        messageEl.style.color = '#000000'; // Set text color to black for light backgrounds
    } else {
        messageEl.classList.add('dark-bubble');
        messageEl.style.color = '#FFFFFF'; // Set text color to white for dark backgrounds
    }
    
    messageEl.style.border = 'none'; // Remove border
    messageEl.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.1)'; // Add subtle shadow
    
    // If it's a consecutive message in an existing row, add the consecutive class
    if (!isNewRow) {
        messageEl.classList.add('consecutive-message');
    }
    
    // Convert URLs to clickable links before setting the content - pass the 'me' sender type
    const messageWithLinks = convertUrlsToLinks(newMessage.text, 'me');
    messageEl.innerHTML = messageWithLinks;
    
    // If the message is short (less than 30 characters), apply special bubble styling
    if (newMessage.text.length < 30) {
        // Set width based on content but not exceeding bubble width
        messageEl.style.width = 'auto';
        messageEl.style.whiteSpace = 'nowrap';
        messageEl.style.wordBreak = 'normal';
        
        // Set reasonable min and max width for the bubble
        messageEl.style.minWidth = '60px';
        messageEl.style.maxWidth = '90%';
        messageEl.style.width = 'fit-content';
        
        // No ellipsis or overflow hiding, let text wrap naturally when it reaches 90% width
    }
    
    // Check if we should show time for this message
    // We'll always show time for a newly sent message
    const shouldShowTime = true;
    
    // Assemble the message components
    messageWrapper.appendChild(messageEl);
    
    // Only add time if needed
    if (shouldShowTime) {
        // Create the timestamp container with read receipts
        const timeEl = document.createElement('div');
        timeEl.className = 'message-time-container';
        
        // Create time text
        const timeTextEl = document.createElement('span');
        timeTextEl.className = 'message-time';
        timeTextEl.textContent = formatTime(newMessage.time);
        timeEl.appendChild(timeTextEl);
        
        // Add read receipt for sent message (single check for just sent)
        const readReceiptEl = document.createElement('span');
        readReceiptEl.className = 'message-read-receipt';
        readReceiptEl.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 12 12" fill="none" style="display: inline-block; vertical-align: middle;">
            <path d="M3.5 6.5L5.5 8.5L8.5 3.5" stroke="#8E8E93" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;
        readReceiptEl.title = 'Sent';
        readReceiptEl.style.marginLeft = '4px';
        timeEl.appendChild(readReceiptEl);
        
        // Add time below the message
        messageWrapper.appendChild(timeEl);
    }
    messageRow.appendChild(messageWrapper);
    
    // Scroll to bottom with a small delay to ensure rendering is complete
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 10);
    
    // Play send sound
    playMessageSound('sent');
    
    // Update chat preview
    updateChatPreview(userId, message);
}

/**
 * Formats a time object to a string
 * @param {Date} date - The date to format
 * @returns {string} The formatted time string
 */
function formatTime(date) {
    const hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    
    // Convert to 12-hour format with AM/PM
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    
    return `${displayHours}:${minutes} ${ampm}`;
}

/**
 * Plays a sound when a message is sent or received
 * @param {string} type - The type of sound to play ('sent' or 'received')
 */
function playMessageSound(type) {
    // Audio features would go here in production
    console.log(`Playing ${type} message sound`);
}

/**
 * Updates the chat preview with the latest message and marks it as unread
 * @param {string|number} userId - The ID of the user
 * @param {string} message - The message text
 */
function updateChatPreview(userId, message) {
    // If unreadMessages is not defined globally, create it
    if (typeof unreadMessages === 'undefined') {
        window.unreadMessages = {};
    }
    
    const chatItem = document.querySelector(`.chat-item[data-user-id="${userId}"]`);
    if (!chatItem) return;
    
    // Update preview text
    const chatPreview = chatItem.querySelector('.chat-preview');
    if (chatPreview) {
        chatPreview.textContent = message;
    }
    
    // Only mark as unread if user is not currently viewing this chat
    const fullscreenChat = document.querySelector('.chat-fullscreen');
    const isCurrentlyViewing = fullscreenChat && 
        fullscreenChat.getAttribute('data-user-id') === userId.toString();
    
    if (!isCurrentlyViewing) {
        // Mark as unread
        chatItem.classList.add('unread');
        
        // Add bold styling to name and preview
        const chatName = chatItem.querySelector('.chat-name');
        const chatPreview = chatItem.querySelector('.chat-preview');
        
        if (chatName) chatName.style.fontWeight = '700';
        if (chatName) chatName.style.color = '#000000';
        if (chatPreview) chatPreview.style.fontWeight = '600';
        if (chatPreview) chatPreview.style.color = '#000000';
        
        // Update unread count
        if (!unreadMessages[userId]) {
            unreadMessages[userId] = 0;
        }
        unreadMessages[userId]++;
        
        // Update or add unread badge
        let chatUnread = chatItem.querySelector('.chat-unread');
        if (!chatUnread) {
            chatUnread = document.createElement('div');
            chatUnread.className = 'chat-unread';
            const chatMeta = chatItem.querySelector('.chat-meta');
            if (chatMeta) {
                chatMeta.appendChild(chatUnread);
            }
        }
        
        if (chatUnread) {
            chatUnread.textContent = unreadMessages[userId];
        }
    }
}

/**
 * Sets up event listeners for viewport adjustments when keyboard appears
 * This helps ensure the chat input stays visible when keyboard is open
 */
function setupViewportAdjustment() {
    // For iOS devices
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        window.addEventListener('resize', handleIOSKeyboard);
    }
    
    // For Android devices
    if (/Android/.test(navigator.userAgent)) {
        window.addEventListener('resize', handleAndroidKeyboard);
    }
    
    // Clean up the event listeners when chat is closed
    const originalCloseFunction = closeFullscreenChat;
    closeFullscreenChat = function() {
        // Call the original close function
        originalCloseFunction();
        
        // Remove the event listeners
        window.removeEventListener('resize', handleIOSKeyboard);
        window.removeEventListener('resize', handleAndroidKeyboard);
    };
}

/**
 * Handles keyboard appearance on iOS devices
 */
function handleIOSKeyboard() {
    const messagesContainer = document.getElementById('fullscreen-messages');
    const fullscreenChat = document.getElementById('fullscreen-chat');
    
    if (!messagesContainer || !fullscreenChat) return;
    
    // Get the current viewport height
    const viewportHeight = window.innerHeight;
    
    // Set a timeout to let the DOM update
    setTimeout(() => {
        // Scroll to bottom to ensure the latest messages are visible
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 50);
}

/**
 * Handles keyboard appearance on Android devices
 */
function handleAndroidKeyboard() {
    const messagesContainer = document.getElementById('fullscreen-messages');
    const fullscreenChat = document.getElementById('fullscreen-chat');
    
    if (!messagesContainer || !fullscreenChat) return;
    
    // Get the current viewport height
    const viewportHeight = window.innerHeight;
    
    // Set a timeout to let the DOM update
    setTimeout(() => {
        // Scroll to bottom to ensure the latest messages are visible
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
}

/**
 * Adds ripple effect to input area
 * @param {HTMLElement} element - The element to add ripple to
 * @param {Event} event - The event (mouse or touch) that triggered the ripple
 * @param {boolean} isTouch - Whether the event is a touch event
 */
function addRippleToInput(element, event, isTouch = false) {
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
    const size = Math.max(rect.width, rect.height);
    
    // Position and style the ripple - use lighter color for input area
    ripple.style.cssText = `
        position: absolute;
        top: ${y - size/2}px;
        left: ${x - size/2}px;
        width: ${size}px;
        height: ${size}px;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        transform: scale(0);
        z-index: 9999;
        pointer-events: none;
        animation: rippleEffect 0.6s linear;
    `;
    
    // Add ripple to element
    element.appendChild(ripple);
    
    // Remove the ripple after animation completes
    setTimeout(() => {
        if (ripple && ripple.parentNode) {
            ripple.parentNode.removeChild(ripple);
        }
    }, 600);
}

/**
 * Adds ripple effect to send button
 * @param {HTMLElement} element - The element to add ripple to
 * @param {Event} event - The event (mouse or touch) that triggered the ripple
 * @param {boolean} isTouch - Whether the event is a touch event
 */
function addRippleToSendButton(element, event, isTouch = false) {
    // Check if a ripple already exists (to prevent multiple ripples)
    const existingRipple = element.querySelector('span[style*="rippleEffect"]');
    if (existingRipple) {
        return; // Don't create another ripple if one already exists
    }
    
    // Get coordinates
    const rect = element.getBoundingClientRect();
    
    // For button, we set the ripple at the center
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    // Create ripple element
    const ripple = document.createElement('span');
    
    // Calculate the size of the ripple
    // For circular button, make it larger than the button
    const size = Math.max(rect.width, rect.height) * 1.5;
    
    // Position and style the ripple - use white color with opacity for button
    ripple.style.cssText = `
        position: absolute;
        top: ${centerY - size/2}px;
        left: ${centerX - size/2}px;
        width: ${size}px;
        height: ${size}px;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        z-index: 9999;
        pointer-events: none;
        animation: rippleEffect 0.5s linear;
    `;
    
    // Add ripple to element
    element.appendChild(ripple);
    
    // Remove the ripple after animation completes
    setTimeout(() => {
        if (ripple && ripple.parentNode) {
            ripple.parentNode.removeChild(ripple);
        }
    }, 500);
}

/**
 * Adds long press event listeners to a message element
 * @param {HTMLElement} messageEl - The message element to add listeners to
 */
function addLongPressListeners(messageEl) {
    console.log('Adding long press listeners to message:', messageEl);
    console.log('Message ID:', messageEl.getAttribute('data-message-id'));
    console.log('Message Sender:', messageEl.getAttribute('data-sender'));
    
    // Skip deleted messages - don't allow long press on them
    if (messageEl.classList.contains('deleted-message')) {
        return;
    }
    
    // First remove any existing listeners to prevent duplicates
    messageEl.removeEventListener('touchstart', handleTouchStart);
    messageEl.removeEventListener('touchend', handleTouchEnd);
    messageEl.removeEventListener('touchcancel', handleTouchEnd);
    messageEl.removeEventListener('touchmove', handleTouchMove);
    messageEl.removeEventListener('mousedown', handleMouseDown);
    messageEl.removeEventListener('mouseup', handleMouseUp);
    messageEl.removeEventListener('mouseleave', handleMouseUp);
    
    // Now add new listeners
    // Track touch events for mobile
    messageEl.addEventListener('touchstart', handleTouchStart, { passive: false });
    messageEl.addEventListener('touchend', handleTouchEnd);
    messageEl.addEventListener('touchcancel', handleTouchEnd);
    messageEl.addEventListener('touchmove', handleTouchMove);
    
    // Track mouse events for desktop
    messageEl.addEventListener('mousedown', handleMouseDown);
    messageEl.addEventListener('mouseup', handleMouseUp);
    messageEl.addEventListener('mouseleave', handleMouseUp);
    
    // Add right-click (context menu) event for desktop as an alternative to long press
    messageEl.addEventListener('contextmenu', function(e) {
        e.preventDefault(); // Prevent default context menu
        showMessageOptions(this);
        return false;
    });
    
    // Add a direct click event for easier popup access 
    // Double click now shows the options menu
    messageEl.addEventListener('dblclick', function(e) {
        e.preventDefault();
        // Reset any menu state before showing the options
        hideMessageOptions();
        // Delay slightly before showing new menu
        setTimeout(() => {
            showMessageOptions(this);
        }, 10);
        return false;
    });
    
    // Add a regular single click handler too for mobile devices
    // This will show the menu after a short delay
    messageEl.addEventListener('click', function(e) {
        // Only proceed if message options aren't already visible
        if (!messageOptionsVisible) {
            // Show the options after a 10ms delay
            setTimeout(() => {
                showMessageOptions(this);
            }, 10);
        }
    });
    
    // Add a visual indicator that the message is long-pressable
    messageEl.style.position = 'relative';
    messageEl.title = 'Press and hold to see options';
}

/**
 * Handles touch start event for long press
 * @param {TouchEvent} e - The touch event
 */
function handleTouchStart(e) {
    console.log('Touch Start event detected on message', this);
    e.preventDefault(); // Prevent default to avoid selection/highlighting
    
    // Reset the touch moved flag
    longPressTouchMoved = false;
    
    // Store initial touch position
    if (e.touches && e.touches[0]) {
        touchStartPosition.x = e.touches[0].clientX;
        touchStartPosition.y = e.touches[0].clientY;
    }
    
    // Add a visual feedback immediately on touch (only opacity, no color change)
    this.style.opacity = '0.7';
    
    // Only start a long press if there isn't a visible menu already
    if (!messageOptionsVisible) {
        // Start long press detection
        startLongPress(this);
    }
}

/**
 * Handles touch end event for long press
 */
function handleTouchEnd() {
    console.log('Touch End event detected');
    
    // Reset any visual feedback (only opacity, no color change)
    if (activeMessageElement) {
        activeMessageElement.style.opacity = '1';
    }
    
    // Only cancel if menu isn't already visible
    if (!messageOptionsVisible) {
        cancelLongPress();
    }
}

/**
 * Handles touch move event for long press
 * @param {TouchEvent} e - The touch event
 */
function handleTouchMove(e) {
    // If there's no touch or no starting position, exit
    if (!e.touches || !e.touches[0]) return;
    
    // Calculate movement distance
    const moveX = Math.abs(e.touches[0].clientX - touchStartPosition.x);
    const moveY = Math.abs(e.touches[0].clientY - touchStartPosition.y);
    
    // If moved more than 10 pixels in any direction, cancel long press
    if (moveX > 10 || moveY > 10) {
        console.log('Touch moved too much - cancelling long press');
        longPressTouchMoved = true;
        
        // Reset any visual feedback (only opacity, no color change)
        if (activeMessageElement) {
            activeMessageElement.style.opacity = '1';
        }
        
        cancelLongPress();
    }
}

/**
 * Handles mouse down event for long press
 * @param {MouseEvent} e - The mouse event
 */
function handleMouseDown(e) {
    console.log('Mouse Down event detected on message', this);
    // Only handle left mouse button
    if (e.button === 0) {
        // Add visual feedback on mouse down (only opacity, no color change)
        this.style.opacity = '0.7';
        
        // Only start a long press if there isn't a visible menu already
        if (!messageOptionsVisible) {
            startLongPress(this);
        }
    }
}

/**
 * Handles mouse up event for long press
 */
function handleMouseUp() {
    console.log('Mouse Up event detected');
    
    // Reset visual feedback (only opacity, no color change)
    if (activeMessageElement) {
        activeMessageElement.style.opacity = '1';
    }
    
    // Only cancel if menu isn't already visible
    if (!messageOptionsVisible) {
        cancelLongPress();
    }
}

/**
 * Starts the long press timer
 * @param {HTMLElement} element - The element being long-pressed
 */
function startLongPress(element) {
    console.log('Long press started on message', element);
    
    // Don't start a new timer if there's already one or if options are already visible
    if (longPressTimer || messageOptionsVisible) {
        console.log('Long press ignored - timer already active or menu visible');
        return;
    }
    
    // Store the element being long-pressed
    activeMessageElement = element;
    
    // Start the timer
    longPressTimer = setTimeout(() => {
        console.log('Long press timeout triggered - showing both menus');
        // Show message options AND reaction selector when long press is complete
        showMessageOptions(element);
        showReactionSelector(element);
    }, longPressTimeout);
    
    // Add visual feedback that the element is being pressed
    element.style.opacity = '0.7';
}

/**
 * Cancels the long press timer
 */
function cancelLongPress() {
    if (longPressTimer) {
        clearTimeout(longPressTimer);
        longPressTimer = null;
    }
    
    // Remove visual feedback if we have an active element
    if (activeMessageElement) {
        activeMessageElement.style.opacity = '1';
    }
}

/**
 * Shows the message options menu
 * @param {HTMLElement} messageEl - The message element
 */
function showMessageOptions(messageEl) {
    console.log('Showing message options for element:', messageEl);
    
    // Force hide any existing menu first to prevent state issues
    hideMessageOptions();
    
    // Set the flag to indicate options are visible
    messageOptionsVisible = true;
    
    // First, check if message options already exist
    let messageOptions = document.getElementById('message-options');
    if (messageOptions) {
        messageOptions.remove();
    }
    
    // Remove any existing overlay
    let overlay = document.getElementById('message-overlay');
    if (overlay) {
        overlay.remove();
    }
    
    // Create overlay - fully transparent, only for capturing events, not for visual effect
    overlay = document.createElement('div');
    overlay.id = 'message-overlay';
    overlay.className = 'message-overlay';
    overlay.style.display = 'block';
    overlay.style.backgroundColor = 'transparent';
    
    // Don't specify z-index here, let the CSS handle it based on desktop/mobile mode
    // overlay.style.zIndex = '9500';
    
    // Add handlers to overlay to close options (for various interactions)
    overlay.addEventListener('click', hideMessageOptions);
    overlay.addEventListener('touchstart', hideMessageOptions);
    // Removed touchmove to prevent accidental dismissal during scrolling
    
    // The fullscreen chat container is the main container for chat
    const fullscreenChat = document.getElementById('fullscreen-chat-container');
    if (fullscreenChat) {
        // Direct event listeners to close the options menu when interacting with 
        // any part of the fullscreen chat container except the message or options
        fullscreenChat.addEventListener('click', function(e) {
            // Only if click is not on message options or the message itself
            if (!e.target.closest('#message-options') && !e.target.closest('.message')) {
                hideMessageOptions();
            }
        });
        
        fullscreenChat.addEventListener('touchstart', function(e) {
            // Only if touch is not on message options or the message itself
            if (!e.target.closest('#message-options') && !e.target.closest('.message')) {
                hideMessageOptions();
            }
        });
        
        // Removed the touchmove event listener to prevent accidental dismissal during scrolling
    }
    
    // Also add to the fullscreen-chat parent div, just to be sure
    const fullscreenChatParent = document.getElementById('fullscreen-chat');
    if (fullscreenChatParent) {
        fullscreenChatParent.addEventListener('click', function(e) {
            if (!e.target.closest('#message-options') && !e.target.closest('.message')) {
                hideMessageOptions();
            }
        });
        
        fullscreenChatParent.addEventListener('touchstart', function(e) {
            if (!e.target.closest('#message-options') && !e.target.closest('.message')) {
                hideMessageOptions();
            }
        });
        
        // Removed the touchmove event listener to prevent accidental dismissal during scrolling
    }
    
    // Get message data
    const messageId = messageEl.getAttribute('data-message-id');
    const sender = messageEl.getAttribute('data-sender');
    const isSentByMe = sender === 'me';
    const messageText = messageEl.textContent || '';
    
    console.log('Message ID:', messageId, 'Sender:', sender, 'Sent by me:', isSentByMe);
    
    // Create options menu - messenger bottom bar style
    messageOptions = document.createElement('div');
    messageOptions.id = 'message-options';
    messageOptions.className = 'message-options message-options-bottom';
    messageOptions.style.display = 'flex';
    // Don't set z-index here, let the CSS handle it based on desktop/mobile mode
    
    // Position at the bottom of the screen (Messenger style)
    const isDesktop = window.innerWidth >= 768;
    
    // Desktop: Always use absolute positioning in the container
    // Mobile: Use fixed positioning for viewport-level positioning
    messageOptions.style.position = isDesktop ? 'absolute' : 'fixed';
    
    messageOptions.style.bottom = '70px'; // Position above the input area
    messageOptions.style.left = '0';
    messageOptions.style.right = '0';
    messageOptions.style.width = '100%';
    
    // Create options based on message sender
    // If the message was sent by the current user, show both delete options
    // Otherwise, only show "delete for you"
    let optionsHTML = `
        <div class="option-item copy" id="copy-message">Copy</div>
    `;
    
    if (isSentByMe) {
        optionsHTML += `
            <div class="option-item delete-everyone" id="delete-for-everyone">Remove for Everyone</div>
            <div class="option-item delete-you" id="delete-for-you">Remove for You</div>
            <div class="option-item cancel" id="cancel-delete">Cancel</div>
        `;
    } else {
        optionsHTML += `
            <div class="option-item delete-you" id="delete-for-you">Remove for You</div>
            <div class="option-item cancel" id="cancel-delete">Cancel</div>
        `;
    }
    
    messageOptions.innerHTML = optionsHTML;
    
    // Add event listeners to options - ডেস্কটপে শুধু fullscreen-chat-container এ দেখাবে
    const fullscreenChatContainer = document.getElementById('fullscreen-chat-container');
    
    if (fullscreenChatContainer) {
        fullscreenChatContainer.appendChild(overlay);
        fullscreenChatContainer.appendChild(messageOptions);
    } else {
        document.body.appendChild(overlay);
        document.body.appendChild(messageOptions);
    }
    
    console.log('Added message options to document body. Options menu:', messageOptions, 'Overlay:', overlay);
    
    // Adjust position if menu is outside the viewport
    const menuRect = messageOptions.getBoundingClientRect();
    
    if (menuRect.right > window.innerWidth) {
        messageOptions.style.left = 'auto';
        messageOptions.style.right = '10px';
    }
    
    if (menuRect.left < 0) {
        messageOptions.style.left = '10px';
        messageOptions.style.right = 'auto';
    }
    
    if (menuRect.bottom > window.innerHeight) {
        messageOptions.style.top = 'auto';
        messageOptions.style.bottom = '70px';
    }
    
    if (menuRect.top < 60) { // Account for header height
        messageOptions.style.top = '70px';
    }
    
    // Add click handlers
    const copyMessage = document.getElementById('copy-message');
    if (copyMessage) {
        copyMessage.addEventListener('click', () => {
            console.log('Copy message clicked');
            // Copy the message text to clipboard
            navigator.clipboard.writeText(messageText).then(() => {
                console.log('Message copied to clipboard');
                // Show a brief toast notification
                const toast = document.createElement('div');
                toast.textContent = 'Copied to clipboard';
                toast.style.position = 'fixed';
                toast.style.bottom = '50px';
                toast.style.left = '50%';
                toast.style.transform = 'translateX(-50%)';
                toast.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                toast.style.color = 'white';
                toast.style.padding = '8px 16px';
                toast.style.borderRadius = '20px';
                toast.style.fontSize = '14px';
                toast.style.zIndex = '10000';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }, 1500);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
            hideMessageOptions();
        });
    }
    
    const deleteForEveryone = document.getElementById('delete-for-everyone');
    if (deleteForEveryone) {
        deleteForEveryone.addEventListener('click', () => {
            console.log('Delete for everyone clicked');
            deleteMessage(messageId, 'for_everyone');
            hideMessageOptions();
        });
    }
    
    const deleteForYou = document.getElementById('delete-for-you');
    if (deleteForYou) {
        deleteForYou.addEventListener('click', () => {
            console.log('Delete for you clicked');
            deleteMessage(messageId, 'for_you');
            hideMessageOptions();
        });
    }
    
    const reactMessage = document.getElementById('react-message');
    if (reactMessage) {
        reactMessage.addEventListener('click', () => {
            console.log('React message clicked');
            showReactionSelector(messageEl);
            hideMessageOptions();
        });
    }
    
    const cancelDelete = document.getElementById('cancel-delete');
    if (cancelDelete) {
        cancelDelete.addEventListener('click', () => {
            console.log('Cancel delete clicked');
            hideMessageOptions();
        });
    }
    
    // Store a reference to the active message element
    activeMessageElement = messageEl;
    
    // Add global event listeners to document to close popup on any interaction
    // Short delay to avoid immediate closing
    setTimeout(() => {
        document.addEventListener('touchstart', (e) => {
            // Don't close if click/touch is on the message options menu
            if (e.target.closest('#message-options')) return;
            hideMessageOptions();
        });
        
        // Removed the touchmove event listener to prevent accidental dismissal during scrolling
        
        // Using a throttled version for mousemove to avoid performance issues
        let lastMove = 0;
        document.addEventListener('mousemove', (e) => {
            // Don't close if mouse is moving over the message options menu
            if (e.target.closest('#message-options')) return;
            
            const now = Date.now();
            if (now - lastMove > 150) { // Throttle to prevent too many calls
                lastMove = now;
                hideMessageOptions();
            }
        });
    }, 500); // Increased delay to ensure menu fully appears before listeners activate
}

/**
 * Hides the message options menu
 */
function hideMessageOptions() {
    // If options are already hidden, exit
    if (!messageOptionsVisible) return;
    
    // Reset the flag before any other operations
    messageOptionsVisible = false;
    console.log('Hiding message options');
    
    const messageOptions = document.getElementById('message-options');
    if (messageOptions) {
        messageOptions.remove();
    }
    
    const overlay = document.getElementById('message-overlay');
    if (overlay) {
        // Remove all event listeners before removing the overlay
        overlay.removeEventListener('click', hideMessageOptions);
        overlay.removeEventListener('touchstart', hideMessageOptions);
        // We've removed the touchmove and mousemove event listeners
        // to prevent popups from closing when scrolling
        overlay.remove();
    }
    
    // Reset opacity on active message element (only opacity, no color change)
    if (activeMessageElement) {
        activeMessageElement.style.opacity = '1';
        activeMessageElement = null;
    }
    
    // Reset long press timer if it exists
    if (longPressTimer) {
        clearTimeout(longPressTimer);
        longPressTimer = null;
    }
    
    // Remove all global event listeners
    document.removeEventListener('touchstart', hideMessageOptions);
    document.removeEventListener('mousemove', hideMessageOptions);
    // We've removed the touchmove event listener to prevent accidental dismissal during scrolling
    
    // Remove event listeners from fullscreen chat container
    const fullscreenChat = document.getElementById('fullscreen-chat');
    if (fullscreenChat) {
        // We just want to remove the event listeners we added for menu closing
        // But keep the original event listeners
        if (typeof originalCloseClickHandlers === 'undefined') {
            // If first time, just do nothing - don't clone
            console.log('No need to restore fullscreen chat listeners, first interaction');
        } else {
            // Clone and replace to remove all listeners
            const newFullscreenChat = fullscreenChat.cloneNode(true);
            fullscreenChat.parentNode.replaceChild(newFullscreenChat, fullscreenChat);
            
            // No need to create a function for this
            // We'll just use the existing logic for handling popup closing
            console.log('Restoring original event listeners will happen automatically');
        }
    }
    
    // Remove event listeners from fullscreen messages container
    const messagesContainer = document.getElementById('fullscreen-messages');
    if (messagesContainer) {
        // Same approach - only remove if we actually added listeners before
        if (typeof originalCloseClickHandlers === 'undefined') {
            console.log('No need to restore message container listeners, first interaction');
        } else {
            // Add long press listeners to messages again
            if (currentFullscreenUserId) {
                // This just ensures the messages have proper event listeners
                // We do this automatically when loading messages
                loadFullscreenMessages(currentFullscreenUserId);
            }
        }
    }
}

/**
 * Deletes a message
 * @param {string|number} messageId - The ID of the message to delete
 * @param {string} deleteType - The type of deletion ('for_you' or 'for_everyone')
 */
function deleteMessage(messageId, deleteType) {
    // Send delete request to the server
    fetch('/api/messages.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'delete_message',
            message_id: messageId,
            delete_type: deleteType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the message from the UI
            // Since the message is deleted by the current user, pass 'you' as deletedBy
            removeMessageFromUI(messageId, 'you');
        } else {
            console.error('Error deleting message:', data.message);
        }
    })
    .catch(error => {
        console.error('Error deleting message:', error);
    });
}

/**
 * Shows the reaction selector for a message
 * @param {HTMLElement} messageEl - The message element
 */
function showReactionSelector(messageEl) {
    console.log('Showing reaction selector for message element:', messageEl);
    
    // Check if this is a message sent by the current user
    const messageSender = messageEl.getAttribute('data-sender');
    if (messageSender === 'me') {
        // নিজের মেসেজে নিজে রিঅ্যাক্ট করা যাবে না
        console.log('Cannot react to your own message');
        return; // Exit the function without showing the reaction selector
    }
    
    // First, remove any existing reaction selector
    const existingSelector = document.getElementById('reaction-selector');
    if (existingSelector) {
        existingSelector.remove();
    }
    
    const messageId = messageEl.getAttribute('data-message-id');
    console.log('Message ID for reaction:', messageId);
    
    // Create reaction selector - transparent background
    const reactionSelector = document.createElement('div');
    reactionSelector.id = 'reaction-selector';
    reactionSelector.className = 'reaction-emoji-selector';
    reactionSelector.style.backgroundColor = 'white'; // Keep white background for the selector itself
    
    // Define reaction options - updated emojis like Facebook Messenger
    const reactions = [
        { emoji: '👍', name: 'like', bengaliName: 'লাইক' },
        { emoji: '❤️', name: 'love', bengaliName: 'ভালোবাসা' },
        { emoji: '😂', name: 'laugh', bengaliName: 'হাসি' },
        { emoji: '😲', name: 'wow', bengaliName: 'অবাক' },
        { emoji: '😢', name: 'sad', bengaliName: 'দুঃখ' },
        { emoji: '😡', name: 'angry', bengaliName: 'রাগ' }
    ];
    
    // Find the current user's reaction on this message if any
    let currentUserReaction = null;
    const allMessages = document.querySelectorAll('.message');
    for (let msg of allMessages) {
        if (msg.getAttribute('data-message-id') === messageId) {
            const reactionsContainer = msg.querySelector('.message-reactions');
            if (reactionsContainer) {
                const activeReaction = reactionsContainer.querySelector('.reaction-emoji.active');
                if (activeReaction) {
                    currentUserReaction = activeReaction.getAttribute('data-reaction-type');
                    console.log('Found current user reaction:', currentUserReaction);
                }
            }
            break;
        }
    }
    
    // Add each reaction option with sequential animations like Facebook Messenger
    reactions.forEach((reaction, index) => {
        const reactionOption = document.createElement('div');
        reactionOption.className = 'reaction-option';
        
        // Highlight current reaction if any
        if (currentUserReaction === reaction.name) {
            reactionOption.classList.add('active');
        }
        
        // Add index as a CSS variable for staggered animation
        reactionOption.style.setProperty('--index', index);
        
        reactionOption.textContent = reaction.emoji;
        reactionOption.title = reaction.bengaliName;
        reactionOption.setAttribute('data-reaction', reaction.name);
        
        // Add click event to handle reaction
        reactionOption.addEventListener('click', () => {
            console.log(`Selected reaction: ${reaction.name}`);
            
            // If clicking on the current reaction, remove it (toggle behavior)
            const newReactionType = (currentUserReaction === reaction.name) ? null : reaction.name;
            addReactionToMessage(messageId, newReactionType);
            reactionSelector.remove();
        });
        
        reactionSelector.appendChild(reactionOption);
    });
    
    // Get the bounding rectangle of the message element
    const messageRect = messageEl.getBoundingClientRect();
    console.log('Message position:', messageRect);
    
    // Calculate window dimensions and scrolling information
    const windowWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    // Check if message is in the bottom half of the viewport
    const halfViewportHeight = viewportHeight / 2;
    const isInBottomHalf = messageRect.top > halfViewportHeight;
    
    // Also check if this is the last message (near bottom)
    const isLastMessage = messageRect.bottom > (viewportHeight - 150);
    
    // Use either condition to decide if we should show above
    const shouldShowAbove = isInBottomHalf || isLastMessage;
    
    console.log('Viewport height:', viewportHeight);
    console.log('Message top position:', messageRect.top);
    console.log('Message bottom position:', messageRect.bottom);
    console.log('Is last message:', isLastMessage);
    console.log('Is message near bottom:', shouldShowAbove);
    
    // ডেস্কটপে শুধু fullscreen-chat-container এ দেখাবে
    const fullscreenChatContainer = document.getElementById('fullscreen-chat-container');
    
    // Always append to fullscreen-chat-container if it exists
    if (fullscreenChatContainer) {
        fullscreenChatContainer.appendChild(reactionSelector);
    } else {
        // Fallback only if container doesn't exist
        document.body.appendChild(reactionSelector);
    }
    
    // Configure the selector style - ডেস্কটপে position:absolute করা হচ্ছে যাতে পেজ জুড়ে না আসে
    const isDesktop = window.innerWidth >= 768;
    
    // Desktop: Always use absolute positioning in the container
    reactionSelector.style.position = isDesktop ? 'absolute' : 'fixed';
    
    reactionSelector.style.display = 'flex';
    reactionSelector.style.opacity = '1';
    reactionSelector.style.border = 'none';
    reactionSelector.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.08)';
    reactionSelector.style.transform = 'translateZ(0)'; // হার্ডওয়্যার অ্যাক্সিলারেশন
    // Don't set z-index here, let the CSS handle it based on desktop/mobile mode
    
    // Calculate position (centered on message)
    const centerX = messageRect.left + (messageRect.width / 2);
    const selectorWidth = 210; // Approximate width
    
    // Calculate left position with bounds checking
    let leftPos = centerX - (selectorWidth / 2);
    if (leftPos < 10) leftPos = 10;
    if ((leftPos + selectorWidth) > windowWidth - 10) {
        leftPos = windowWidth - 10 - selectorWidth;
    }
    
    // Set the horizontal position
    reactionSelector.style.left = leftPos + 'px';
    
    // Set vertical position based on message location
    if (shouldShowAbove) {
        // Position ABOVE the message if it's in the bottom half of the screen
        reactionSelector.style.top = (messageRect.top - 50) + 'px';
        console.log('Positioning ABOVE message');
    } else {
        // Position BELOW the message if it's in the top half of the screen
        reactionSelector.style.top = (messageRect.bottom + 5) + 'px';
        console.log('Positioning BELOW message');
    }
    
    console.log('Reaction selector positioned at:', {
        left: reactionSelector.style.left,
        top: reactionSelector.style.top
    });
    
    // Create a click handler to close the reaction selector when clicking outside
    const handleDocumentClick = (e) => {
        if (!e.target.closest('#reaction-selector')) {
            console.log('Clicked outside reaction selector, removing');
            reactionSelector.remove();
            document.removeEventListener('click', handleDocumentClick);
        }
    };
    
    // Use setTimeout to avoid immediate closing
    setTimeout(() => {
        document.addEventListener('click', handleDocumentClick);
    }, 200); // Increased delay
}

/**
 * Adds a reaction to a message
 * @param {string|number} messageId - The ID of the message
 * @param {string} reactionType - The type of reaction (like, love, etc.)
 */
function addReactionToMessage(messageId, reactionType) {
    fetch('/api/messages.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'react_message',
            message_id: messageId,
            reaction_type: reactionType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Reaction added successfully:', data);
            
            // Update UI to show the reaction
            updateMessageReactions(messageId, data.data.reactions);
            
            // Reload messages to ensure correct data
            if (currentFullscreenUserId) {
                loadFullscreenMessages(currentFullscreenUserId);
            }
        } else {
            console.error('Error adding reaction:', data.message);
        }
    })
    .catch(error => {
        console.error('Error adding reaction:', error);
    });
}

/**
 * Updates the UI to show reactions for a message
 * @param {string|number} messageId - The ID of the message
 * @param {Array} reactions - Array of reaction objects
 */
function updateMessageReactions(messageId, reactions) {
    const messageEl = document.querySelector(`.message[data-message-id="${messageId}"]`);
    if (!messageEl) return;
    
    // Remove existing reactions container if any
    const existingReactions = messageEl.querySelector('.message-reactions');
    if (existingReactions) {
        existingReactions.remove();
    }
    
    // If no reactions, do nothing
    if (!reactions || reactions.length === 0) return;
    
    // Count reactions by type
    const reactionCounts = {};
    reactions.forEach(reaction => {
        const type = reaction.type;
        reactionCounts[type] = (reactionCounts[type] || 0) + 1;
    });
    
    // Create reactions container
    const reactionsContainer = document.createElement('div');
    reactionsContainer.className = 'message-reactions';
    
    // Add reaction emojis with counts
    const emojiMap = {
        'like': '👍',
        'love': '❤️',
        'laugh': '😆',
        'wow': '😮',
        'sad': '😢',
        'angry': '😠'
    };
    
    // Get the current user's reaction if any
    let currentUserReaction = null;
    if (currentUserId) {
        const userReaction = reactions.find(r => r.user_id == currentUserId);
        if (userReaction) {
            currentUserReaction = userReaction.type;
        }
    }
    
    // Display reactions
    Object.keys(reactionCounts).forEach(type => {
        const reactionWrapper = document.createElement('div');
        reactionWrapper.className = 'reaction-emoji';
        
        // Add data attribute for tracking reaction type
        reactionWrapper.setAttribute('data-reaction-type', type);
        
        if (currentUserReaction === type) {
            reactionWrapper.classList.add('active');
        }
        
        const emoji = emojiMap[type] || '👍';
        reactionWrapper.innerHTML = `${emoji}`;
        
        if (reactionCounts[type] > 1) {
            const count = document.createElement('span');
            count.className = 'reaction-count';
            count.textContent = reactionCounts[type];
            reactionWrapper.appendChild(count);
        }
        
        // Add click event to toggle this reaction
        reactionWrapper.addEventListener('click', () => {
            // If this is the current user's reaction, remove it
            // Otherwise, set this as the new reaction
            const newReactionType = (currentUserReaction === type) ? null : type;
            addReactionToMessage(messageId, newReactionType);
        });
        
        reactionsContainer.appendChild(reactionWrapper);
    });
    
    // Add reactions container to message
    messageEl.appendChild(reactionsContainer);
}

/**
 * Detects URLs in text and converts them to clickable links
 * @param {string} text - The message text to process
 * @param {string} sender - The message sender ('me' or 'other')
 * @returns {string} - HTML with clickable links
 */
function convertUrlsToLinks(text, sender) {
    if (!text) return '';
    
    // Regular expression to find URLs
    // This pattern matches http://, https://, ftp:// URLs as well as "www." prefixed addresses
    const urlRegex = /(https?:\/\/|www\.)[^\s\n]+/gi;
    
    // Determine link color based on message sender and bubble background color
    let linkColor;
    
    if (sender === 'me') {
        // For messages sent by me (current user), check bubble color
        const currentUserColorMeta = document.querySelector('meta[name="user-avatar-color"]');
        let currentUserColor = "#0084FF"; // Default bubble color
        
        // Get color from meta tag
        if (currentUserColorMeta) {
            currentUserColor = currentUserColorMeta.getAttribute('content');
        }
        
        // Set link color based on bubble color brightness
        if (isLightColor(currentUserColor)) {
            linkColor = '#0066CC'; // Dark link for light bubbles
        } else {
            linkColor = 'rgba(255, 255, 255, 0.95)'; // Light link for dark bubbles
        }
    } else {
        // For messages received from others, use a color that contrasts with their bubble
        // Get the current fullscreen chat container
        const fullscreenChat = document.getElementById('fullscreen-chat');
        if (fullscreenChat && fullscreenChat.getAttribute('data-avatar-color')) {
            // Use a complementary or contrasting color based on the other user's avatar color
            const otherUserColor = fullscreenChat.getAttribute('data-avatar-color');
            
            // Determine if the other user's color is dark or light
            const isLight = isLightColor(otherUserColor);
            
            // If other user's color is light, use a darker link color for contrast
            // If dark, use a lighter color for contrast
            linkColor = isLight ? '#0066CC' : 'rgba(255, 255, 255, 0.95)';
        } else {
            // Default link color if we can't determine the message bubble color
            linkColor = '#0066CC';
        }
    }
    
    // Replace URLs with HTML links
    return text.replace(urlRegex, function(url) {
        // Check if the URL already has http/https prefix
        let href = url;
        if (url.toLowerCase().indexOf('http') !== 0) {
            href = 'http://' + url; // Add http:// if not present
        }
        
        // Create link with styling based on the sender - added pointer and click/touch event handling
        return `<a href="${href}" target="_blank" rel="noopener noreferrer" 
            style="color: ${linkColor}; text-decoration: underline; font-weight: bold; word-break: break-all; font-size: 16px; cursor: pointer;"
            onclick="window.open('${href}', '_blank')" 
            ontouchend="window.open('${href}', '_blank')">${url}</a>`;
    });
}

/**
 * Determines if a color is light or dark
 * @param {string} color - The color in hex or rgb format
 * @returns {boolean} - True if the color is light, false if dark
 */
function isLightColor(color) {
    // Convert hex to RGB if necessary
    let r, g, b;
    
    if (color.startsWith('#')) {
        // Handle hex colors
        const hex = color.substring(1);
        r = parseInt(hex.substring(0, 2), 16);
        g = parseInt(hex.substring(2, 4), 16);
        b = parseInt(hex.substring(4, 6), 16);
    } else if (color.startsWith('rgb')) {
        // Handle rgb/rgba colors
        const rgbValues = color.match(/\d+/g);
        if (rgbValues && rgbValues.length >= 3) {
            r = parseInt(rgbValues[0]);
            g = parseInt(rgbValues[1]);
            b = parseInt(rgbValues[2]);
        } else {
            // Default to assuming it's a dark color
            return false;
        }
    } else {
        // Default to assuming it's a dark color
        return false;
    }
    
    // Calculate perceived brightness using the formula:
    // (0.299*R + 0.587*G + 0.114*B)
    const brightness = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
    // Return true for light colors (brightness > 0.5)
    return brightness > 0.5;
}

/**
 * Removes a message from the UI
 * @param {string|number} messageId - The ID of the message to remove
 * @param {string} deletedBy - Who deleted the message ('you' or username)
 */
function removeMessageFromUI(messageId, deletedBy = null) {
    const messageEl = document.querySelector(`.message[data-message-id="${messageId}"]`);
    if (!messageEl) return;
    
    // Get the message wrapper
    const messageWrapper = messageEl.closest('.message-wrapper');
    if (!messageWrapper) return;
    
    // Get the current user ID from the chat
    const currentFullscreenUserId = document.querySelector('.chat-fullscreen')?.getAttribute('data-user-id');
    
    // We'll add a fade-out and fade-in transition
    messageWrapper.style.transition = 'opacity 0.3s ease';
    messageWrapper.style.opacity = '0';
    
    // Instead of removing the message, update it to show deletion status
    setTimeout(() => {
        if (messageEl) {
            // Add deleted-message class
            messageEl.classList.add('deleted-message');
            
            // Style for deleted message - smaller size and more compact
            messageEl.style.fontStyle = 'italic';
            messageEl.style.opacity = '0.7';
            messageEl.style.color = '#666';
            messageEl.style.fontSize = '12px'; // Smaller font size
            messageEl.style.padding = '4px 8px'; // Smaller padding for more compact bubble
            messageEl.style.whiteSpace = 'nowrap'; // Prevent line breaks
            messageEl.style.overflow = 'hidden'; // Prevent text from overflowing
            messageEl.style.textOverflow = 'ellipsis'; // Add ellipsis for overflowing text
            
            // Set the deleted message text based on who deleted it
            if (deletedBy === 'you') {
                messageEl.textContent = 'You deleted a message';
            } else if (deletedBy) {
                messageEl.textContent = `${deletedBy} deleted a message`;
            } else {
                // Fallback if deletedBy is not provided
                messageEl.textContent = 'Message was deleted';
            }
            
            // Set smaller width for deletion messages
            messageEl.style.width = 'auto';
            messageEl.style.minWidth = '40px'; // Reduced from 60px
            messageEl.style.maxWidth = '200px'; // Fixed max width to prevent overflow
            messageEl.style.width = 'fit-content';
            
            // Fade the message back in
            messageWrapper.style.opacity = '1';
        }
    }, 300);
    
    // Reload messages after a delay to ensure server state is in sync
    setTimeout(() => {
        if (currentFullscreenUserId) {
            loadFullscreenMessages(currentFullscreenUserId);
            
            // Update chat list (update unread count and preview for deleted message)
            fetch('/api/users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.users) {
                        // Update chat list with fresh data from server
                        // This will refresh unread counts and message previews
                        if (typeof renderChatList === 'function') {
                            renderChatList(data.data.users);
                        } else if (window.renderChatList) {
                            window.renderChatList(data.data.users);
                        } else if (window.loadUsers) {
                            window.loadUsers('');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating chat list after message deletion:', error);
                });
        }
    }, 1000);
}