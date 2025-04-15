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
    <title>ChatMe | Terms of Service</title>
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
            max-width: 1000px;
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
        
        /* Main Content */
        .terms-section {
            padding: 3rem 0;
        }
        
        .terms-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .terms-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
            font-family: 'Nunito', sans-serif;
        }
        
        .terms-subtitle {
            font-size: 1.1rem;
            color: var(--medium);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .terms-content {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow);
            margin-bottom: 3rem;
        }
        
        .terms-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            margin-top: 2.5rem;
            color: var(--dark);
            border-bottom: 1px solid var(--lighter);
            padding-bottom: 0.5rem;
        }
        
        .terms-content h2:first-of-type {
            margin-top: 0;
        }
        
        .terms-content p {
            margin-bottom: 1.25rem;
            color: var(--medium);
            line-height: 1.7;
        }
        
        .terms-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .terms-content li {
            margin-bottom: 0.75rem;
            color: var(--medium);
            line-height: 1.6;
        }
        
        .terms-content li:last-child {
            margin-bottom: 0;
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
            .terms-content {
                padding: 2rem;
            }
            
            .terms-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .terms-content {
                padding: 1.5rem;
            }
            
            .terms-title {
                font-size: 1.75rem;
            }
            
            .terms-subtitle {
                font-size: 1rem;
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
                            <a href="terms.php" class="nav-link active">
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
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">Terms</span>
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
        <section class="terms-section">
            <div class="terms-header">
                <h1 class="terms-title">Terms of Service</h1>
                <p class="terms-subtitle">By using ChatMe, you agree to comply with the following terms and conditions.</p>
            </div>
            
            <div class="terms-content">
                <h2>1. Eligibility</h2>
                <ul>
                    <li>ChatMe is available to users of all ages</li>
                    <li>Parental supervision is advised for minors under 18 years of age</li>
                </ul>
                
                <h2>2. User Responsibilities</h2>
                <ul>
                    <li>Keep your account and password secure</li>
                    <li>Do not send abusive, violent, obscene, or illegal content</li>
                    <li>Do not impersonate others or misuse the platform</li>
                </ul>
                
                <h2>3. Our Rights</h2>
                <ul>
                    <li>We reserve the right to modify these terms at any time</li>
                    <li>We may temporarily or permanently suspend accounts that violate our policies</li>
                    <li>We strive to provide support and maintain a secure environment</li>
                </ul>
                
                <h2>4. Limitation of Liability</h2>
                <ul>
                    <li>ChatMe uses advanced technology for protection, but in case of system failures, data loss, or misuse, ChatMe will not be held responsible</li>
                    <li>Users are responsible for their own use of the platform</li>
                </ul>
            </div>
        </section>
    </main>
    
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
                &copy; <?php echo date('Y'); ?> <strong style="font-weight: 900; font-family: 'Nunito', sans-serif;">ChatMe</strong>. All rights reserved.
            </div>
        </div>
    </footer>
    
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
    </script>
</body>
</html>