function initializeApp() {
        showLoadingSpinner();
        
        // Add message listener for updating chat previews
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'newMessage' && event.data.userId && event.data.message) {
                updateChatPreview(event.data.userId, event.data.message);
                playMessageSound('received');
            }
        });
        
        // Load users from API
        fetch('/api/users.php')
            .then(response => response.json())
            .then(users => {
                currentUsers = users;
                renderChatList();
                hideLoadingSpinner();
                setupEventListeners();
            })
            .catch(error => {
                console.error('Error loading users:', error);
                showErrorState('Unable to load users. Please try again later.');
                hideLoadingSpinner();
            });
    }
