<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user ID is provided
$profile_user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Load user data
$user_data = null;
$users_json = file_get_contents('data/users.json');
$users = json_decode($users_json, true);

foreach ($users as $user) {
    if ($user['id'] == $profile_user_id) {
        $user_data = $user;
        break;
    }
}

// If user not found, redirect to messenger
if (!$user_data) {
    header("Location: messenger.php");
    exit;
}

// Get current user data
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_display_name = $_SESSION['display_name'];

// Default avatar color if not set
$avatar_color = isset($user_data['avatar_color']) ? $user_data['avatar_color'] : '#007aff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile: <?php echo htmlspecialchars($user_data['display_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        :root {
            --primary-color: #007aff;
            --background-color: #f2f2f7;
            --card-color: #ffffff;
            --text-color: #000000;
            --secondary-text: #8e8e93;
            --border-color: #e0e0e2;
            --danger-color: #ff3b30;
            --header-height: 44px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f7;
            color: var(--text-color);
        }
        
        .profile-container {
            max-width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header styles */
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 16px;
            height: var(--header-height);
            background-color: white;
            border-bottom: 1px solid var(--border-color);
        }
        
        .back-button {
            color: var(--primary-color);
            font-size: 16px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .back-button i {
            margin-right: 5px;
        }
        
        .header-title {
            font-size: 17px;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 20px;
        }
        
        .action-button {
            color: var(--primary-color);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        /* Profile content */
        .profile-content {
            flex: 1;
            padding: 20px 16px;
        }
        
        .profile-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            color: white;
            margin-right: 15px;
        }
        
        .profile-details {
            flex: 1;
        }
        
        .profile-name {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 5px;
        }
        
        .profile-username {
            font-size: 15px;
            color: var(--secondary-text);
            margin: 0;
        }
        
        .profile-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .profile-button {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .primary-button {
            background-color: var(--primary-color);
            color: white;
        }
        
        .secondary-button {
            background-color: #f2f2f7;
            color: var(--primary-color);
        }
        
        .button-icon {
            margin-right: 8px;
        }
        
        .section-title {
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 15px;
            color: var(--text-color);
        }
        
        .stat-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 5px;
        }
        
        .stat-label {
            font-size: 13px;
            color: var(--secondary-text);
        }
        
        .activity-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 36px;
            height: 36px;
            background-color: #f2f2f7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-size: 15px;
            font-weight: 500;
            margin: 0 0 4px;
        }
        
        .activity-time {
            font-size: 13px;
            color: var(--secondary-text);
            margin: 0;
        }
        
        .logout-button {
            background-color: var(--danger-color);
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            cursor: pointer;
        }
        
        /* Media queries */
        @media (min-width: 768px) {
            .profile-container {
                max-width: 500px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <a href="home.php" class="back-button">
                <i class="fas fa-chevron-left"></i>
                Back
            </a>
            <div class="header-title">Profile</div>
            <div class="header-actions">
                <?php if ($profile_user_id == $current_user_id): ?>
                <button class="action-button" id="edit-profile-button">
                    <i class="fas fa-pen"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-info">
                    <div class="profile-avatar" style="background-color: <?php echo $avatar_color; ?>">
                        <?php echo strtoupper(substr($user_data['display_name'], 0, 1)); ?>
                    </div>
                    <div class="profile-details">
                        <h1 class="profile-name"><?php echo htmlspecialchars($user_data['display_name']); ?></h1>
                        <p class="profile-username">@<?php echo htmlspecialchars($user_data['username']); ?></p>
                    </div>
                </div>
                
                <?php if ($profile_user_id != $current_user_id): ?>
                <div class="profile-actions">
                    <button class="profile-button primary-button" id="message-button">
                        <i class="fas fa-comment button-icon"></i>
                        Message
                    </button>
                    <button class="profile-button secondary-button" id="call-button">
                        <i class="fas fa-phone button-icon"></i>
                        Call
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Stats Card -->
            <div class="profile-card">
                <h2 class="section-title">Message Stats</h2>
                <div class="stat-group">
                    <div class="stat-item">
                        <div class="stat-value">
                            <?php
                            // Calculate stats
                            $messages_json = file_get_contents('data/messages.json');
                            $messages = json_decode($messages_json, true);
                            
                            $sent_count = 0;
                            $received_count = 0;
                            
                            foreach ($messages as $message) {
                                if ($message['sender_id'] == $profile_user_id) {
                                    $sent_count++;
                                } elseif ($message['recipient_id'] == $profile_user_id) {
                                    $received_count++;
                                }
                            }
                            
                            echo $sent_count;
                            ?>
                        </div>
                        <div class="stat-label">Sent</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $received_count; ?></div>
                        <div class="stat-label">Received</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo count($users) - 1; ?></div>
                        <div class="stat-label">Contacts</div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="profile-card">
                <h2 class="section-title">Recent Activity</h2>
                <ul class="activity-list">
                    <?php
                    // Sort messages by timestamp (newest first)
                    usort($messages, function($a, $b) {
                        return $b['timestamp'] - $a['timestamp'];
                    });
                    
                    // Get recent messages involving this user
                    $recent_activity = [];
                    $activity_count = 0;
                    
                    foreach ($messages as $message) {
                        if (($message['sender_id'] == $profile_user_id || $message['recipient_id'] == $profile_user_id) && $activity_count < 3) {
                            $activity_count++;
                            $recent_activity[] = $message;
                        }
                    }
                    
                    if (empty($recent_activity)) {
                        echo '<li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">No recent activity</div>
                                <div class="activity-time">Start chatting!</div>
                            </div>
                        </li>';
                    } else {
                        foreach ($recent_activity as $activity) {
                            $activity_time = date('M d, g:i A', $activity['timestamp']);
                            $is_sender = $activity['sender_id'] == $profile_user_id;
                            $other_user_id = $is_sender ? $activity['recipient_id'] : $activity['sender_id'];
                            
                            $other_user_name = 'User';
                            foreach ($users as $u) {
                                if ($u['id'] == $other_user_id) {
                                    $other_user_name = $u['display_name'];
                                    break;
                                }
                            }
                            
                            echo '<li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-' . ($is_sender ? 'paper-plane' : 'inbox') . '"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">' .
                                        ($is_sender ? 'Sent a message to ' : 'Received a message from ') .
                                        htmlspecialchars($other_user_name) .
                                    '</div>
                                    <div class="activity-time">' . $activity_time . '</div>
                                </div>
                            </li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            
            <?php if ($profile_user_id == $current_user_id): ?>
            <!-- Logout Button -->
            <button class="logout-button" id="logout-button">
                <i class="fas fa-sign-out-alt button-icon"></i>
                Log Out
            </button>
            <?php endif; ?>
        </div>
    </div>
