<?php
session_start();

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// User data for avatar and menu - if logged in
if ($is_logged_in) {
    $current_user_id = $_SESSION['user_id'];
    $current_username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $current_display_name = isset($_SESSION['display_name']) ? $_SESSION['display_name'] : '';
    
    // Default avatar color if not set
    $avatar_color = isset($_SESSION['avatar_color']) ? $_SESSION['avatar_color'] : '#3b82f6';
    
    // Get first letter of display name for avatar
    $avatar_letter = !empty($current_display_name) ? strtoupper(substr($current_display_name, 0, 1)) : 'U';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatMe | Help & Support Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tab image for browsers -->
    <link rel="icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="https://i.imghippo.com/files/tYd2694rng.jpg">
    <meta property="og:image" content="https://i.imghippo.com/files/tYd2694rng.jpg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: var(--dark);
            background: var(--body-bg);
        }
        
        a {
            color: var(--primary);
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header */
        .site-header {
            background: var(--white);
            border-bottom: 1px solid var(--lighter);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .app-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 900;
            font-size: 1.25rem;
            color: var(--dark);
            font-family: 'Nunito', sans-serif;
        }
        
        .app-logo span {
            background: linear-gradient(to right, #404547, #05060E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .nav-link {
            color: var(--medium);
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: var(--dark);
            text-decoration: none;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: none;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #404547, #05060E);
            color: var(--white);
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        
        /* About section */
        .about-section {
            padding: 4rem 0;
        }
        
        .about-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .about-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
            font-family: 'Nunito', sans-serif;
        }
        
        .about-subtitle {
            font-size: 1.25rem;
            color: var(--medium);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .feature-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #404547, #05060E);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--white);
            font-size: 1.5rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }
        
        .feature-description {
            color: var(--medium);
            line-height: 1.6;
        }

        .help-content {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow);
            margin-bottom: 4rem;
        }
        
        .help-section {
            margin-bottom: 2rem;
        }
        
        .help-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .faq-item {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--lighter);
            padding-bottom: 1.5rem;
        }
        
        .faq-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .faq-question {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .faq-answer {
            color: var(--medium);
            line-height: 1.7;
        }
        
        .contact-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .contact-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #404547, #05060E);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--white);
            font-size: 1.5rem;
        }
        
        .contact-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .contact-description {
            color: var(--medium);
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            text-align: center;
        }
        
        .btn-outline {
            border: 1px solid var(--lighter);
            background-color: transparent;
            color: var(--medium);
        }
        
        .btn-outline:hover {
            background-color: var(--lightest);
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #404547, #05060E);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        
        /* Footer */
        .app-footer {
            padding: 2rem 0;
            border-top: 1px solid var(--lighter);
            text-align: center;
            color: var(--light);
            font-size: 0.875rem;
            margin-top: 2rem;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .footer-link {
            color: var(--medium);
        }
        
        .footer-copyright {
            color: var(--light);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .help-content {
                padding: 1.5rem;
            }
            
            .help-title {
                font-size: 1.75rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .contact-options {
                grid-template-columns: 1fr;
            }
        }
        /* Layout */
        .app-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 240px;
            background: var(--white);
            border-left: 1px solid var(--lighter);
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            z-index: 10;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        
        .main-content {
            flex: 1;
            margin-right: 240px;
            transition: margin-right 0.3s ease;
        }
        
        /* Navbar */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--lighter);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 5;
            height: 56px;
            box-shadow: var(--shadow-sm);
        }
        
        .navbar-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            color: var(--medium);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem;
        }
        
        /* Sidebar content */
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
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--dark);
        }
        
        .app-logo span {
            color: var(--primary);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section {
            margin-bottom: 1.5rem;
        }
        
        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--light);
            letter-spacing: 0.05em;
        }
        
        .nav-list {
            list-style: none;
        }
        
        .nav-item {
            margin: 0.25rem 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1.5rem;
            color: var(--medium);
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-menu-button {
                display: block;
            }
            
            .sidebar {
                transform: translateX(100%);
                box-shadow: var(--shadow-lg);
                z-index: 30;
            }
            
            .sidebar-visible .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
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
                    <h3 class="nav-section-title">Main Menu</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link">
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
                    <h3 class="nav-section-title">Information</h3>
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
                            <a href="help.php" class="nav-link active">
                                <i class="fas fa-question-circle"></i>
                                <span>Help Center</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <h3 class="nav-section-title">Settings</h3>
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
        
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        
        <!-- Main content -->
        <div class="main-content">
            <!-- Navbar -->
            <div class="navbar">
                <div class="navbar-title" id="navbar-title" style="display: inline-flex; margin: 0; position: static; transform: none; left: auto;">
                    <a href="javascript:location.reload();" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 24px; margin-right: 8px;">
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">Help</span>
                    </a>
                </div>
                
                <div class="menu-container" style="display: flex; align-items: center; justify-content: flex-end;">
                    <button class="navbar-menu-button" id="navbar-menu-button" style="margin-left: 5px; -webkit-tap-highlight-color: transparent; outline: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="container">
        <section class="about-section">
            <div class="about-header">
                <h1 class="about-title">Help & Support</h1>
                <p class="about-subtitle">Find answers to common questions and learn how to get the most out of ChatMe.</p>
            </div>
            
            <div class="help-content">
                
                <div class="help-section">
                    <h2 class="section-title">Frequently Asked Questions & Feature Help</h2>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <h3 class="feature-title">How do I update my profile?</h3>
                            <p class="feature-description">You can update your profile information by visiting your profile page. Click on your avatar in the sidebar, then click the "Edit Profile" button. You can change your biography, display name, and other details.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3 class="feature-title">Can I change my avatar color?</h3>
                            <p class="feature-description">Yes, you can change your avatar color by going to your profile page and clicking the "Edit Profile" button. ChatMe offers various color options that will reflect your personality and make you stand out in chats.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <h3 class="feature-title">How do I start a conversation?</h3>
                            <p class="feature-description">To start a new conversation, go to your home page and find the user you want to chat with in the list. Click on their name to open a chat window. You can also search for users by typing their name in the search bar at the top.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <h3 class="feature-title">How does network status monitoring work?</h3>
                            <p class="feature-description">ChatMe automatically monitors your network status and allows you to send messages even when you're offline. When offline, you'll see a network indicator at the top, and when you're back online, your messages will automatically sync.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3 class="feature-title">How does fast navigation work?</h3>
                            <p class="feature-description">ChatMe's fast navigation feature preloads pages in advance. When you hover over or touch a link, the system loads that page in the background, so it can be displayed quickly when clicked, regardless of network conditions.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3 class="feature-title">Are my messages private and secure?</h3>
                            <p class="feature-description">Yes, your messages are completely private and secured with end-to-end encryption. Only you and the recipient of your messages can view them. Even ChatMe administrators cannot access your private messages.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-language"></i>
                            </div>
                            <h3 class="feature-title">How do I use multilingual support?</h3>
                            <p class="feature-description">ChatMe supports messaging in any language. You can type in English, Bengali, or any other language. To change the interface language, go to profile settings, and select your preferred language in the "Language" section. Use a Unicode-enabled keyboard to easily type in any language.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <h3 class="feature-title">What are the message deletion options?</h3>
                            <p class="feature-description">Long-press on any message and you'll get deletion options. There are two options: (1) "Delete for me only" - where the other person can still see the message, and (2) "Delete for everyone" - where the message will be removed for both parties and will show "This message was deleted".</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h3 class="feature-title">Can I customize notifications and sounds?</h3>
                            <p class="feature-description">Yes, go to your profile settings and click on the "Notifications" section to customize notifications. You can turn notifications on/off for specific people, change notification sounds, and enable "Do Not Disturb" mode for specific times.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3 class="feature-title">How is the mobile experience?</h3>
                            <p class="feature-description">ChatMe is specially optimized for mobile devices. It features fullscreen chat mode, touch optimization, and large buttons that make usage on mobile easy. The UI automatically adjusts when the keyboard opens so you can see the chat while typing.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-sync"></i>
                            </div>
                            <h3 class="feature-title">How does cross-device syncing work?</h3>
                            <p class="feature-description">ChatMe keeps your messages and profile information synced on the server. Whenever you switch from one device to another, all your conversations and settings will automatically be there. You can easily continue a conversation started on your phone on your laptop, and vice versa.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-smile"></i>
                            </div>
                            <h3 class="feature-title">How do I add reactions to messages?</h3>
                            <p class="feature-description">Long-press on any message (or right-click on desktop). A reaction menu will appear where you can select from various emotions like 'haha', 'like', 'love', 'wow', 'sad', 'angry', etc. You cannot add multiple reactions to a single message, but you can change your previous reaction.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <h3 class="feature-title">How does link sharing work?</h3>
                            <p class="feature-description">ChatMe automatically detects URLs in your messages and converts them to clickable links. When sharing a link, the system will automatically show a preview of the link, including the site's title, image, and brief description. When sharing YouTube links, you'll see an inline preview.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="feature-title">How do I search messages?</h3>
                            <p class="feature-description">Inside any chat, click on the search icon at the top. Type keywords in the search bar, and all messages containing that keyword will be highlighted as you type. You can also filter by file type, media, links, etc. Your search history is saved so you can quickly redo common searches.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3 class="feature-title">What are read status and typing indicators?</h3>
                            <p class="feature-description">When someone has read your message, you'll see double ticks next to the message. Typing indicators (moving dots) will show when the other person is typing a message. You can disable these features in profile settings if you don't want others to know if you've read their messages.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-magic"></i>
                            </div>
                            <h3 class="feature-title">How do I use smart text formatting?</h3>
                            <p class="feature-description">ChatMe supports special formatting. Use *text* for bold, _text_ for italic, ~text~ for strikethrough. Use ``code`` to display code blocks. Text-to-emoji conversion is also supported - typing :) will automatically convert to 😊 emoji.</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-moon"></i>
                            </div>
                            <h3 class="feature-title">Can I use Dark Mode?</h3>
                            <p class="feature-description">Yes, ChatMe has a dark mode. To use it, go to your profile settings and toggle the "Dark Mode" switch in the "Appearance" section. You can set it to "Auto" to automatically activate light or dark mode according to your device's system setting. Dark mode reduces eye strain and saves battery.</p>
                        </div>
                    </div>
                </div>
                
                <div class="help-section">
                    <div class="about-header">
                        <h2 class="section-title">Need More Help?</h2>
                        <p class="about-subtitle">If you couldn't find the answer to your question, feel free to contact us through one of the following options:</p>
                    </div>
                    
                    <div class="contact-options">
                        <div class="contact-card">
                            <div class="feature-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3 class="feature-title">Email Support</h3>
                            <p class="feature-description">Send us an email and we'll get back to you within 24 hours. We're always ready to help with any questions or concerns.</p>
                            <a href="#" class="btn btn-outline" onclick="showFeatureNotAvailable('Email Support'); return false;">Contact Support</a>
                        </div>
                        
                        <div class="contact-card">
                            <div class="feature-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h3 class="feature-title">Knowledge Base</h3>
                            <p class="feature-description">Browse our detailed guides and tutorials for step-by-step instructions on using all the features of ChatMe.</p>
                            <a href="#" class="btn btn-outline" onclick="showFeatureNotAvailable('Knowledge Base Articles'); return false;">View Articles</a>
                        </div>
                        
                        <div class="contact-card">
                            <div class="feature-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3 class="feature-title">Live Chat</h3>
                            <p class="feature-description">Chat with our support team in real-time. Available Monday through Friday, 9 AM to 6 PM.</p>
                            <a href="#" class="btn btn-primary" onclick="showFeatureNotAvailable('Live Chat Support'); return false;">Start Chat</a>
                        </div>
                    </div>
                </div>
                

            </div>
        </section>
    </main>
    
    <!-- Feature not available notification -->
    <div class="notification" id="notification" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0, 0, 0, 0.8); color: white; padding: 12px 20px; border-radius: 4px; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.2); max-width: 90%;">
        <div id="notification-message"></div>
    </div>
    
    <script src="js/ripple-effect.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle
            const appLayout = document.getElementById('app-layout');
            const navbarMenuButton = document.getElementById('navbar-menu-button');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            function toggleSidebar() {
                appLayout.classList.toggle('sidebar-visible');
                console.log("Menu button clicked, sidebar toggled");
            }
            
            if (navbarMenuButton) {
                navbarMenuButton.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
            
            // Logout functionality
            const logoutLinks = document.querySelectorAll('.logout-link');
            logoutLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = 'logout.php';
                });
            });
        });
        
        // Function to show notification when a feature is not available
        function showFeatureNotAvailable(featureName) {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            
            notificationMessage.textContent = `${featureName} is not available yet. This feature is coming soon!`;
            
            notification.style.display = 'block';
            
            // Hide notification after 3 seconds
            setTimeout(function() {
                notification.style.display = 'none';
            }, 3000);
        }
    </script>
    
    <!-- Footer -->
    <footer class="app-footer">
        <div class="container">
            <div class="footer-links">
                <a href="about.php" class="footer-link">About</a>
                <a href="privacy.php" class="footer-link">Privacy</a>
                <a href="terms.php" class="footer-link">Terms</a>
                <a href="help.php" class="footer-link">Help</a>
            </div>
            
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> <strong style="font-weight: 900; font-family: 'Nunito', sans-serif;">ChatMe</strong>. All rights reserved
            </div>
        </div>
    </footer>
</body>
</html>