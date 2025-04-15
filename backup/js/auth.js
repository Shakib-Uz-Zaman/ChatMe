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
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        // Validate input
        if (!username || !password) {
            showError('Please enter both username and password');
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
        login(username, password)
            .then(response => {
                // Handle successful login
                if (response.success) {
                    // Show success message briefly
                    showSuccess('Login successful! Redirecting...');
                    
                    // Redirect to messenger
                    setTimeout(() => {
                        window.location.href = 'messenger.php';
                    }, 500);
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
        
        // Get form values
        const username = document.getElementById('username').value.trim();
        const displayName = document.getElementById('display_name').value.trim();
        const password = document.getElementById('password').value;
        const avatarColor = document.getElementById('avatar_color').value;
        
        // Validate input
        if (!username || !displayName || !password) {
            showError('Please fill in all fields');
            return;
        }
        
        // Validate username format
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            showError('Username can only contain letters, numbers, and underscores');
            return;
        }
        
        // Validate password strength
        if (password.length < 8) {
            showError('Password must be at least 8 characters long');
            return;
        }
        
        // Show loading state
        const registerButton = document.getElementById('register-button');
        registerButton.classList.add('loading');
        registerButton.disabled = true;
        
        // Clear previous messages
        hideError();
        hideSuccess();
        
        // Call register function with avatar color
        register(username, displayName, password, avatarColor)
            .then(response => {
                // Handle successful registration
                if (response.success) {
                    // Show success message briefly
                    showSuccess('Registration successful! Redirecting to login...');
                    
                    // Redirect to login
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
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
     * @param {string} username - The user's username
     * @param {string} password - The user's password
     * @returns {Promise} Promise that resolves when login is complete
     */
    function login(username, password) {
        return fetch('api/auth.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
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
     * @param {string} username - The user's username
     * @param {string} displayName - The user's display name
     * @param {string} password - The user's password
     * @param {string} avatarColor - The user's avatar color
     * @returns {Promise} Promise that resolves when registration is complete
     */
    function register(username, displayName, password, avatarColor) {
        return fetch('api/auth.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                display_name: displayName,
                password: password,
                avatar_color: avatarColor
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
     * @param {string} message - The success message to display
     */
    function showSuccess(message) {
        if (successMessage && successText) {
            successText.textContent = message;
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
