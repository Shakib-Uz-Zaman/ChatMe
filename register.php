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
    <title>ChatMe | Register</title>
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
        
        .register-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 40px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* Desktop styling for register container */
        @media (min-width: 640px) {
            .register-container {
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
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-text);
            font-size: 15px;
        }
        
        .register-link {
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
        
        /* Avatar color selector */
        .avatar-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Gap reduced */
            margin-top: 12px; /* Margin reduced */
            justify-content: center;
            padding: 14px 15px; /* Padding reduced */
            background: linear-gradient(135deg, rgba(240, 243, 247, 0.9) 0%, rgba(255, 255, 255, 0.9) 100%);
            border-radius: 14px; /* Border radius reduced */
            /* Box shadow removed */
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            z-index: 1;
        }
        
        .avatar-selector::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 16px;
            z-index: -1;
        }
        
        .avatar-color {
            width: 38px; /* Size reduced */
            height: 38px; /* Size reduced */
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            /* Shadow removed */
            border: 2px solid rgba(255, 255, 255, 0.9); /* Border reduced */
            z-index: 1;
        }
        
        .avatar-color:hover {
            transform: translateY(-3px);
            /* Shadow removed */
            border-color: white;
        }
        
        .avatar-color.selected {
            transform: scale(1.15);
            border: 3px solid white;
            /* Shadow removed */
        }
        
        .avatar-color.selected::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            bottom: -5px;
            right: -5px;
            /* Shadow removed */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: var(--primary-color);
        }
        
        /* Custom color picker */
        .custom-color-picker-container {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
            border: 2px dashed rgba(0, 0, 0, 0.2);
        }
        
        #custom-color-picker {
            width: 28px;
            height: 28px;
            border: none;
            padding: 0;
            background: transparent;
            cursor: pointer;
        }
        
        #custom-color-picker::-webkit-color-swatch-wrapper {
            padding: 0;
        }
        
        #custom-color-picker::-webkit-color-swatch {
            border: none;
            border-radius: 50%;
        }
        
        /* Avatar preview */
        .avatar-preview-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px; /* Margin reduced */
            margin-top: 12px; /* Margin reduced */
            position: relative;
        }
        
        .avatar-preview {
            width: 70px; /* Size reduced */
            height: 70px; /* Size reduced */
            border-radius: 50%;
            background-color: #5664D2;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Shadow removed */
            border: 3px solid white; /* Border reduced */
            transition: all 0.3s ease;
            margin-bottom: 6px; /* Margin reduced */
            position: relative;
            overflow: hidden;
        }
        
        .avatar-preview::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0) 50%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .avatar-preview-letter {
            color: white;
            font-size: 30px; /* Font size reduced */
            font-weight: 800;
            text-transform: uppercase;
            user-select: none;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
        }
        
        .avatar-preview-text {
            font-size: 13px;
            color: var(--secondary-text);
            font-weight: 600;
            background-color: rgba(245, 247, 250, 0.7);
            padding: 4px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .avatar-preview.color-change {
            animation: colorPulse 0.5s ease;
        }
        
        @keyframes colorPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
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
        .register-container.fade-out {
            opacity: 0;
            transform: scale(0.98) translateY(-10px);
            transition: all 0.3s ease;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Multi-step form styles */
        .register-progress {
            margin-bottom: 20px;
        }
        
        .progress-bar {
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .progress-bar-inner {
            height: 100%;
            background: linear-gradient(to right, #404547, #05060E);
            width: 33.33%;
            transition: width 0.3s ease;
        }
        
        .step-indicators {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        
        .step-indicators::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e0e0e0;
            z-index: -1;
        }
        
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: #666;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }
        
        .step-indicator.active {
            background: linear-gradient(to right, #404547, #05060E);
            color: #fff;
        }
        
        .step-indicator.completed {
            background: linear-gradient(to right, #404547, #05060E);
            color: #fff;
        }
        
        .step-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            gap: 15px;
        }
        
        .btn-next, .btn-back {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            background: linear-gradient(to right, #404547, #05060E);
            color: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-back {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-next:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn-back:hover {
            background: #e9e9e9;
        }
        
        .btn-next i, .btn-back i {
            margin: 0 6px;
        }
        
        .register-step {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <div class="logo-image" style="text-align: center; margin-bottom: 10px;">
                <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 70px; border-radius: 15px;">
            </div>
            <h1 class="app-name" style="color: var(--primary-color); font-weight: 800; font-size: 28px; margin-top: 5px; font-family: 'Nunito', sans-serif;">ChatMe</h1>
            <p style="color: var(--secondary-text); margin-top: 8px; font-size: 15px;">Connect and chat in real-time</p>
        </div>
        
        <h2 class="card-title" style="color: #333; font-weight: 700;">Create Account</h2>
        
        <div id="error-message" class="error-message">
            <i class="fas fa-exclamation-circle"></i> <span id="error-text"></span>
        </div>
        
        <div id="success-message" class="success-message">
            <i class="fas fa-check-circle"></i> <span id="success-text"></span>
        </div>
        
        <form id="register-form">
            <!-- Progress bar -->
            <div class="register-progress">
                <div class="progress-bar">
                    <div class="progress-bar-inner" id="register-progress-bar"></div>
                </div>
                <div class="step-indicators">
                    <span class="step-indicator active" data-step="1">1</span>
                    <span class="step-indicator" data-step="2">2</span>
                    <span class="step-indicator" data-step="3">3</span>
                </div>
            </div>
            
            <!-- Step 1: Basic Information -->
            <div class="register-step" id="register-step-1">
                <h3 class="step-title">Basic Information</h3>
                
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <div class="input-icon-wrapper">
                        <input type="text" id="first_name" class="form-control" placeholder="Enter first name" required>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <div class="input-icon-wrapper">
                        <input type="text" id="last_name" class="form-control" placeholder="Enter last name" required>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email_or_mobile">Email or Mobile Number</label>
                    <div class="input-icon-wrapper">
                        <input type="text" id="email_or_mobile" class="form-control" placeholder="Enter email or mobile number" required>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-next" id="step-1-next">
                        <span class="btn-text">Next</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Personal Details -->
            <div class="register-step" id="register-step-2" style="display: none;">
                <h3 class="step-title">Personal Details</h3>
                
                <div class="form-group">
                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                    <div class="input-icon-wrapper">
                        <input type="date" id="date_of_birth" class="form-control" required>
                        <div class="input-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <div class="input-icon-wrapper">
                        <select id="gender" class="form-control" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <input type="password" id="password" class="form-control" placeholder="Create a password" autocomplete="new-password" required>
                        <div class="input-icon" id="toggle-password">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-back" id="step-2-back">
                        <i class="fas fa-arrow-left"></i>
                        <span class="btn-text">Back</span>
                    </button>
                    <button type="button" class="btn btn-next" id="step-2-next">
                        <span class="btn-text">Next</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Appearance -->
            <div class="register-step" id="register-step-3" style="display: none;">
                <h3 class="step-title">Choose Your Avatar</h3>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-palette" style="margin-right: 6px;"></i>Choose Avatar Color</label>
                    <div class="avatar-preview-container">
                        <div class="avatar-preview">
                            <div class="avatar-preview-letter">
                                <span>A</span>
                            </div>
                        </div>
                        <div class="avatar-preview-text">Preview</div>
                    </div>
                    <div class="avatar-selector" id="avatar-selector">
                        <div class="avatar-color selected" style="background-color: #5664D2;" data-color="#5664D2"></div>
                        <div class="avatar-color" style="background-color: #FF6B6B;" data-color="#FF6B6B"></div>
                        <div class="avatar-color" style="background-color: #1ED97C;" data-color="#1ED97C"></div>
                        <div class="avatar-color" style="background-color: #FF9F43;" data-color="#FF9F43"></div>
                        <div class="avatar-color" style="background-color: #6772E5;" data-color="#6772E5"></div>
                        <div class="avatar-color" style="background-color: #F368E0;" data-color="#F368E0"></div>
                        <div class="avatar-color" style="background-color: #0ABDE3;" data-color="#0ABDE3"></div>
                        <div class="avatar-color" style="background-color: #8854D0;" data-color="#8854D0"></div>
                        <div class="avatar-color" style="background-color: #20BF6B;" data-color="#20BF6B"></div>
                        <div class="avatar-color" style="background-color: #EB4D4B;" data-color="#EB4D4B"></div>
                        <div class="avatar-color custom-color-picker-container" id="custom-color-container">
                            <input type="color" id="custom-color-picker" value="#000000" title="Choose custom color">
                        </div>
                    </div>
                    <input type="hidden" id="avatar_color" value="#5664D2">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-back" id="step-3-back">
                        <i class="fas fa-arrow-left"></i>
                        <span class="btn-text">Back</span>
                    </button>
                    <button type="submit" id="register-button" class="btn">
                        <div style="display: flex; align-items: center; justify-content: center; width: 100%;">
                            <span class="loading-spinner"></span>
                            <span class="btn-text">Sign Up</span>
                        </div>
                    </button>
                </div>
            </div>
        </form>
        
        <div class="register-footer">
            Already have an account? <a href="login.php" class="register-link">Log In</a>
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
        
        // Avatar color selector
        const avatarSelector = document.getElementById('avatar-selector');
        const avatarColorInput = document.getElementById('avatar_color');
        const avatarPreview = document.querySelector('.avatar-preview');
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');
        const previewLetter = document.querySelector('.avatar-preview-letter span');
        
        // Update preview letter when first name or last name changes
        function updatePreviewLetter() {
            let letter = 'A';
            if (firstNameInput.value.trim()) {
                letter = firstNameInput.value.trim()[0].toUpperCase();
            }
            previewLetter.textContent = letter;
        }
        
        // Listen for input changes
        firstNameInput.addEventListener('input', updatePreviewLetter);
        
        // Custom color picker
        const customColorPicker = document.getElementById('custom-color-picker');
        const customColorContainer = document.getElementById('custom-color-container');
        
        // Handle custom color picker change
        customColorPicker.addEventListener('input', function(e) {
            const selectedColor = e.target.value;
            
            // Remove selection from all
            document.querySelectorAll('.avatar-color').forEach(color => {
                color.classList.remove('selected');
            });
            
            // Add selection to custom color container
            customColorContainer.classList.add('selected');
            customColorContainer.dataset.color = selectedColor;
            
            // Update hidden input
            avatarColorInput.value = selectedColor;
            
            // Update preview avatar color
            avatarPreview.style.backgroundColor = selectedColor;
            
            // Add animation effect
            avatarPreview.classList.add('color-change');
            setTimeout(() => {
                avatarPreview.classList.remove('color-change');
            }, 500);
        });
        
        // Avatar color selection for preset colors
        avatarSelector.addEventListener('click', function(e) {
            if (e.target.classList.contains('avatar-color') && e.target.id !== 'custom-color-container') {
                // Remove selection from all
                document.querySelectorAll('.avatar-color').forEach(color => {
                    color.classList.remove('selected');
                });
                
                // Add selection to clicked
                e.target.classList.add('selected');
                
                // Get the selected color
                const selectedColor = e.target.dataset.color;
                
                // Update hidden input
                avatarColorInput.value = selectedColor;
                
                // Update preview avatar color
                avatarPreview.style.backgroundColor = selectedColor;
                
                // Add animation effect
                avatarPreview.classList.add('color-change');
                setTimeout(() => {
                    avatarPreview.classList.remove('color-change');
                }, 500);
            }
        });
        
        // Initialize preview letter
        updatePreviewLetter();
        
        // Multi-step form logic
        const currentStep = 1;
        const progressBar = document.getElementById('register-progress-bar');
        const step1 = document.getElementById('register-step-1');
        const step2 = document.getElementById('register-step-2');
        const step3 = document.getElementById('register-step-3');
        
        // Step 1 Next button
        document.getElementById('step-1-next').addEventListener('click', function() {
            // Validate step 1 fields
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const emailOrMobile = document.getElementById('email_or_mobile').value.trim();
            
            if (!firstName || !lastName || !emailOrMobile) {
                // Show error message
                const errorMessage = document.getElementById('error-message');
                const errorText = document.getElementById('error-text');
                errorText.textContent = 'Please fill in all required fields';
                errorMessage.classList.add('show');
                return;
            }
            
            // Validate email/mobile format
            const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailOrMobile);
            const isMobile = /^[0-9]{10,15}$/.test(emailOrMobile);
            
            if (!isEmail && !isMobile) {
                // Show error message
                const errorMessage = document.getElementById('error-message');
                const errorText = document.getElementById('error-text');
                errorText.textContent = 'Please enter a valid email address or mobile number';
                errorMessage.classList.add('show');
                return;
            }
            
            // Hide error message
            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.remove('show');
            
            // Move to step 2
            step1.style.display = 'none';
            step2.style.display = 'block';
            
            // Update progress bar and indicators
            progressBar.style.width = '66.66%';
            document.querySelector('.step-indicator[data-step="1"]').classList.add('completed');
            document.querySelector('.step-indicator[data-step="2"]').classList.add('active');
        });
        
        // Step 2 Back button
        document.getElementById('step-2-back').addEventListener('click', function() {
            // Move back to step 1
            step2.style.display = 'none';
            step1.style.display = 'block';
            
            // Update progress bar and indicators
            progressBar.style.width = '33.33%';
            document.querySelector('.step-indicator[data-step="2"]').classList.remove('active');
        });
        
        // Step 2 Next button
        document.getElementById('step-2-next').addEventListener('click', function() {
            // Validate step 2 fields
            const dateOfBirth = document.getElementById('date_of_birth').value;
            const gender = document.getElementById('gender').value;
            const password = document.getElementById('password').value;
            
            if (!dateOfBirth || !gender || !password) {
                // Show error message
                const errorMessage = document.getElementById('error-message');
                const errorText = document.getElementById('error-text');
                errorText.textContent = 'Please fill in all required fields';
                errorMessage.classList.add('show');
                return;
            }
            
            // Validate password strength
            if (password.length < 8) {
                // Show error message
                const errorMessage = document.getElementById('error-message');
                const errorText = document.getElementById('error-text');
                errorText.textContent = 'Password must be at least 8 characters long';
                errorMessage.classList.add('show');
                return;
            }
            
            // Hide error message
            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.remove('show');
            
            // Move to step 3
            step2.style.display = 'none';
            step3.style.display = 'block';
            
            // Update progress bar and indicators
            progressBar.style.width = '100%';
            document.querySelector('.step-indicator[data-step="2"]').classList.add('completed');
            document.querySelector('.step-indicator[data-step="3"]').classList.add('active');
        });
        
        // Step 3 Back button
        document.getElementById('step-3-back').addEventListener('click', function() {
            // Move back to step 2
            step3.style.display = 'none';
            step2.style.display = 'block';
            
            // Update progress bar and indicators
            progressBar.style.width = '66.66%';
            document.querySelector('.step-indicator[data-step="3"]').classList.remove('active');
        });
    </script>
</body>
</html>
