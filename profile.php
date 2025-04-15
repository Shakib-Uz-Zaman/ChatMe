<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Function to adjust color brightness
function adjustColorBrightness($hex, $percent) {
    // Remove the # from the hex color
    $hex = ltrim($hex, '#');
    
    // Convert hex to rgb
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Increase each component (make brighter)
    $r = min(255, $r + $percent);
    $g = min(255, $g + $percent);
    $b = min(255, $b + $percent);
    
    // Convert back to hex
    return '#' . sprintf('%02x', $r) . sprintf('%02x', $g) . sprintf('%02x', $b);
}

// Check if user ID is provided
$profile_user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Load user data
$user_data = null;
$users_json = file_get_contents('data/users.json');
$users_data = json_decode($users_json, true);

// Check if 'users' key exists in the data
if (isset($users_data['users'])) {
    $users = $users_data['users'];
    
    foreach ($users as $user) {
        if ($user['id'] == $profile_user_id) {
            $user_data = $user;
            break;
        }
    }
}

// If user not found, redirect to messenger
if (!$user_data) {
    header("Location: home.php");
    exit;
}

// Get current user data
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_display_name = $_SESSION['display_name'];

// Default avatar color if not set
$avatar_color = isset($user_data['avatar_color']) ? $user_data['avatar_color'] : '#3b82f6';

// Calculate join date
$join_date = date('F Y', $user_data['created_at']);

