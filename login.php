<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatMe | Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <!-- Tab image for browsers -->
    <link rel="icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <meta property="og:image" content="https://i.imghippo.com/files/tYd2694rng.jpg">
    <style>
        :root {
            --primary-color: #404547;
            --primary-dark: #303234;
            --primary-light: #5a5f61;
            --secondary-color: #05060E;
            --error-color: #ff3b30;
            --success-color: #34c759;
            --background-color: #f5f7fa;
            --card-color: #ffffff;
            --text-color: #000000;
            --secondary-text: #6B7280;
            --border-color: #e0e0e2;
            --box-shadow: 0 4px 12px rgba(5, 6, 14, 0.15);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(5, 6, 14, 0.05), transparent 70%);
            pointer-events: none;
            z-index: -1;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 40px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* Desktop styling for login container */
        @media (min-width: 640px) {
            .login-container {
                padding: 30px 32px;
                margin-top: 0;
                margin-bottom: 20px;
                max-width: 450px;
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 30px;
            box-shadow: 0 4px 15px rgba(5, 6, 14, 0.2);
        }
        
        .app-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .card-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 16px; /* Height reduced */
        }
        
        .form-label {
            display: block;
            margin-bottom: 6px; /* Height reduced */
            font-size: 14px; /* Font size reduced */
            font-weight: 500;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 11px 15px; /* Height reduced */
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 15px; /* Font size reduced */
            outline: none;
            transition: all 0.3s ease;
            background-color: transparent;
            color: var(--text-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }
        
        .form-control::placeholder {
            color: #a0a8b8;
            opacity: 0.75;
        }
        
        .input-icon-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-text);
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .input-icon-wrapper:focus-within .input-icon {
            color: var(--primary-color);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px 15px; /* Height reduced */
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 6px; /* Border radius reduced */
            font-size: 15px; /* Font size reduced */
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            /* Shadow removed */
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn:hover {
            /* No shadow */
        }
        
        .btn:active {
            /* No shadow */
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: translateX(-100%);
        }
        
        .btn:hover::before {
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-text);
            font-size: 15px;
        }
        
        .login-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .error-message {
            color: var(--error-color);
            background-color: rgba(255, 59, 48, 0.1);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        .success-message {
            color: #34c759;
            background-color: rgba(52, 199, 89, 0.1);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            border-left: 3px solid #34c759;
            font-weight: 600;
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .success-message.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }
        
        .success-message i {
            margin-right: 5px;
            color: #34c759;
            font-size: 16px;
        }
        
        /* Loading indicator */
        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            display: none;
            vertical-align: middle;
        }
        
        .btn.loading .loading-spinner {
            display: inline-block;
        }
        
        /* Success state for button */
        .btn.success {
            background: linear-gradient(135deg, #34c759, #32d190);
            box-shadow: 0 4px 12px rgba(52, 199, 89, 0.3);
            transform: translateY(-2px);
            transition: all 0.5s ease;
        }
        
        /* Dots loading animation for redirection */
        .dots-loading span {
            animation: dots 1.5s infinite;
            animation-fill-mode: both;
            font-size: 16px;
            opacity: 0;
            display: inline-block;
        }
        
        .dots-loading span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .dots-loading span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes dots {
            0% { opacity: 0; transform: translateY(0); }
            25% { opacity: 1; transform: translateY(-5px); }
            50% { opacity: 1; transform: translateY(0); }
            75% { opacity: 1; transform: translateY(5px); }
            100% { opacity: 0; transform: translateY(0); }
        }
        
        /* Fade out animation for container */
        .login-container.fade-out {
            opacity: 0;
            transform: scale(0.98) translateY(-10px);
            transition: all 0.3s ease;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-image" style="text-align: center; margin-bottom: 10px;">
                <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 70px; border-radius: 15px;">
            </div>
            <h1 class="app-name" style="color: var(--primary-color); font-weight: 800; font-size: 28px; margin-top: 5px; font-family: 'Nunito', sans-serif;">ChatMe</h1>
            <p style="color: var(--secondary-text); margin-top: 8px; font-size: 15px;">Connect and chat in real-time</p>
        </div>
        
        <h2 class="card-title" style="color: #333; font-weight: 700;">Sign In</h2>
        
        <div id="error-message" class="error-message">
            <i class="fas fa-exclamation-circle"></i> <span id="error-text"></span>
        </div>
        
        <div id="success-message" class="success-message">
            <i class="fas fa-check-circle"></i> <span id="success-text"></span>
        </div>
        
        <form id="login-form">
            <div class="form-group">
                <label class="form-label" for="login_id">Email or Mobile</label>
                <div class="input-icon-wrapper">
                    <input type="text" id="login_id" class="form-control" placeholder="Enter email or mobile number" autocomplete="username" required>
                    <div class="input-icon">
                        <i class="fas fa-at"></i>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrapper">
                    <input type="password" id="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                    <div class="input-icon" id="toggle-password">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
            
            <button type="submit" id="login-button" class="btn">
                <div style="display: flex; align-items: center; justify-content: center; width: 100%;">
                    <span class="loading-spinner"></span>
                    <span class="btn-text">Log In</span>
                </div>
            </button>
        </form>
        
        <div class="login-footer">
            Don't have an account? <a href="register.php" class="login-link">Sign Up</a>
        </div>
    </div>
    
    <script src="js/auth.js"></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
