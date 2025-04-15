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

// Load messages data
$messages_file = '../data/messages.json';
if (!file_exists($messages_file)) {
    file_put_contents($messages_file, json_encode(['messages' => []]));
}
$messages_data = json_decode(file_get_contents($messages_file), true);

$current_user_id = $_SESSION['user_id'];

// Handle GET requests - retrieve messages
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Support both user_id and recipient_id for compatibility
    if (isset($_GET['user_id']) || isset($_GET['recipient_id'])) {
        // If user_id is set, use it, otherwise use recipient_id
        $recipient_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (int)$_GET['recipient_id'];
        
        // Make sure messages array exists
        $messages = isset($messages_data['messages']) ? $messages_data['messages'] : [];
        
        // Filter messages between current user and recipient
        $filtered_messages = array_filter($messages, function($message) use ($current_user_id, $recipient_id) {
            return ($message['sender_id'] == $current_user_id && $message['recipient_id'] == $recipient_id) ||
                   ($message['sender_id'] == $recipient_id && $message['recipient_id'] == $current_user_id);
        });
        
        // Sort messages by timestamp if there are any
        if (!empty($filtered_messages)) {
            usort($filtered_messages, function($a, $b) {
                return $a['timestamp'] <=> $b['timestamp'];
            });
        }
        
        echo json_encode([
            'success' => true,
            'messages' => array_values($filtered_messages)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User ID or Recipient ID is required'
        ]);
    }
    exit;
}

// Handle POST requests - send message, mark as read, or delete message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Check if this is a "mark as read" request
    if (isset($input['action']) && $input['action'] === 'mark_read' && isset($input['user_id'])) {
        $other_user_id = (int)$input['user_id'];
        
        // Find all unread messages from the other user to current user and mark them as read
        $updated = false;
        foreach ($messages_data['messages'] as &$msg) {
            if ($msg['sender_id'] == $other_user_id && 
                $msg['recipient_id'] == $current_user_id && 
                $msg['read'] == false) {
                
                $msg['read'] = true;
                $updated = true;
            }
        }
        
        if ($updated) {
            // Save updated data
            file_put_contents($messages_file, json_encode($messages_data, JSON_PRETTY_PRINT));
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'marked_read' => true
            ]
        ]);
        exit;
    }
    
    // Check if this is a "react to message" request
    if (isset($input['action']) && $input['action'] === 'react_message' && isset($input['message_id'])) {
        $message_id = (int)$input['message_id'];
        $reaction_type = isset($input['reaction_type']) ? $input['reaction_type'] : null;
        
        // Find the message by ID
        $message_found = false;
        $message_index = -1;
        
        foreach ($messages_data['messages'] as $index => $msg) {
            if ($msg['id'] == $message_id) {
                $message_found = true;
                $message_index = $index;
                break;
            }
        }
        
        if (!$message_found) {
            echo json_encode([
                'success' => false,
                'message' => 'Message not found'
            ]);
            exit;
        }
        
        // Check if reaction exists for this user
        $existing_reaction_index = -1;
        
        // Initialize reactions array if it doesn't exist
        if (!isset($messages_data['messages'][$message_index]['reactions'])) {
            $messages_data['messages'][$message_index]['reactions'] = [];
        }
        
        foreach ($messages_data['messages'][$message_index]['reactions'] as $rindex => $reaction) {
            if ($reaction['user_id'] == $current_user_id) {
                $existing_reaction_index = $rindex;
                break;
            }
        }
        
        // If reaction_type is null, remove the reaction
        if ($reaction_type === null) {
            if ($existing_reaction_index >= 0) {
                // Remove the reaction
                array_splice($messages_data['messages'][$message_index]['reactions'], $existing_reaction_index, 1);
            }
        } else {
            // Create or update the reaction
            $reaction = [
                'user_id' => $current_user_id,
                'type' => $reaction_type,
                'timestamp' => time()
            ];
            
            if ($existing_reaction_index >= 0) {
                // Update existing reaction
                $messages_data['messages'][$message_index]['reactions'][$existing_reaction_index] = $reaction;
            } else {
                // Add new reaction
                $messages_data['messages'][$message_index]['reactions'][] = $reaction;
            }
        }
        
        // Save updated data
        file_put_contents($messages_file, json_encode($messages_data, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'message_id' => $message_id,
                'reactions' => $messages_data['messages'][$message_index]['reactions']
            ]
        ]);
        exit;
    }
    
    // Check if this is a "delete message" request
    if (isset($input['action']) && $input['action'] === 'delete_message' && isset($input['message_id'])) {
        $message_id = (int)$input['message_id'];
        $delete_type = isset($input['delete_type']) ? $input['delete_type'] : 'for_you'; // default to 'for_you'
        
        // Find the message by ID
        $message_found = false;
        $message_index = -1;
        
        foreach ($messages_data['messages'] as $index => $msg) {
            if ($msg['id'] == $message_id) {
                $message_found = true;
                $message_index = $index;
                break;
            }
        }
        
        if (!$message_found) {
            echo json_encode([
                'success' => false,
                'message' => 'Message not found'
            ]);
            exit;
        }
        
        // Check if user has permission to delete this message
        $message = $messages_data['messages'][$message_index];
        $is_sender = ($message['sender_id'] == $current_user_id);
        
        if ($delete_type === 'for_everyone' && !$is_sender) {
            echo json_encode([
                'success' => false,
                'message' => 'You can only delete messages for everyone if you sent them'
            ]);
            exit;
        }
        
        // Handle different deletion types
        if ($delete_type === 'for_everyone') {
            // Mark as deleted for everyone instead of completely removing
            $messages_data['messages'][$message_index]['deleted_for_everyone'] = true;
            $messages_data['messages'][$message_index]['deleted_by_user_id'] = $current_user_id;
            $messages_data['messages'][$message_index]['deletion_timestamp'] = time();
        } 
        else {
            // Delete for you - mark the message as deleted for this user
            if ($is_sender) {
                $messages_data['messages'][$message_index]['deleted_for_sender'] = true;
            } else {
                $messages_data['messages'][$message_index]['deleted_for_recipient'] = true;
            }
        }
        
        // Save updated data
        file_put_contents($messages_file, json_encode($messages_data, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'message_id' => $message_id,
                'delete_type' => $delete_type
            ]
        ]);
        exit;
    }
    
    // Regular message sending
    if (!isset($input['recipient_id']) || !isset($input['message'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Recipient ID and message are required'
        ]);
        exit;
    }
    
    $recipient_id = (int)$input['recipient_id'];
    $message_text = trim($input['message']);
    
    if (empty($message_text)) {
        echo json_encode([
            'success' => false,
            'message' => 'Message cannot be empty'
        ]);
        exit;
    }
    
    // Create new message
    $new_message = [
        'id' => count($messages_data['messages']) + 1,
        'sender_id' => $current_user_id,
        'recipient_id' => $recipient_id,
        'message' => $message_text,
        'timestamp' => time(),
        'read' => false,
        'deleted_for_sender' => false,
        'deleted_for_recipient' => false,
        'deleted_for_everyone' => false,
        'deleted_by_user_id' => null,
        'deletion_timestamp' => null,
        'reactions' => []
    ];
    
    // Add message to data
    $messages_data['messages'][] = $new_message;
    
    // Save updated data
    file_put_contents($messages_file, json_encode($messages_data, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'message' => $new_message
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
