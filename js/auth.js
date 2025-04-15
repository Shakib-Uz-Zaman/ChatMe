/**
 * Authentication functionality for the messenger app
 * Handles login, registration, logout, and auth error handling
 */

document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    
    // Set up event listeners
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    
    /**
     * Login form handler
     * @param {Event} e - The submit event
     */
    function handleLogin(e) {
        e.preventDefault();
        
        // Get form values
        const loginId = document.getElementById('login_id').value.trim();
        const password = document.getElementById('password').value;
        
        // Validate input
        if (!loginId || !password) {
            showError('Please enter both login ID and password');
            return;
        }
        
        // Show loading state
        const loginButton = document.getElementById('login-button');
        loginButton.classList.add('loading');
        loginButton.disabled = true;
        
        // Clear previous messages
        hideError();
        hideSuccess();
        
        // Call login function
        login(loginId, password)
            .then(response => {
                // Handle successful login
                if (response.success) {
                    // Show success message with better styling
                    showSuccess('Login successful!');
                    
                    // Add success class to button
                    loginButton.classList.remove('loading');
                    loginButton.classList.add('success');
                    loginButton.querySelector('.btn-text').innerHTML = 'Redirecting <span class="dots-loading"><span>.</span><span>.</span><span>.</span></span>';
                    
                    // Redirect to home page with slight delay for better UX
                    setTimeout(() => {
                        // Add fade-out effect to the entire form
                        document.querySelector('.login-container').classList.add('fade-out');
                        
                        // Redirect after fade effect
                        setTimeout(() => {
                            window.location.href = 'home.php';
                        }, 300);
                    }, 1000);
                } else {
                    // Show error message
                    showError(response.message || 'Login failed. Please try again.');
                    loginButton.classList.remove('loading');
                    loginButton.disabled = false;
                }
            })
            .catch(error => {
                // Handle error
                console.error('Login error:', error);
                showError('An error occurred. Please try again.');
                loginButton.classList.remove('loading');
                loginButton.disabled = false;
            });
    }
    
    /**
     * Register form handler
     * @param {Event} e - The submit event
     */
    function handleRegister(e) {
        e.preventDefault();
        
        // Now we already validate the fields step by step in the multi-step form,
        // But let's do a final validation just to be sure
        
        // Get form values
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const emailOrMobile = document.getElementById('email_or_mobile').value.trim();
        const password = document.getElementById('password').value;
        const dateOfBirth = document.getElementById('date_of_birth').value;
        const gender = document.getElementById('gender').value;
        const avatarColor = document.getElementById('avatar_color').value;
        
        // Check if we're missing any required fields
        if (!firstName || !lastName || !emailOrMobile || !password || !dateOfBirth || !gender) {
            showError('Please fill in all fields');
            
            // Go back to the step that might be missing information
            if (!firstName || !lastName || !emailOrMobile) {
                // Go to step 1
                document.getElementById('register-step-3').style.display = 'none';
                document.getElementById('register-step-2').style.display = 'none';
                document.getElementById('register-step-1').style.display = 'block';
                document.getElementById('register-progress-bar').style.width = '33.33%';
                document.querySelector('.step-indicator[data-step="1"]').classList.add('active');
                document.querySelector('.step-indicator[data-step="2"]').classList.remove('active', 'completed');
                document.querySelector('.step-indicator[data-step="3"]').classList.remove('active', 'completed');
            } else if (!dateOfBirth || !gender || !password) {
                // Go to step 2
                document.getElementById('register-step-3').style.display = 'none';
                document.getElementById('register-step-2').style.display = 'block';
                document.getElementById('register-step-1').style.display = 'none';
                document.getElementById('register-progress-bar').style.width = '66.66%';
                document.querySelector('.step-indicator[data-step="1"]').classList.add('completed');
                document.querySelector('.step-indicator[data-step="2"]').classList.add('active');
                document.querySelector('.step-indicator[data-step="3"]').classList.remove('active', 'completed');
            }
            
            return;
        }
        
        // Show loading state
        const registerButton = document.getElementById('register-button');
        registerButton.classList.add('loading');
        registerButton.disabled = true;
        
        // Clear previous messages
        hideError();
        hideSuccess();
        
        // Generate a display name from first name and last name
        const displayName = firstName + ' ' + lastName;
        
        // Call register function with email/mobile as username
        register(firstName, lastName, emailOrMobile, emailOrMobile, displayName, password, avatarColor, dateOfBirth, gender)
            .then(response => {
                // Handle successful registration
                if (response.success) {
                    // Show success message with better styling
                    showSuccess('Registration successful!');
                    
                    // Add success class to button
                    registerButton.classList.remove('loading');
                    registerButton.classList.add('success');
                    registerButton.querySelector('.btn-text').innerHTML = 'Redirecting to login <span class="dots-loading"><span>.</span><span>.</span><span>.</span></span>';
                    
                    // Redirect to login page with slight delay for better UX
                    setTimeout(() => {
                        // Add fade-out effect to the entire form
                        document.querySelector('.register-container').classList.add('fade-out');
                        
                        // Redirect after fade effect
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 300);
                    }, 1000);
                } else {
                    // Show error message
                    showError(response.message || 'Registration failed. Please try again.');
                    registerButton.classList.remove('loading');
                    registerButton.disabled = false;
                }
            })
            .catch(error => {
                // Handle error
                console.error('Registration error:', error);
                showError('An error occurred. Please try again.');
                registerButton.classList.remove('loading');
                registerButton.disabled = false;
            });
    }
    
    /**
     * Login function - handles user login
     * @param {string} loginId - The user's username, email or mobile number
     * @param {string} password - The user's password
     * @returns {Promise} Promise that resolves when login is complete
     */
    function login(loginId, password) {
        return fetch('api/auth.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login_id: loginId,
                password: password
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        });
    }
    
    /**
     * Register function - handles user registration
     * @param {string} firstName - The user's first name
     * @param {string} lastName - The user's last name
     * @param {string} username - The user's username
     * @param {string} emailOrMobile - The user's email or mobile number
     * @param {string} displayName - The user's display name
     * @param {string} password - The user's password
     * @param {string} avatarColor - The user's avatar color
     * @param {string} dateOfBirth - The user's date of birth
     * @param {string} gender - The user's gender
     * @returns {Promise} Promise that resolves when registration is complete
     */
    function register(firstName, lastName, username, emailOrMobile, displayName, password, avatarColor, dateOfBirth, gender) {
        return fetch('api/auth.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                first_name: firstName,
                last_name: lastName,
                username: username,
                email_or_mobile: emailOrMobile,
                display_name: displayName,
                password: password,
                avatar_color: avatarColor,
                date_of_birth: dateOfBirth,
                gender: gender
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        });
    }
    
    /**
     * Show error message in the UI
     * @param {string} message - The error message to display
     */
    function showError(message) {
        if (errorMessage && errorText) {
            errorText.textContent = message;
            errorMessage.classList.add('show');
            
            // Focus first form field
            const firstInput = document.querySelector('form input');
            if (firstInput) {
                firstInput.focus();
            }
        }
    }
    
    /**
     * Hide error message in the UI
     */
    function hideError() {
        if (errorMessage) {
            errorMessage.classList.remove('show');
        }
    }
    
    /**
     * Show success message in the UI
     * @param {string} message - The success message to display (can include HTML)
     */
    function showSuccess(message) {
        if (successMessage && successText) {
            successText.innerHTML = message;
            successMessage.classList.add('show');
        }
    }
    
    /**
     * Hide success message in the UI
     */
    function hideSuccess() {
        if (successMessage) {
            successMessage.classList.remove('show');
        }
    }
});
