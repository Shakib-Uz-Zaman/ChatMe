    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toast message function
            function showToast(message) {
                alert(message);
            }
            
            // Message button
            const messageButton = document.getElementById('message-button');
            if (messageButton) {
                messageButton.addEventListener('click', function() {
                    window.location.href = 'messenger.php';
                });
            }
            
            // Call button
            const callButton = document.getElementById('call-button');
            if (callButton) {
                callButton.addEventListener('click', function() {
                    showToast('Calling feature coming soon');
                });
            }
            
            // Edit profile button
            const editProfileButton = document.getElementById('edit-profile-button');
            if (editProfileButton) {
                editProfileButton.addEventListener('click', function() {
                    showToast('Edit profile feature coming soon');
                });
            }
            
            // Logout button
            const logoutButton = document.getElementById('logout-button');
            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    // Log out by sending request to API
                    fetch('api/auth.php?action=logout', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'login.php';
                        } else {
                            showToast('Failed to log out. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred. Please try again.');
                    });
                });
            }
        });
    </script>
</body>
</html>
