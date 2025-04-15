<?php
session_start();
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Load users data
$users_file = '../data/users.json';
if (!file_exists($users_file)) {
    file_put_contents($users_file, json_encode(['users' => []]));
}
$users_data = json_decode(file_get_contents($users_file), true);

$current_user_id = $_SESSION['user_id'];

// Handle GET requests - retrieve users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Make sure users array exists
    $users = isset($users_data['users']) ? $users_data['users'] : [];
    
    // Check if current=true, to return only the current user info
    if (isset($_GET['current']) && $_GET['current'] === 'true') {
        // Find the current user
        $current_user = null;
        foreach ($users as $user) {
            if ($user['id'] == $current_user_id) {
                $current_user = $user;
                break;
            }
        }
        
        if ($current_user) {
            echo json_encode([
                'success' => true,
                'user' => $current_user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Current user not found'
            ]);
        }
        exit;
    }
    
    // Filter out current user from the list
    $filtered_users = array_filter($users, function($user) use ($current_user_id) {
        return $user['id'] != $current_user_id;
    });
    
    // Apply search filter if provided
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = strtolower(trim($_GET['search']));
        
        // First, get users that match by display_name (higher priority)
        $display_name_matches = array_filter($filtered_users, function($user) use ($search_term) {
            return isset($user['display_name']) && 
                   strpos(strtolower($user['display_name']), $search_term) !== false;
        });
        
        // Then, get users that match by username but not already in display_name matches
        $username_matches = array_filter($filtered_users, function($user) use ($search_term, $display_name_matches) {
            // Skip if this user is already matched by display_name
            foreach ($display_name_matches as $match) {
                if ($match['id'] === $user['id']) {
                    return false;
                }
            }
            
            return isset($user['username']) && 
                   strpos(strtolower($user['username']), $search_term) !== false;
        });
        
        // Combine results with display_name matches first, then username matches
        $filtered_users = array_merge($display_name_matches, $username_matches);
    }
    
    // Load messages data to find last message for each user
    $messages_file = '../data/messages.json';
    if (file_exists($messages_file)) {
        $messages_data = json_decode(file_get_contents($messages_file), true);
        $messages = isset($messages_data['messages']) ? $messages_data['messages'] : [];
        
        // Find the last message between current user and each other user
        foreach ($filtered_users as &$user) {
            $user_messages = array_filter($messages, function($message) use ($current_user_id, $user) {
                return ($message['sender_id'] == $current_user_id && $message['recipient_id'] == $user['id']) ||
                       ($message['sender_id'] == $user['id'] && $message['recipient_id'] == $current_user_id);
            });
            
            if (!empty($user_messages)) {
                usort($user_messages, function($a, $b) {
                    return $b['timestamp'] <=> $a['timestamp'];
                });
                
                // Get the last non-deleted message for preview
                $last_message = null;
                foreach ($user_messages as $msg) {
                    $is_deleted = $msg['deleted_for_everyone'] || 
                            ($msg['sender_id'] == $current_user_id && $msg['deleted_for_sender']) ||
                            ($msg['sender_id'] == $user['id'] && $msg['deleted_for_recipient']);
                    
                    if (!$is_deleted) {
                        $last_message = $msg;
                        break;
                    }
                }
                
                // If all messages are deleted, use the first one but mark it
                if ($last_message === null && !empty($user_messages)) {
                    $last_message = reset($user_messages);
                    $last_message['message'] = 'Message was deleted';
                }
                
                // Count unread messages
                $unread_count = 0;
                foreach ($user_messages as $msg) {
                    // Skip deleted messages for unread count
                    $is_deleted = $msg['deleted_for_everyone'] || 
                                ($msg['sender_id'] == $current_user_id && $msg['deleted_for_sender']) ||
                                ($msg['sender_id'] == $user['id'] && $msg['deleted_for_recipient']);
                    
                    // Only count unread messages that are not deleted
                    if ($msg['sender_id'] == $user['id'] && !$msg['read'] && !$is_deleted) {
                        $unread_count++;
                    }
                }
                
                $user['last_message'] = [
                    'text' => $last_message['message'],
                    'timestamp' => $last_message['timestamp'],
                    'is_sent' => $last_message['sender_id'] == $current_user_id,
                    'read' => $last_message['read']
                ];
                
                // Add unread count to user data
                $user['unread_count'] = $unread_count;
            }
            
            // Check if user has network connectivity
            // In a real app, we would check if user has been active recently
            // For now, we'll use a combination of the session status and a timestamp
            
            // Get the timestamp from the request - client sends this when network is available
            $timestamp = isset($_GET['timestamp']) ? intval($_GET['timestamp']) : 0;
            $current_time = time();
            
            // If the timestamp is within last 5 minutes, consider the user as online
            // Or if the user has a specific network_status in their data
            $user['online'] = (
                ($timestamp > 0 && ($current_time - $timestamp) < 300) || 
                (isset($user['network_status']) && $user['network_status'] === 'online')
            );
        }
        
        // Sort users with recent messages at the top
        usort($filtered_users, function($a, $b) {
            if (isset($a['last_message']) && isset($b['last_message'])) {
                return $b['last_message']['timestamp'] <=> $a['last_message']['timestamp'];
            } elseif (isset($a['last_message'])) {
                return -1;
            } elseif (isset($b['last_message'])) {
                return 1;
            } else {
                return strcasecmp($a['display_name'], $b['display_name']);
            }
        });
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'users' => array_values($filtered_users)
        ]
    ]);
    exit;
}

// Update user's online status (implemented for network connectivity)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'status') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['status'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Status is required'
        ]);
        exit;
    }
    
    $status = $input['status'];
    $network_type = isset($input['network_type']) ? $input['network_type'] : 'unknown';
    
    // Make sure users array exists
    if (!isset($users_data['users']) || !is_array($users_data['users'])) {
        $users_data['users'] = [];
    }
    
    // Check if current user exists
    $current_user_exists = false;
    
    // Find the current user in the users array and update their status
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $current_user_id) {
            $user['network_status'] = $status;
            $user['network_type'] = $network_type;
            $user['last_active'] = time();
            $current_user_exists = true;
            break;
        }
    }
    
    // If current user doesn't exist in the users array, add them
    if (!$current_user_exists) {
        // Get current user data from session
        $display_name = isset($_SESSION['display_name']) ? $_SESSION['display_name'] : 'User ' . $current_user_id;
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'user' . $current_user_id;
        $avatar_color = isset($_SESSION['avatar_color']) ? $_SESSION['avatar_color'] : '#' . dechex(rand(0x000000, 0xFFFFFF));
        
        // Create new user entry
        $new_user = [
            'id' => $current_user_id,
            'display_name' => $display_name,
            'username' => $username,
            'avatar_color' => $avatar_color,
            'network_status' => $status,
            'network_type' => $network_type,
            'last_active' => time(),
            'created_at' => time()
        ];
        
        // Add to users array
        $users_data['users'][] = $new_user;
    }
    
    // Save updated user data
    file_put_contents($users_file, json_encode($users_data, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'status' => $status,
            'network_type' => $network_type,
            'timestamp' => time()
        ]
    ]);
    exit;
}

// Handle unsupported methods
echo json_encode([
    'success' => false,
    'message' => 'Method not supported'
]);
?>
