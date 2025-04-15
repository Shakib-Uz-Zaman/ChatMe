<?php
session_start();
header('Content-Type: application/json');

// Get action from query string
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different actions
switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'register':
        handleRegister();
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

/**
 * Handle user login
 */
function handleLogin() {
    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate request data
    if (!isset($data['login_id']) || !isset($data['password'])) {
        sendResponse(false, 'Email/mobile and password are required');
        return;
    }
    
    $loginId = $data['login_id'];
    $password = $data['password'];
    
    // Load users from JSON file
    $users = loadUsers();
    
    // Find user by email/mobile or username
    $user = null;
    foreach ($users as $u) {
        // Check if login ID matches username, email or mobile
        if (isset($u['email_or_mobile']) && strtolower($u['email_or_mobile']) === strtolower($loginId)) {
            $user = $u;
            break;
        } elseif (isset($u['username']) && strtolower($u['username']) === strtolower($loginId)) {
            $user = $u;
            break;
        }
    }
    
    // Check if user exists
    if (!$user) {
        sendResponse(false, 'Invalid email/mobile number or password');
        return;
    }
    
    // Check if password is hashed
    if (isset($user['password']) && substr($user['password'], 0, 4) === '$2y$') {
        // Verify password with password_verify for hashed passwords
        if (!password_verify($password, $user['password'])) {
            sendResponse(false, 'Invalid email/mobile number or password');
            return;
        }
    } else {
        // Plain text password comparison for non-hashed passwords
        if ($user['password'] !== $password) {
            sendResponse(false, 'Invalid email/mobile number or password');
            return;
        }
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    
    // Handle new and old user format
    if (isset($user['username'])) {
        $_SESSION['username'] = $user['username'];
    } elseif (isset($user['email_or_mobile'])) {
        $_SESSION['username'] = $user['email_or_mobile']; // Use email/mobile as username for new format users
    }
    
    $_SESSION['display_name'] = $user['display_name'];
    
    // Return success response with proper data
    $responseData = [
        'user_id' => $user['id'],
        'display_name' => $user['display_name']
    ];
    
    // Add additional fields based on user data format
    if (isset($user['username'])) {
        $responseData['username'] = $user['username'];
    }
    if (isset($user['email_or_mobile'])) {
        $responseData['email_or_mobile'] = $user['email_or_mobile'];
    }
    
    sendResponse(true, 'Login successful', $responseData);
}

/**
 * Handle user registration
 */
function handleRegister() {
    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate request data
    if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email_or_mobile']) || !isset($data['password'])) {
        sendResponse(false, 'First name, last name, email/mobile, and password are required');
        return;
    }
    
    $firstName = $data['first_name'];
    $lastName = $data['last_name'];
    $emailOrMobile = $data['email_or_mobile'];
    $displayName = isset($data['display_name']) ? $data['display_name'] : $firstName . ' ' . $lastName;
    $password = $data['password'];
    $avatarColor = isset($data['avatar_color']) ? $data['avatar_color'] : '#007aff';
    
    // Validate email/mobile format
    $isEmail = preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $emailOrMobile);
    $isMobile = preg_match('/^[0-9]{10,15}$/', $emailOrMobile);
    
    if (!$isEmail && !$isMobile) {
        sendResponse(false, 'Please enter a valid email address or mobile number');
        return;
    }
    
    // Validate password length
    if (strlen($password) < 8) {
        sendResponse(false, 'Password must be at least 8 characters long');
        return;
    }
    
    // Load users from JSON file
    $users = loadUsers();
    
    // Check if email/mobile already exists
    foreach ($users as $user) {
        if (isset($user['email_or_mobile']) && strtolower($user['email_or_mobile']) === strtolower($emailOrMobile)) {
            sendResponse(false, 'Email or mobile number already registered');
            return;
        }
    }
    
    // Generate new user ID
    $newId = 1;
    if (!empty($users)) {
        $lastUser = end($users);
        $newId = $lastUser['id'] + 1;
    }
    
    // Get date of birth and gender from request data
    $dateOfBirth = isset($data['date_of_birth']) ? $data['date_of_birth'] : '';
    $gender = isset($data['gender']) ? $data['gender'] : '';

    // Create new user
    $newUser = [
        'id' => $newId,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email_or_mobile' => $emailOrMobile,
        'display_name' => $displayName,
        'password' => $password,
        'avatar_color' => $avatarColor,
        'date_of_birth' => $dateOfBirth,
        'gender' => $gender,
        'created_at' => time()
    ];
    
    // Add user to array
    $users[] = $newUser;
    
    // Save users to JSON file
    if (!saveUsers($users)) {
        sendResponse(false, 'Failed to save user data');
        return;
    }
    
    // Return success response
    sendResponse(true, 'Registration successful', [
        'user_id' => $newId,
        'email_or_mobile' => $emailOrMobile,
        'display_name' => $displayName
    ]);
}

/**
 * Handle user logout
 */
function handleLogout() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
    
    // Return success response
    sendResponse(true, 'Logout successful');
}

/**
 * Load users from JSON file
 * @return array Users array
 */
function loadUsers() {
    $filePath = '../data/users.json';
    
    if (!file_exists($filePath)) {
        return [];
    }
    
    $fileContent = file_get_contents($filePath);
    $data = json_decode($fileContent, true) ?: [];
    
    // Check if users are in the "users" array or directly in the root
    if (isset($data['users']) && is_array($data['users'])) {
        return $data['users'];
    } elseif (is_array($data)) {
        // Clean up the data to only include numeric arrays
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value['id']) && (isset($value['username']) || isset($value['email_or_mobile']))) {
                $result[] = $value;
            }
        }
        return $result;
    }
    
    return [];
}

/**
 * Save users to JSON file
 * @param array $users Users array
 * @return bool Success status
 */
function saveUsers($users) {
    $filePath = '../data/users.json';
    $fileContent = json_encode(['users' => $users], JSON_PRETTY_PRINT);
    return file_put_contents($filePath, $fileContent) !== false;
}

/**
 * Send JSON response
 * @param bool $success Success status
 * @param string $message Response message
 * @param array $data Additional data (optional)
 */
function sendResponse($success, $message, $data = []) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if (!empty($data)) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}
