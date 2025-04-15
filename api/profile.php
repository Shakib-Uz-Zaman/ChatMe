<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit;
}

// Get current user information
$user_id = $_SESSION['user_id'];

// Load users data
$users_json = file_get_contents('../data/users.json');
$users_data = json_decode($users_json, true);

// Handle update bio request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_bio') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['bio'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Bio content is required'
        ]);
        exit;
    }
    
    $bio = trim($input['bio']);
    
    // Update user's bio in the data
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $user_id) {
            $user['bio'] = $bio;
            break;
        }
    }
    
    // Save updated data back to the file
    if (file_put_contents('../data/users.json', json_encode($users_data, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Bio updated successfully',
            'data' => [
                'bio' => $bio
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update bio'
        ]);
    }
    
    exit;
}

// Handle update profile request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_profile') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['display_name']) || !isset($input['username'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Display name and username are required'
        ]);
        exit;
    }
    
    $display_name = trim($input['display_name']);
    $username = trim($input['username']);
    $gender = isset($input['gender']) ? trim($input['gender']) : '';
    $date_of_birth = isset($input['date_of_birth']) ? trim($input['date_of_birth']) : '';
    $avatar_color = isset($input['avatar_color']) ? trim($input['avatar_color']) : '#3b82f6';
    
    // Validate fields
    if (empty($display_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Display name cannot be empty'
        ]);
        exit;
    }
    
    if (empty($username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Username cannot be empty'
        ]);
        exit;
    }
    
    // Check if username is already taken by another user
    foreach ($users_data['users'] as $user) {
        if ($user['id'] != $user_id && isset($user['username']) && $user['username'] === $username) {
            echo json_encode([
                'success' => false,
                'message' => 'Username is already taken'
            ]);
            exit;
        }
    }
    
    // Update user's profile in the data
    $user_updated = false;
    foreach ($users_data['users'] as &$user) {
        if ($user['id'] == $user_id) {
            $user['display_name'] = $display_name;
            $user['username'] = $username;
            $user['gender'] = $gender;
            $user['date_of_birth'] = $date_of_birth;
            $user['avatar_color'] = $avatar_color;
            
            // Update session data
            $_SESSION['display_name'] = $display_name;
            $_SESSION['username'] = $username;
            
            $user_updated = true;
            break;
        }
    }
    
    if (!$user_updated) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    // Save updated data back to the file
    if (file_put_contents('../data/users.json', json_encode($users_data, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'display_name' => $display_name,
                'username' => $username,
                'gender' => $gender,
                'date_of_birth' => $date_of_birth,
                'avatar_color' => $avatar_color
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update profile'
        ]);
    }
    
    exit;
}

// Handle unsupported methods
echo json_encode([
    'success' => false,
    'message' => 'Method not supported'
]);
?>