<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$display_name = $_SESSION['display_name'];
$avatar_letter = strtoupper(substr($display_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="user-id" content="<?php echo $user_id; ?>">
    <meta name="user-avatar-color" content="<?php echo isset($_SESSION['avatar_color']) ? $_SESSION['avatar_color'] : '#3b82f6'; ?>">
    <title>ChatMe | Messages</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home-custom.css?v=1.8">
    <link rel="stylesheet" href="css/reset-chat-styles.css?v=1.0">
    <link rel="stylesheet" href="css/fullscreen-chat.css?v=1.1">
    <link rel="stylesheet" href="css/placeholder-fix.css?v=1.0">
    <link rel="stylesheet" href="css/unread.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&family=Lato:wght@400;500;700&family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&family=Atma:wght@400;500;600;700&family=Tiro+Bangla:wght@400;500&family=Kohinoor+Bangla:wght@400;500;700&family=Mukta:wght@300;400;500;600;700&family=Noto+Serif+Bengali:wght@400;500;600;700&family=Baloo+Da+2:wght@400;500;600;700&family=Noto+Serif:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <!-- Tab image for browsers -->
    <link rel="icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <meta property="og:image" content="https://i.imghippo.com/files/tYd2694rng.jpg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        /* Search Icon Button Hover Effect */
        .search-icon-button:hover i {
            color: var(--dark) !important;
        }
        
        /* Direct ripple effect styling in the head for immediate loading */
        .chat-item {
            position: relative !important;
            overflow: hidden !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 14px 16px !important;
            box-shadow: none !important;
            border: none !important;
            background-color: var(--background-color) !important;
        }
        
        .ripple {
            position: absolute;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
            z-index: 100;
        }
        
        @keyframes ripple-animation {
            0% {
                transform: scale(0);
                opacity: 0.8;
            }
            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }
        
        /* Override any conflicting styles */
        .search-icon {
            color: #a9adb5 !important;
        }
        
        .search-field:not(:placeholder-shown) ~ .search-icon,
        .search-field:focus ~ .search-icon,
        #user-search:not(:placeholder-shown) ~ .search-icon,
        #user-search:focus ~ .search-icon,
        .search-wrapper:focus-within .search-icon {
            display: none !important;
        }
        
        /* Only show the search clear button when the input has content */
        .search-clear {
            display: none !important;
        }
        
        .search-field:not(:placeholder-shown) ~ .search-clear,
        #user-search:not(:placeholder-shown) ~ .search-clear {
            display: flex !important;
        }
        
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #93c5fd;
            --secondary: #10b981;
            --accent: #8b5cf6;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --dark: #1f2937;
            --medium: #4b5563;
            --light: #9ca3af;
            --lighter: #e5e7eb;
            --lightest: #f3f4f6;
            --white: #ffffff;
            --body-bg: #f9fafb;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            
            --radius-sm: 0.125rem;
            --radius: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 1rem;
            --radius-full: 9999px;
            
            /* Original messenger variables */
            --background-color: #ffffff;
            --item-background: #ffffff;
            --primary-text: #000000;
            --secondary-text: #8e8e93;
            --separator-color: #e0e0e2;
            --highlight-color: #4687FF;
            --badge-color: #ff3b30;
            --online-color: #1ED97C;
            --header-height: 40px;
            --sidebar-width: 250px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: var(--dark);
            background: var(--body-bg);
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }
        
        /* Prevent zooming on input fields */
        input[type="text"],
        input[type="search"],
        input[type="email"],
        input[type="password"],
        textarea {
            font-size: 16px; /* Minimum font size to prevent iOS zoom */
            max-height: 100%; /* Prevent layout shifts */
        }
        
        /* Fix for paste and autofill background color */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px var(--lightest) inset !important;
            -webkit-text-fill-color: var(--dark) !important;
            transition: background-color 5000s ease-in-out 0s;
            background-color: transparent !important;
        }
        
        /* Special styling for search input */
        #search-input {
            background-color: transparent !important;
            -webkit-appearance: none;
            appearance: none;
        }
        
        #search-input:-webkit-autofill,
        #search-input:-webkit-autofill:hover, 
        #search-input:-webkit-autofill:focus,
        #search-input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px var(--lightest) inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* App layout */
        .app-layout {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }
        
        /* Sidebar styles */
        .sidebar {
            background-color: var(--white);
            border-left: 1px solid var(--lighter);
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: var(--sidebar-width);
            flex-shrink: 0;
            z-index: 30;
            transition: transform 0.3s ease;
            overflow-y: auto;
            position: fixed;
            top: 0;
            right: 0;
        }
        
        .main-content {
            flex: 1;
            margin-right: var(--sidebar-width);
            transition: margin-right 0.3s ease;
        }
        
        .sidebar-header {
            padding: 0 1.5rem;
            border-bottom: 1px solid var(--lighter);
            height: 56px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: var(--white);
        }
        
        .app-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.25rem;
            text-decoration: none;
        }
        
        .app-logo i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        
        .nav-section {
            margin-bottom: 1.5rem;
        }
        
        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--light);
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--medium);
            text-decoration: none;
            gap: 0.75rem;
            border-left: 2px solid transparent;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            color: var(--dark);
            background: var(--lightest);
            text-decoration: none;
        }
        
        .nav-link.active {
            color: #000000;
            background: rgba(0, 0, 0, 0.04);
            border-left-color: #000000;
            font-weight: 600;
        }
        
        .nav-link i {
            font-size: 1.25rem;
            width: 1.25rem;
            text-align: center;
        }
        
        /* User menu */
        .user-menu {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--lighter);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            /* cursor removed as per requirements */
            transition: background 0.2s;
        }
        
        .user-menu:hover {
            background: var(--lightest);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .user-info {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-status {
            font-size: 0.75rem;
            color: var(--light);
        }
        
        /* Main content */
        .main-content {
            flex: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--body-bg);
            overflow-y: hidden;
        }
        
        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.5rem;
            background-color: var(--white);
            border-bottom: 1px solid var(--lighter);
            height: 56px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 5;
            position: relative;
        }
        
        .navbar-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.25rem;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .menu-container {
            width: 40px;
            display: flex;
            align-items: center;
        }
        
        .placeholder-container {
            width: 40px;
        }
        
        .navbar-menu-button {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--medium);
            /* cursor removed as per requirements */
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--lightest);
            border-radius: var(--radius-full);
            padding: 0.5rem 1rem;
            width: 100%;
            max-width: 400px;
            margin: 0 1rem;
        }
        
        .search-icon {
            color: var(--medium);
            margin-right: 0.5rem;
        }
        
        .search-input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-size: 0.875rem;
            color: var(--dark);
        }
        
        .search-input::placeholder {
            color: var(--medium);
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .profile-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: var(--lightest);
            color: var(--medium);
            /* cursor removed as per requirements */
            transition: all 0.2s;
        }
        
        .profile-action-btn:hover {
            background-color: var(--lighter);
            color: var(--dark);
        }

        /* Messages container styles */
        .messages-container {
            width: 100%;
            height: 100%;
            background-color: var(--background-color);
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow-y: auto;
            padding: 0;
            margin: 0;
            border: none;
        }
        
        /* Chat list styles */
        .chat-list {
            flex: 1;
            overflow-y: auto;
            background-color: var(--background-color);
            padding: 8px 4px;
            border: none;
            margin: 0;
            box-shadow: none;
        }
        
        .chat-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            background-color: var(--background-color);
            margin: 0;
            border-radius: 0;
            /* transition removed as per requirements */
            border: none;
            box-shadow: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
            -webkit-tap-highlight-color: transparent;
        }
        
        @keyframes ripple-animation {
            0% {
                transform: scale(0);
                opacity: 0.4;
            }
            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }
        
        .chat-ripple {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.15);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            margin-top: -50px;
            margin-left: -50px;
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
            animation: ripple-animation 0.6s ease-out;
            z-index: 100;
        }
        
        .chat-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .chat-avatar-initial {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            /* Transform transition removed as per requirements */
        }
        
        /* Removed hover effect as per requirements */
        
        .chat-info {
            flex: 1;
            min-width: 0; /* Important for text truncation */
        }
        
        .chat-name {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--primary-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.2px;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        
        .chat-preview {
            font-size: 14px;
            line-height: 1.4;
            color: var(--secondary-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0.85;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        
        .chat-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 12px;
            min-width: 45px;
        }
        
        .chat-time {
            font-size: 12px;
            color: #8a9aa9;
            margin-bottom: 4px;
            font-weight: 500;
            opacity: 0.8;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        
        .chat-unread {
            background-color: #ff3b30; 
            font-weight: 400;
            color: white;
            font-size: 13px;
            font-weight: 500;
            min-width: 20px;
            height: 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }
        
        /* Compose button styles removed as per requirements */
        /* Date headers */
        .date-header {
            text-align: center;
            margin: 15px 0;
            position: relative;
        }
        
        .date-header-text {
            position: relative;
            z-index: 2;
            background-color: #f2f2f7;
            padding: 0 10px;
            font-size: 13px;
            color: var(--secondary-text);
            display: inline-block;
        }
        
        /* Media queries */
        @media (max-width: 768px) {
            /* For mobile devices */
            .app-layout {
                display: block;
            }
            
            .sidebar {
                position: fixed;
                transform: translateX(100%);
                box-shadow: var(--shadow-lg);
            }
            
            .sidebar-visible .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .navbar-menu-button {
                display: block;
            }
            
            /* Compose button reference removed */
        }

        /* Desktop specific styles */
        @media (min-width: 769px) {
            /* For desktop devices */
            .app-layout {
                display: flex;
            }
            
            .sidebar {
                position: fixed;
                right: 0;
                transform: none;
                display: flex;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
            
            .main-content {
                flex: 1;
            }
            
            .navbar-menu-button {
                display: none;
            }
        }
        
        /* Sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 20;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s;
        }
        
        .sidebar-visible .sidebar-overlay {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="app-layout" id="app-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="home.php" class="app-logo" style="justify-content: center; text-align: center; width: 100%; text-decoration: none;">
                    <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" class="logo-image" style="height: 32px; margin-right: 10px;">
                    <span style="color: black; font-weight: 900; font-size: 22px; font-family: 'Nunito', sans-serif;">ChatMe</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">MAIN MENU</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link active">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <h3 class="nav-section-title">INFORMATION</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="about.php" class="nav-link">
                                <i class="fas fa-info-circle"></i>
                                <span>About</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="privacy.php" class="nav-link">
                                <i class="fas fa-shield-alt"></i>
                                <span>Privacy</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="terms.php" class="nav-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Terms</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="help.php" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Help Center</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <h3 class="nav-section-title">SETTINGS</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link logout-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            

        </div>
        
        <!-- Main content -->
        <div class="main-content">
            <!-- Navbar -->
            <div class="navbar">
                <div class="navbar-title" id="navbar-title" style="display: inline-flex; margin: 0; position: static; transform: none; left: auto;">
                    <a href="javascript:location.reload();" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 24px; margin-right: 8px;">
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">Messages</span>
                    </a>
                </div>
                
                <!-- Search input (hidden by default) -->
                <div class="search-container" id="search-container" style="position: absolute; top: 8px; right: 5%; width: 45%; max-width: 600px; display: none;">
                    <div class="search-wrapper" style="background-color: var(--lightest); border-radius: 8px; padding: 8px 15px 8px 15px; display: flex; align-items: center; position: relative;">
                        <i class="fas fa-search" style="color: var(--medium); margin-right: 8px;"></i>
                        <input type="text" id="search-input" placeholder="Search user" style="border: none; background: none !important; outline: none; width: 100%; font-size: 14px; padding-right: 48px; -webkit-appearance: none; -moz-appearance: none; appearance: none; -webkit-text-fill-color: inherit; color: inherit;">
                        <div id="search-close" style="display: flex; align-items: center; justify-content: center; padding: 15px; cursor: pointer; z-index: 1000; position: absolute; right: 0; top: 0; bottom: 0; width: 48px; height: 100%; touch-action: manipulation; -webkit-tap-highlight-color: transparent; background-color: rgba(0,0,0,0.03); border-radius: 0 8px 8px 0; user-select: none;">
                            <i class="fas fa-times" style="color: var(--medium); font-size: 22px; pointer-events: none;"></i>
                        </div>
                    </div>
                </div>

                <div class="menu-container" style="display: flex; align-items: center; justify-content: flex-end;">
                    <!-- Search Icon Button -->
                    <div class="search-icon-button" id="search-icon-button" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; cursor: pointer; margin-right: 20px; position: relative; z-index: 90; touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                        <i class="fas fa-search" style="color: var(--medium); font-size: 1.25rem; pointer-events: none;"></i>
                    </div>
                    
                    <button class="navbar-menu-button" id="navbar-menu-button" style="margin-left: 0px; -webkit-tap-highlight-color: transparent; outline: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Messages content -->
            <div class="messages-container">
                <!-- Chat List -->
                <div class="chat-list" id="chat-list">
                    <!-- Chat items will be populated by JavaScript -->
                    <div class="loading-spinner"></div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>



    <script src="js/home.js"></script>
    <script src="js/fullscreen-chat.js?v=1.1"></script>
    <script src="js/ripple-effect.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle logout link in sidebar
            const logoutLink = document.querySelector('.logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Send logout request to server
                    fetch('api/auth.php?action=logout', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'login.php';
                        } else {
                            console.error('Logout failed');
                            alert('Failed to log out. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error. Please try again.');
                    });
                });
            }
            
            // Event handlers for search functionality
            const searchIconButton = document.getElementById('search-icon-button');
            const searchContainer = document.getElementById('search-container');
            const searchInput = document.getElementById('search-input');
            const searchClose = document.getElementById('search-close');
            const navbarTitle = document.getElementById('navbar-title');
            
            // Show search bar when search icon is clicked
            if (searchIconButton && searchContainer) {
                // Completely revised click handler with better protection against unwanted events
                searchIconButton.addEventListener('click', function(e) {
                    // Search icon clicked
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // If the button is marked as deactivated, completely ignore the click
                    if (this.getAttribute('data-deactivated') === 'true') {
                        // Search icon click ignored - button is deactivated
                        return false;
                    }
                    
                    // Set a flag to track if the search was just closed (to prevent immediate reopening)
                    const justClosed = window.searchJustClosed || false;
                    if (justClosed) {
                        // Console log removed: 'Search icon click ignored - search was just closed';
                        window.searchJustClosed = false;
                        return false;
                    }
                    
                    // Temporarily disable button interactions
                    this.style.pointerEvents = 'none';
                    
                    // Position search container based on screen size
                    if (window.innerWidth < 769) {
                        // Mobile layout - keep original position in center
                        searchContainer.style.right = 'auto';
                        searchContainer.style.left = '50%';
                        searchContainer.style.transform = 'translateX(-50%)';
                        searchContainer.style.width = '80%';
                        searchContainer.style.maxWidth = '400px';
                    } else {
                        // Desktop layout - position on right
                        searchContainer.style.right = '5%';
                        searchContainer.style.left = 'auto';
                        searchContainer.style.transform = 'none';
                        searchContainer.style.width = '45%'; // বড় প্রস্থ
                    }
                    
                    // Show search container
                    searchContainer.style.display = 'block';
                    
                    // Hide navbar title only on mobile devices
                    if (navbarTitle && window.innerWidth < 769) {
                        navbarTitle.style.opacity = '0';
                    }
                    
                    // Hide search icon button
                    searchIconButton.style.opacity = '0';
                    
                    // Focus on search input after a short delay
                    if (searchInput) {
                        setTimeout(() => {
                            searchInput.focus();
                        }, 100);
                    }
                    
                    return false;
                });
                
                // Add touchstart listener with immediate visual feedback
                searchIconButton.addEventListener('touchstart', function(e) {
                    this.style.backgroundColor = 'rgba(0,0,0,0.05)';
                });
                
                // Enhanced touchend handler with extra protection
                searchIconButton.addEventListener('touchend', function(e) {
                    this.style.backgroundColor = 'transparent';
                    
                    // Check if this touch event should be ignored (if search was just closed)
                    if (window.searchJustClosed || this.getAttribute('data-deactivated') === 'true') {
                        // Console log removed: 'Search touchend ignored - search was just closed or button is deactivated';
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }, { passive: false });
                
                // Enhanced touchcancel handler
                searchIconButton.addEventListener('touchcancel', function(e) {
                    this.style.backgroundColor = 'transparent';
                    
                    // Also check deactivated state here
                    if (this.getAttribute('data-deactivated') === 'true') {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }, { passive: false });
            }
            
            // Hide search bar when close icon is clicked - enhanced for desktop and mobile
            if (searchClose) {
                // Add direct click event handler with debugging (works better on desktop)
                searchClose.onclick = function(e) {
                    // Console log removed: "Search close button clicked";
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Provide visual feedback for desktop users
                    if (window.innerWidth >= 641) {
                        this.style.backgroundColor = 'rgba(0,0,0,0.08)';
                        setTimeout(() => {
                            this.style.backgroundColor = 'rgba(0,0,0,0.02)';
                        }, 150);
                    }
                    
                    closeSearchBar();
                    return false; // Prevent event bubbling
                };
                
                // Enhanced mobile touch handling with better events
                // Using touchstart with immediate action
                searchClose.addEventListener('touchstart', function(e) {
                    // Console log removed: "Search close touchstart triggered";
                    // Prevent any default behavior that might interfere
                    e.preventDefault();
                    // Style changes for visual feedback
                    this.style.backgroundColor = 'rgba(0,0,0,0.08)';
                }, { passive: false });
                
                // Using touchend for actual action
                searchClose.addEventListener('touchend', function(e) {
                    // Console log removed: "Search close touchend triggered";
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Reset the style
                    this.style.backgroundColor = 'rgba(0,0,0,0.03)';
                    
                    // Immediate action for mobile - no delay
                    closeSearchBar();
                    return false;
                }, { passive: false });
                
                // Extra handlers for better mobile experience
                searchClose.addEventListener('touchcancel', function(e) {
                    // Console log removed: "Search close touchcancel triggered";
                    // Reset the style
                    this.style.backgroundColor = 'rgba(0,0,0,0.03)';
                }, { passive: false });
                
                // Desktop-specific mousedown and mouseup handlers for better response
                // React on mousedown for immediate visual feedback (desktop specific)
                searchClose.addEventListener('mousedown', function(e) {
                    // Check if this is a desktop device
                    if (window.innerWidth >= 641) {
                        // Console log removed: "Search close mousedown triggered (desktop)";
                        this.style.backgroundColor = 'rgba(0,0,0,0.1)';
                    }
                });
                
                // Additional mouseup for hybrid devices and desktop
                searchClose.addEventListener('mouseup', function(e) {
                    // Console log removed: "Search close mouseup triggered";
                    // Reset desktop styles
                    if (window.innerWidth >= 641) {
                        this.style.backgroundColor = 'rgba(0,0,0,0.02)';
                    }
                    closeSearchBar();
                });
                
                // Handle mouse leave case for desktop
                searchClose.addEventListener('mouseleave', function(e) {
                    if (window.innerWidth >= 641) {
                        this.style.backgroundColor = 'rgba(0,0,0,0.02)';
                    }
                });
            }
            
            // Close search bar when clicking outside
            document.addEventListener('click', function(e) {
                if (searchContainer && 
                    !searchContainer.contains(e.target) && 
                    searchIconButton && 
                    !searchIconButton.contains(e.target)) {
                    closeSearchBar();
                }
            });
            
            // Close search bar function with better protection against reopening
            function closeSearchBar() {
                // Removed console log for production
                
                // Reset search input field
                if (searchInput) {
                    searchInput.value = '';
                }
                
                // Clear search results by calling loadUsers with empty string
                if (window.loadUsers) {
                    window.loadUsers('');
                }
                
                // Create a temporary invisible overlay to block events
                const protectiveOverlay = document.createElement('div');
                protectiveOverlay.id = 'protective-overlay';
                protectiveOverlay.style.position = 'fixed';
                protectiveOverlay.style.top = '0';
                protectiveOverlay.style.left = '0';
                protectiveOverlay.style.width = '100%';
                protectiveOverlay.style.height = '100%';
                protectiveOverlay.style.zIndex = '2000';
                protectiveOverlay.style.background = 'transparent';
                document.body.appendChild(protectiveOverlay);
                
                // Completely disable search icon button to prevent reopening
                if (searchIconButton) {
                    searchIconButton.style.pointerEvents = 'none';
                    searchIconButton.setAttribute('data-deactivated', 'true');
                }
                
                // Hide the search container immediately
                if (searchContainer) {
                    searchContainer.style.display = 'none';
                    // Console log removed: "Search container hidden entirely";
                }
                
                // Show navbar title again with small delay for better visual transition
                setTimeout(function() {
                    if (navbarTitle) {
                        navbarTitle.style.opacity = '1';
                    }
                    
                    // Show search icon button again with delay
                    if (searchIconButton) {
                        searchIconButton.style.opacity = '1';
                        // Console log removed: "Search icon shown with delay";
                    }
                }, 100);
                
                // Remove focus from the input
                if (searchInput) {
                    searchInput.blur();
                }
                
                // Set a global flag to indicate search was just closed
                window.searchJustClosed = true;
                
                // Remove the protective overlay and re-enable search icon after a sufficient delay
                setTimeout(function() {
                    if (document.body.contains(protectiveOverlay)) {
                        document.body.removeChild(protectiveOverlay);
                    }
                    
                    if (searchIconButton) {
                        searchIconButton.style.pointerEvents = 'auto';
                        searchIconButton.removeAttribute('data-deactivated');
                        // Console log removed: "Search icon fully reactivated";
                    }
                    
                    // Reset the justClosed flag after a longer delay
                    setTimeout(function() {
                        window.searchJustClosed = false;
                    }, 200);
                }, 500);
            }
            
            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    // Handle search functionality here
                    // Removed console log to avoid console clutter
                    
                    // Use the loadUsers function from home.js to search in real-time
                    if (typeof loadUsers === 'function') {
                        loadUsers(searchInput.value.trim());
                    }
                    
                    // If Enter key is pressed, trigger full search
                    if (e.key === 'Enter') {
                        // Stop form submission if in a form
                        e.preventDefault();
                        
                        // Focus back on the search input after search
                        searchInput.focus();
                        
                        // Call loadUsers with search term
                        if (typeof loadUsers === 'function') {
                            loadUsers(searchInput.value.trim());
                        }
                    }
                });
            }
            
            // Add window resize handler to update search container positioning
            window.addEventListener('resize', function() {
                if (searchContainer && searchContainer.style.display === 'block') {
                    // Update search container positioning based on screen size
                    if (window.innerWidth < 769) {
                        // Mobile layout - keep original position in center
                        searchContainer.style.right = 'auto';
                        searchContainer.style.left = '50%';
                        searchContainer.style.transform = 'translateX(-50%)';
                        searchContainer.style.width = '80%';
                        searchContainer.style.maxWidth = '400px';
                    } else {
                        // Desktop layout - position on right with longer width
                        searchContainer.style.right = '5%';
                        searchContainer.style.left = 'auto';
                        searchContainer.style.transform = 'none';
                        searchContainer.style.width = '45%'; // বড় প্রস্থ
                    }
                }
            });
        });
    </script>
</body>
</html>
