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
    <title>ChatMe | About Us</title>
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
        
        /* Main Content */
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
        
        .about-content {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow);
            margin-bottom: 4rem;
        }
        
        .about-content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .about-content p {
            margin-bottom: 1.5rem;
            color: var(--medium);
            line-height: 1.8;
        }
        
        .about-content p:last-child {
            margin-bottom: 0;
        }
        
        .cta-section {
            text-align: center;
            padding: 3rem;
            background: linear-gradient(135deg, #404547, #05060E);
            border-radius: var(--radius-lg);
            color: var(--white);
            margin-bottom: 4rem;
        }
        
        .cta-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .cta-subtitle {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .btn-cta {
            background: var(--white);
            color: #05060E;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-cta:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }
        
        /* Team section */
        .team-section {
            margin-bottom: 4rem;
        }
        
        .team-heading {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .team-member {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }
        
        .team-member:hover {
            transform: translateY(-5px);
        }
        
        .team-photo {
            height: 200px;
            background: linear-gradient(135deg, #404547, #05060E);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 3rem;
        }
        
        .team-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .team-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .team-role {
            color: var(--medium);
            margin-bottom: 1rem;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .social-link {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--lightest);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--medium);
            transition: all 0.2s;
        }
        
        .social-link:hover {
            background: var(--primary);
            color: var(--white);
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
            .about-content {
                padding: 2rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-section {
                padding: 2rem;
            }
            
            .team-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
        
        @media (max-width: 576px) {
            .about-title {
                font-size: 2rem;
            }
            
            .about-subtitle {
                font-size: 1rem;
            }
            
            .cta-title {
                font-size: 1.5rem;
            }
            
            .feature-card {
                padding: 1.5rem;
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
                            <a href="about.php" class="nav-link active">
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
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">About</span>
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
                <h1 class="about-title">About ChatMe</h1>
                <p class="about-subtitle">A fun, safe, and user-friendly messaging platform designed for users of all ages.</p>
            </div>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="feature-title">Interactive Communication</h3>
                    <p class="feature-description">Experience seamless, real-time messaging with intuitive chat interfaces and interactive elements.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3 class="feature-title">Rich Profiles</h3>
                    <p class="feature-description">Create and customize comprehensive user profiles with extended information management capabilities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Responsive Design</h3>
                    <p class="feature-description">Enjoy a consistent and intuitive experience across all devices with our adaptive and responsive design.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3 class="feature-title">Modern Styling</h3>
                    <p class="feature-description">Experience elegant visual design with gradient-based UI interactions and professional color schemes.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="feature-title">Multilingual Support</h3>
                    <p class="feature-description">Connect with users worldwide with our comprehensive multilingual capabilities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure & Reliable</h3>
                    <p class="feature-description">Your conversations are important - we've built ChatMe with security and reliability as core principles.</p>
                </div>
            </div>
            
            <div class="about-content">
                <h2>About ChatMe</h2>
                <p>
                    ChatMe is a fun, safe, and user-friendly messaging platform designed for users of all ages. Whether you're a child, teenager, adult, or senior, ChatMe allows you to connect and communicate easily in a secure digital environment.
                </p>
                
                <h3 style="font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem; color: var(--dark);">Our Mission:</h3>
                <ul style="list-style: none; padding-left: 0; margin-bottom: 2rem;">
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        To provide a communication space that is inclusive of all age groups
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        To ensure privacy and safety while maintaining real-time messaging features
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        To foster a positive, respectful, and creative digital experience for everyone
                    </li>
                </ul>
                
                <h3 style="font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem; color: var(--dark);">ChatMe Features:</h3>
                <ul style="list-style: none; padding-left: 0;">
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        Real-time messaging
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        End-to-End encryption
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        Customizable user profiles
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        Stickers, emojis, media sharing
                    </li>
                    <li style="margin-bottom: 0.75rem; position: relative; padding-left: 1.5rem;">
                        <span style="position: absolute; left: 0; color: var(--primary);">•</span>
                        Parental control settings for minors (if applicable)
                    </li>
                </ul>
            </div>
            
            <div class="cta-section">
                <h2 class="cta-title">Start Communicating Better Today</h2>
                <p class="cta-subtitle">Join thousands of users who've already discovered a better way to connect.</p>
                <a href="<?php echo $is_logged_in ? 'home.php' : 'login.php'; ?>" class="btn btn-cta">
                    <?php echo $is_logged_in ? 'Go to Messages' : 'Get Started'; ?>
                </a>
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