// Get first letter of display name for avatar
$avatar_letter = strtoupper(substr($user_data['display_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatMe | Profile | <?php echo htmlspecialchars($user_data['display_name']); ?></title>
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
        
        /* Utility classes */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .flex {
            display: flex;
        }
        
        .flex-col {
            flex-direction: column;
        }
        
        .items-center {
            align-items: center;
        }
        
        .justify-between {
            justify-content: space-between;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-4 {
            gap: 1rem;
        }
        
        .gap-6 {
            gap: 1.5rem;
        }
        
        .mt-4 {
            margin-top: 1rem;
        }
        
        .mb-4 {
            margin-bottom: 1rem;
        }
        
        .my-4 {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        
        .p-4 {
            padding: 1rem;
        }
        
        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        
        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .text-sm {
            font-size: 0.875rem;
        }
        
        .text-lg {
            font-size: 1.125rem;
        }
        
        .text-xl {
            font-size: 1.25rem;
        }
        
        .text-2xl {
            font-size: 1.5rem;
        }
        
        .font-medium {
            font-weight: 500;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .font-bold {
            font-weight: 700;
        }
        
        .text-white {
            color: var(--white);
        }
        
        .text-dark {
            color: var(--dark);
        }
        
        .text-medium {
            color: var(--medium);
        }
        
        .text-light {
            color: var(--light);
        }
        
        .text-primary {
            color: var(--primary);
        }
        
        .text-secondary {
            color: var(--secondary);
        }
        
        .text-accent {
            color: var(--accent);
        }
        
        .text-danger {
            color: var(--danger);
        }
        
        /* About section styling */
        .about-content {
            min-height: 60px;
            white-space: pre-line;
            line-height: 1.5;
        }
        
        .about-placeholder {
            color: var(--light);
            font-style: italic;
        }
        
        #bio-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--lighter);
            border-radius: 10px;
            resize: vertical;
            min-height: 120px;
            outline: none;
            font-family: inherit;
            line-height: 1.5;
            transition: border-color 0.2s ease;
        }
        
        #bio-textarea:focus {
            border-color: var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(to right, #404547, #05060E);
            color: white;
            border: none;
        }
        
        .bg-white {
            background-color: var(--white);
        }
        
        .bg-primary {
            background-color: var(--primary);
        }
        
        .bg-secondary {
            background-color: var(--secondary);
        }
        
        .bg-accent {
            background-color: var(--accent);
        }
        
        .bg-lighter {
            background-color: var(--lighter);
        }
        
        .bg-lightest {
            background-color: var(--lightest);
        }
        
        .rounded {
            border-radius: var(--radius);
        }
        
        .rounded-md {
            border-radius: var(--radius-md);
        }
        
        .rounded-lg {
            border-radius: var(--radius-lg);
        }
        
        .rounded-xl {
            border-radius: var(--radius-xl);
        }
        
        .rounded-full {
            border-radius: var(--radius-full);
        }
        
        .shadow {
            box-shadow: var(--shadow);
        }
        
        .shadow-md {
            box-shadow: var(--shadow-md);
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
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .search-bar {
            position: relative;
            width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            padding-left: 2.5rem;
            border: 1px solid var(--lighter);
            border-radius: var(--radius-full);
            background: var(--lightest);
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            border-color: var(--primary-light);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light);
            pointer-events: none;
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
        
        .app-logo img {
            width: 32px;
            height: 32px;
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
        
        /* User menu */
        .user-menu {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--lighter);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .user-menu:hover {
            background: var(--lightest);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: <?php echo $avatar_color; ?>;
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
        
        /* Page content */
        .page-container {
            padding: 0;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .page-description {
            color: var(--medium);
        }
        
        /* Profile layout */
        .profile-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        /* Profile header */
        .profile-header {
            background: var(--white);
            border-radius: 0;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        
        .profile-cover {
            height: 140px;
            background: linear-gradient(135deg, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, 30); ?>);
            position: relative;
        }
        
        .cover-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        
        .profile-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }
        
        .profile-action-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: var(--radius-full);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
        }
        
        .profile-action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .profile-avatar-wrapper {
            position: absolute;
            bottom: -48px;
            left: 2rem;
            border: 4px solid white;
            border-radius: 50%;
        }
        
        .profile-avatar-large {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background-color: <?php echo $avatar_color; ?>;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
        }
        

        
        .profile-header-content {
            padding: 1rem 2rem 1.5rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }
        
        .profile-header-info {
            padding-left: 7.5rem;
        }
        
        .profile-header-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .profile-header-username {
            color: var(--medium);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }
        
        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .profile-header-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #404547, #05060E);
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--medium);
            border: 1px solid var(--lighter);
        }
        
        .btn-outline:hover {
            background-color: var(--lightest);
            color: var(--dark);
            text-decoration: none;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            text-decoration: none;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 100;
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }
        
        .modal-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--lighter);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--medium);
            cursor: pointer;
        }
        
        .modal-body {
            padding: 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--lighter);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        
        .color-picker {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .avatar-color {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(255, 255, 255, 0.9);
            z-index: 1;
        }
        
        .avatar-color:hover {
            transform: translateY(-3px);
            border-color: white;
        }
        
        .avatar-color.selected {
            transform: scale(1.15);
            border: 3px solid white;
        }
        
        .avatar-color.selected::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            background-color: white;
            border-radius: 50%;
            opacity: 0.9;
        }
        
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
        
        .color-picker {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }
        
        /* Main profile content */
        .profile-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .content-card {
            background: var(--white);
            border-radius: 0;
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--lighter);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-title i {
            color: #000000;
        }
        
        .card-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .card-content {
            padding: 1.5rem;
        }
        
        /* About section */
        .about-content {
            color: var(--medium);
            line-height: 1.6;
            min-height: 60px;
            white-space: pre-line;
        }
        
        .about-placeholder {
            color: var(--light);
            font-style: italic;
        }
        
        #bio-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--lighter);
            border-radius: 10px;
            resize: vertical;
            min-height: 120px;
            outline: none;
            font-family: inherit;
            line-height: 1.5;
            transition: border-color 0.2s ease;
        }
        
        #bio-textarea:focus {
            border-color: var(--primary-color);
        }
        
        /* Footer */
        .app-footer {
            padding: 1rem;
            border-top: 1px solid var(--lighter);
            text-align: center;
            color: var(--light);
            font-size: 0.875rem;
            margin-top: 0.5rem;
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
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
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
            }
            
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Mobile styles */
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
            
            .search-bar {
                display: none;
            }
            
            .profile-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .profile-header-actions {
                width: 100%;
            }
            
            .page-container {
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .profile-header-info {
                padding-left: 0;
                margin-top: 3rem;
            }
            
            .profile-avatar-wrapper {
                left: 50%;
                transform: translateX(-50%);
            }
        }
        
        /* Toast message */
        .toast-message {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background-color: white;
            color: var(--dark);
            border-left: 4px solid var(--primary);
            border-radius: var(--radius);
            padding: 1rem;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            min-width: 300px;
            max-width: 400px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .toast-message.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .toast-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .toast-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .toast-close {
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            color: var(--light);
        }
        
        .toast-body {
            font-size: 0.875rem;
            color: var(--medium);
        }
        
        /* Overlay when sidebar is open on mobile */
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
                    <h3 class="nav-section-title">Main Menu</h3>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link <?php echo !isset($_GET['id']) ? 'active' : ''; ?>">
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
        
        <!-- Main content -->
        <div class="main-content">
            <!-- Navbar -->
            <div class="navbar">
                <?php if (isset($_GET['id'])): ?>
                <div class="placeholder-container" style="display: flex; align-items: center;">
                    <a href="home.php" style="text-decoration: none; color: var(--dark); font-size: 1.25rem; display: flex; align-items: center; padding: 0.25rem;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                
                <!-- For other users' profiles, title stays in center -->
                <div class="navbar-title" id="navbar-title" style="display: inline-flex; margin: 0; position: absolute; transform: translateX(-50%); left: 50%;">
                    <a href="javascript:location.reload();" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 24px; margin-right: 8px;">
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">Profile</span>
                    </a>
                </div>
                <?php else: ?>
                <!-- For own profile, title goes to the left -->
                <div class="navbar-title" id="navbar-title" style="display: inline-flex; margin: 0; position: static; transform: none; left: auto;">
                    <a href="javascript:location.reload();" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="https://i.imghippo.com/files/tYd2694rng.jpg" alt="App Logo" style="height: 24px; margin-right: 8px;">
                        <span style="font-weight: 900; font-family: 'Nunito', sans-serif;">Profile</span>
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="menu-container" style="display: flex; align-items: center; justify-content: flex-end;">
                    <button class="navbar-menu-button" id="navbar-menu-button" style="margin-left: 5px; -webkit-tap-highlight-color: transparent; outline: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Page content -->
            <div class="page-container">
                <!-- Profile header -->
                <div class="profile-header">
                    <div class="profile-cover">
                        <div class="cover-pattern"></div>
                        
                        <div class="profile-actions">
                            <?php if ($profile_user_id == $current_user_id): ?>
                            <button class="profile-action-btn" id="edit-profile-btn">
                                <i class="fas fa-pen"></i>
                            </button>
                            <?php else: ?>
                            <button class="profile-action-btn" id="share-profile-other-btn" title="Share Profile" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-avatar-wrapper">
                            <div class="profile-avatar-large"><?php echo $avatar_letter; ?></div>
                        </div>
                    </div>
                    
                    <div class="profile-header-content">
                        <div class="profile-header-info" style="text-align: center; width: 100%;">
                            <h1 class="profile-header-name" style="margin-left: auto; margin-right: auto;"><?php echo htmlspecialchars($user_data['display_name']); ?></h1>
                            <div class="profile-header-username" style="justify-content: center;">
                                <?php if (isset($user_data['email_or_mobile'])): ?>
                                    <?php echo htmlspecialchars($user_data['email_or_mobile']); ?>
                                <?php elseif (isset($user_data['username'])): ?>
                                    @<?php echo htmlspecialchars($user_data['username']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="profile-header-actions" style="width: 100%; justify-content: center; margin-top: 20px;">
                            <?php if ($profile_user_id != $current_user_id): ?>
                            <button class="btn" id="message-btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">
                                <i class="fas fa-comment"></i>
                                <span>Message</span>
                            </button>
                            <button class="btn" id="call-btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">
                                <i class="fas fa-phone"></i>
                                <span>Call</span>
                            </button>
                            <?php else: ?>
                            <button class="btn" id="share-profile-btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">
                                <i class="fas fa-share-alt"></i>
                                <span>Share Profile</span>
                            </button>
                            <button class="btn" id="logout-btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Profile layout -->
                <div class="profile-layout">
                    <!-- Main profile content -->
                    <div class="profile-content">
                        <!-- Personal Info section -->
                        <div class="content-card">
                            <div class="card-header">
                                <h2 class="card-title">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Personal Info</span>
                                </h2>
                            </div>
                            
                            <div class="card-content">
                                <div class="personal-info">
                                    <div class="info-item" style="display: flex; margin-bottom: 1rem;">
                                        <div class="info-label" style="width: 140px; font-weight: 600; color: var(--medium); white-space: nowrap;">
                                            <i class="fas fa-calendar-alt" style="margin-right: 0.5rem; color: #000000;"></i>
                                            Date of Birth:
                                        </div>
                                        <div class="info-value" style="flex: 1;">
                                            <?php if (isset($user_data['date_of_birth']) && !empty($user_data['date_of_birth'])): ?>
                                                <?php 
                                                    $date = new DateTime($user_data['date_of_birth']);
                                                    echo $date->format('F j, Y');
                                                ?>
                                            <?php else: ?>
                                                <span class="info-placeholder" style="color: var(--light); font-style: italic;">Not specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item" style="display: flex; margin-bottom: 1rem;">
                                        <div class="info-label" style="width: 140px; font-weight: 600; color: var(--medium); white-space: nowrap;">
                                            <i class="fas fa-user" style="margin-right: 0.5rem; color: #000000;"></i>
                                            Gender:
                                        </div>
                                        <div class="info-value" style="flex: 1;">
                                            <?php if (isset($user_data['gender']) && !empty($user_data['gender'])): ?>
                                                <?php echo ucfirst(htmlspecialchars($user_data['gender'])); ?>
                                            <?php else: ?>
                                                <span class="info-placeholder" style="color: var(--light); font-style: italic;">Not specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item" style="display: flex; margin-bottom: 1rem;">
                                        <div class="info-label" style="width: 140px; font-weight: 600; color: var(--medium); white-space: nowrap;">
                                            <i class="fas fa-clock" style="margin-right: 0.5rem; color: #000000;"></i>
                                            Joined:
                                        </div>
                                        <div class="info-value" style="flex: 1;">
                                            <?php echo $join_date; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- About section integrated inside Personal Info -->
                                    <div class="info-item" style="display: flex; flex-direction: column; margin-top: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                            <div class="info-label" style="font-weight: 600; color: var(--medium);">
                                                <i class="fas fa-info-circle" style="margin-right: 0.5rem; color: #000000;"></i>
                                                About
                                            </div>
                                            <?php if ($profile_user_id == $current_user_id): ?>
                                            <div>
                                                <button class="btn btn-outline" id="edit-about-btn" style="padding: 6px 12px; font-size: 0.875rem;">
                                                    <i class="fas fa-pen"></i>
                                                    <span>Edit</span>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="about-content" id="about-content" style="width: 100%; line-height: 1.4;">
                                            <?php if (isset($user_data['bio']) && !empty($user_data['bio'])): ?>
                                                <?php echo nl2br(htmlspecialchars($user_data['bio'])); ?>
                                            <?php else: ?>
                                                <p class="about-placeholder">This user hasn't added a bio yet.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="about-edit-form" id="about-edit-form" style="display: none; width: 100%;">
                                            <textarea id="bio-textarea" class="form-control" rows="4" placeholder="Write something about yourself..."><?php echo isset($user_data['bio']) ? htmlspecialchars($user_data['bio']) : ''; ?></textarea>
                                            <div class="form-actions" style="display: flex; gap: 10px; margin-top: 10px;">
                                                <button id="save-bio-btn" class="btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">Save</button>
                                                <button id="cancel-bio-btn" class="btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <footer class="app-footer">
                    <div class="footer-links">
                        <a href="about.php" class="footer-link">About</a>
                        <a href="privacy.php" class="footer-link">Privacy</a>
                        <a href="terms.php" class="footer-link">Terms</a>
                        <a href="help.php" class="footer-link">Help</a>
                    </div>
                    
                    <div class="footer-copyright">
                        &copy; <?php echo date('Y'); ?> <strong style="font-weight: 900; font-family: 'Nunito', sans-serif;">ChatMe</strong>. All rights reserved.
                    </div>
                </footer>
            </div>
        </div>
        
        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>
    
    <!-- Toast message -->
    <div class="toast-message" id="toast-message">
        <div class="toast-header">
            <div class="toast-title">
                <i class="fas fa-check-circle text-primary"></i>
                <span id="toast-title-text">Success</span>
            </div>
            <button class="toast-close" id="toast-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="toast-body" id="toast-body">
            Operation completed successfully.
        </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div class="modal" id="edit-profile-modal">
        <div class="modal-overlay" id="profile-modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Profile</h3>
                <button class="modal-close" id="close-profile-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-profile-form">
                    <div class="form-group">
                        <label for="edit-display-name">Display Name</label>
                        <input type="text" id="edit-display-name" class="form-input" value="<?php echo htmlspecialchars($user_data['display_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-gender">Gender</label>
                        <select id="edit-gender" class="form-input">
                            <option value="">Select</option>
                            <option value="male" <?php echo (isset($user_data['gender']) && $user_data['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($user_data['gender']) && $user_data['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-dob">Date of Birth</label>
                        <input type="date" id="edit-dob" class="form-input" value="<?php echo isset($user_data['date_of_birth']) ? $user_data['date_of_birth'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Avatar Color</label>
                        <div class="color-picker">
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
                        <input type="hidden" id="avatar-color" value="<?php echo $avatar_color; ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn" style="background: linear-gradient(to right, <?php echo $avatar_color; ?>, <?php echo adjustColorBrightness($avatar_color, -30); ?>); color: white;">Save Changes</button>
                        <button type="button" class="btn btn-outline" id="cancel-profile-edit">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
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
            }
            
            if (navbarMenuButton) {
                navbarMenuButton.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
            
            // Toast message functionality
            const toastMessage = document.getElementById('toast-message');
            const toastTitleText = document.getElementById('toast-title-text');
            const toastBody = document.getElementById('toast-body');
            const toastClose = document.getElementById('toast-close');
            
            function showToast(title, message, type = 'success') {
                // Set content
                toastTitleText.textContent = title;
                toastBody.textContent = message;
                
                // Set icon based on type
                const icon = toastTitleText.previousElementSibling;
                if (type === 'success') {
                    icon.className = 'fas fa-check-circle text-primary';
                } else if (type === 'error') {
                    icon.className = 'fas fa-exclamation-circle text-danger';
                } else if (type === 'warning') {
                    icon.className = 'fas fa-exclamation-triangle text-warning';
                } else if (type === 'info') {
                    icon.className = 'fas fa-info-circle text-accent';
                }
                
                // Show toast
                toastMessage.classList.add('show');
                
                // Hide toast after 3 seconds
                setTimeout(() => {
                    toastMessage.classList.remove('show');
                }, 3000);
            }
            
            // Close toast button
            if (toastClose) {
                toastClose.addEventListener('click', function() {
                    toastMessage.classList.remove('show');
                });
            }
            
            // Handle logout button
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    // Send logout request to server
                    fetch('api/auth.php?action=logout', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'login.php';
                        } else {
                            showToast('Error', 'Failed to log out. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Error', 'Network error. Please try again.', 'error');
                    });
                });
            }
            
            // Handle message button
            const messageBtn = document.getElementById('message-btn');
            if (messageBtn) {
                messageBtn.addEventListener('click', function() {
                    window.location.href = 'home.php?chat=<?php echo $profile_user_id; ?>';
                });
            }
            
            // Handle call button
            const callBtn = document.getElementById('call-btn');
            if (callBtn) {
                callBtn.addEventListener('click', function() {
                    showToast('Info', 'Calling feature coming soon!', 'info');
                });
            }
            
            // Handle edit profile functionality
            const editProfileBtn = document.getElementById('edit-profile-btn');
            const editProfileModal = document.getElementById('edit-profile-modal');
            const profileModalOverlay = document.getElementById('profile-modal-overlay');
            const closeProfileModal = document.getElementById('close-profile-modal');
            const cancelProfileEdit = document.getElementById('cancel-profile-edit');
            const editProfileForm = document.getElementById('edit-profile-form');
            const avatarColorInput = document.getElementById('avatar-color');
            const colorOptions = document.querySelectorAll('.color-option');
            
            // Avatar color functions are now handled in the new implementation
            
            // Initialize color picker
            const avatarColors = document.querySelectorAll('.avatar-color');
            const customColorPicker = document.getElementById('custom-color-picker');
            const customColorContainer = document.getElementById('custom-color-container');
            
            if (avatarColors.length > 0) {
                // Set initial selected color
                let selectedColorFound = false;
                
                avatarColors.forEach(color => {
                    if (color.dataset.color === avatarColorInput.value) {
                        color.classList.add('selected');
                        selectedColorFound = true;
                    } else {
                        color.classList.remove('selected');
                    }
                    
                    // Add click event to each color (skip the custom color container)
                    if (color.id !== 'custom-color-container') {
                        color.addEventListener('click', function() {
                            // Remove 'selected' class from all colors
                            avatarColors.forEach(c => c.classList.remove('selected'));
                            
                            // Add 'selected' class to clicked color
                            this.classList.add('selected');
                            
                            // Update the hidden input value
                            avatarColorInput.value = this.dataset.color;
                        });
                    }
                });
                
                // If the current color isn't one of the predefined colors, it must be a custom color
                if (!selectedColorFound && avatarColorInput.value) {
                    customColorContainer.classList.add('selected');
                    customColorPicker.value = avatarColorInput.value;
                }
            }
            
            // Handle custom color picker change
            if (customColorPicker) {
                customColorPicker.addEventListener('input', function(e) {
                    const selectedColor = e.target.value;
                    
                    // Remove selection from all
                    avatarColors.forEach(color => {
                        color.classList.remove('selected');
                    });
                    
                    // Add selection to custom color container
                    customColorContainer.classList.add('selected');
                    
                    // Update the hidden input value
                    avatarColorInput.value = selectedColor;
                });
            }
            
            // Show edit profile modal
            if (editProfileBtn) {
                editProfileBtn.addEventListener('click', function() {
                    editProfileModal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            // Close modal functions
            function closeProfileModalFunc() {
                editProfileModal.classList.remove('show');
                document.body.style.overflow = '';
            }
            
            if (profileModalOverlay) {
                profileModalOverlay.addEventListener('click', closeProfileModalFunc);
            }
            
            if (closeProfileModal) {
                closeProfileModal.addEventListener('click', closeProfileModalFunc);
            }
            
            if (cancelProfileEdit) {
                cancelProfileEdit.addEventListener('click', closeProfileModalFunc);
            }
            
            // Handle profile form submission
            if (editProfileForm) {
                editProfileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const displayName = document.getElementById('edit-display-name').value.trim();
                    const gender = document.getElementById('edit-gender').value;
                    const dateOfBirth = document.getElementById('edit-dob').value;
                    const avatarColor = avatarColorInput.value;
                    
                    // Validate form
                    if (!displayName) {
                        showToast('Error', 'Display name is required', 'error');
                        return;
                    }
                    
                    // Create profile data object
                    const profileData = {
                        display_name: displayName,
                        username: displayName.toLowerCase().replace(/\s+/g, '_'),
                        gender: gender,
                        date_of_birth: dateOfBirth,
                        avatar_color: avatarColor
                    };
                    
                    // Show loading state
                    const submitBtn = editProfileForm.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> Saving...';
                    
                    // Send update request to server
                    fetch('api/profile.php?action=update_profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(profileData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        if (data.success) {
                            // Close modal
                            closeProfileModalFunc();
                            
                            // Show success message
                            showToast('Success', 'Profile updated successfully!', 'success');
                            
                            // Update meta tag with new color value
                            const avatarColorMeta = document.querySelector('meta[name="user-avatar-color"]');
                            if (avatarColorMeta) {
                                avatarColorMeta.setAttribute('content', avatarColor);
                            }
                            
                            // If the user is in a chat, update the chat bubbles with the new color
                            if (window.opener) {
                                try {
                                    // If this page was opened from the chat, try to update the chat bubbles
                                    if (window.opener.document.querySelector('.message-sent')) {
                                        const sentMessages = window.opener.document.querySelectorAll('.message-sent');
                                        sentMessages.forEach(message => {
                                            message.style.backgroundColor = avatarColor;
                                            message.style.color = '#FFFFFF';
                                        });
                                    }
                                } catch (e) {
                                    console.log('Could not update chat bubbles in opener window:', e);
                                }
                            }
                            
                            // Reload page to show updated profile
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showToast('Error', data.message || 'Failed to update profile. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        showToast('Error', 'Network error. Please try again.', 'error');
                    });
                });
            }
            
            // Handle edit about button
            const editAboutBtn = document.getElementById('edit-about-btn');
            const aboutContent = document.getElementById('about-content');
            const aboutEditForm = document.getElementById('about-edit-form');
            const bioTextarea = document.getElementById('bio-textarea');
            const saveBioBtn = document.getElementById('save-bio-btn');
            const cancelBioBtn = document.getElementById('cancel-bio-btn');
            
            if (editAboutBtn) {
                editAboutBtn.addEventListener('click', function() {
                    // Show the edit form and hide the content
                    aboutContent.style.display = 'none';
                    aboutEditForm.style.display = 'block';
                    // Focus the textarea
                    bioTextarea.focus();
                });
            }
            
            if (cancelBioBtn) {
                cancelBioBtn.addEventListener('click', function() {
                    // Hide the edit form and show the content
                    aboutEditForm.style.display = 'none';
                    aboutContent.style.display = 'block';
                });
            }
            
            if (saveBioBtn) {
                saveBioBtn.addEventListener('click', function() {
                    const bio = bioTextarea.value.trim();
                    
                    // Disable the button to prevent multiple clicks
                    saveBioBtn.disabled = true;
                    saveBioBtn.innerHTML = '<span class="loading-spinner"></span> Saving...';
                    
                    // Send update request to server
                    fetch('api/profile.php?action=update_bio', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ bio: bio })
                    })
                    .then(response => response.json())
                    .then(data => {
                        saveBioBtn.disabled = false;
                        saveBioBtn.innerHTML = 'Save';
                        
                        if (data.success) {
                            // Update the displayed bio content
                            if (bio) {
                                aboutContent.innerHTML = bio.replace(/\n/g, '<br>');
                            } else {
                                aboutContent.innerHTML = '<p class="about-placeholder">This user hasn\'t added a bio yet.</p>';
                            }
                            
                            // Hide the edit form and show the content
                            aboutEditForm.style.display = 'none';
                            aboutContent.style.display = 'block';
                            
                            showToast('Success', 'Your bio has been updated!', 'success');
                        } else {
                            showToast('Error', data.message || 'Failed to update bio. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        saveBioBtn.disabled = false;
                        saveBioBtn.innerHTML = 'Save';
                        showToast('Error', 'Network error. Please try again.', 'error');
                    });
                });
            }
            
            // Function to handle sharing profile
            function handleProfileShare() {
                // Create the URL to share
                const profileUrl = window.location.origin + window.location.pathname + '?id=<?php echo $profile_user_id; ?>';
                
                // Try to use the Web Share API if available
                if (navigator.share) {
                    navigator.share({
                        title: '<?php echo htmlspecialchars($user_data['display_name']); ?>\'s Profile',
                        text: 'Check out <?php echo htmlspecialchars($user_data['display_name']); ?>\'s profile on ChatMe!',
                        url: profileUrl
                    })
                    .then(() => {
                        showToast('Success', 'Profile shared successfully!', 'success');
                    })
                    .catch((error) => {
                        // If user cancels sharing, do not show error (AbortError)
                        if (error.name === 'AbortError') {
                            console.log('Share operation was cancelled by the user');
                            return;
                        }
                        
                        console.error('Error sharing:', error);
                        // Fallback to clipboard for other errors
                        copyToClipboard(profileUrl);
                    });
                } else {
                    // Fallback to clipboard for browsers that don't support Web Share API
                    copyToClipboard(profileUrl);
                }
            }
            
            // Handle share profile button
            const shareProfileBtn = document.getElementById('share-profile-btn');
            if (shareProfileBtn) {
                shareProfileBtn.addEventListener('click', handleProfileShare);
            }
            
            // Handle share profile button for other users' profiles
            const shareProfileOtherBtn = document.getElementById('share-profile-other-btn');
            if (shareProfileOtherBtn) {
                shareProfileOtherBtn.addEventListener('click', handleProfileShare);
            }
            
            // Function to copy text to clipboard
            function copyToClipboard(text) {
                // Create a temporary input element
                const input = document.createElement('input');
                input.style.position = 'fixed';
                input.style.opacity = 0;
                input.value = text;
                document.body.appendChild(input);
                
                // Select and copy the text
                input.select();
                input.setSelectionRange(0, 99999); // For mobile devices
                
                try {
                    document.execCommand('copy');
                    showToast('Success', 'Profile URL copied to clipboard!', 'success');
                } catch (err) {
                    console.error('Failed to copy:', err);
                    showToast('Error', 'Failed to copy URL. Please try again.', 'error');
                }
                
                // Remove the temporary input
                document.body.removeChild(input);
            }
            
            // User menu in sidebar
            const sidebarUserMenu = document.getElementById('sidebar-user-menu');
            if (sidebarUserMenu) {
                sidebarUserMenu.addEventListener('click', function() {
                    showToast('Info', 'User menu options coming soon!', 'info');
                });
            }
            
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
                            showToast('Error', 'Failed to log out. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Error', 'Network error. Please try again.', 'error');
                    });
                });
            }
        });
    </script>
</body>
</html>
