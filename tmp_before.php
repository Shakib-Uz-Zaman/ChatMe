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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home-custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <style>
        :root {
            --background-color: #ffffff;
            --item-background: #ffffff;
            --primary-text: #000000;
            --secondary-text: #8e8e93;
            --separator-color: #e0e0e2;
            --highlight-color: #4687FF;
            --badge-color: #ff3b30;
            --header-height: 44px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--primary-text);
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
        }
        
        .messages-container {
            max-width: 100%;
            height: 100vh;
            background-color: var(--background-color);
            display: flex;
            flex-direction: column;
        }
        
        /* Header styles */
        .messages-header {
            padding: 0 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--background-color);
            height: var(--header-height);
            border-bottom: 1px solid var(--separator-color);
        }
        
        .hamburger-menu {
            width: 20px;
            height: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
        }
        
        .hamburger-line {
            width: 100%;
            height: 2px;
            background-color: var(--primary-text);
            border-radius: 1px;
        }
        
        .messages-title {
            font-size: 18px;
            font-weight: 500;
            flex-grow: 1;
            text-align: center;
            margin: 0;
        }
        
        /* Search bar styles */
        .search-container {
            padding: 10px 16px;
            background-color: var(--background-color);
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #F5F5F5;
            border-radius: 16px;
            padding: 8px 12px;
        }
        
        .search-icon {
            color: #8e8e93;
            margin-right: 6px;
            font-size: 14px;
        }
        
        .search-input {
            border: none;
            background: transparent;
            font-size: 16px;
            outline: none;
            width: 100%;
            color: var(--primary-text);
        }
        
        /* Chat list styles */
        .chat-list {
            flex: 1;
            overflow-y: auto;
            background-color: var(--background-color);
        }
        
        .chat-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid var(--separator-color);
            background-color: var(--background-color);
            cursor: pointer;
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
        }
        
        .chat-info {
            flex: 1;
            min-width: 0; /* Important for text truncation */
        }
        
        .chat-name {
            font-weight: 500;
            font-size: 17px;
            margin-bottom: 4px;
            color: var(--primary-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-preview {
            font-size: 15px;
            color: var(--secondary-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 12px;
            min-width: 45px;
        }
        
        .chat-time {
            font-size: 13px;
            color: var(--secondary-text);
            margin-bottom: 4px;
        }
        
        .chat-unread {
            background-color: #ff3b30; font-weight: 400;
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
        
        /* Compose button styles */
        .compose-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--highlight-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 100;
            cursor: pointer;
        }
        
        .compose-icon {
            font-size: 24px;
        }
        
        /* Chat fullscreen styles */
        .chat-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--background-color);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        
        .fullscreen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 16px;
            height: var(--header-height);
            border-bottom: 1px solid var(--separator-color);
            background-color: var(--background-color);
        }
        
        .fullscreen-back {
            display: flex;
            align-items: center;
            color: var(--highlight-color);
            cursor: pointer;
        }
        
        .fullscreen-back-icon {
            margin-right: 5px;
            font-size: 16px;
        }
        
        .fullscreen-back-text {
            font-size: 16px;
        }
        
        .fullscreen-title {
            font-weight: 500;
            font-size: 17px;
        }
        
        .fullscreen-actions {
            display: flex;
            gap: 20px;
        }
        
        .action-icon {
            color: var(--highlight-color);
            font-size: 18px;
            cursor: pointer;
        }
        
        .fullscreen-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background-color: #f2f2f7;
        }
        
        /* Message styles */
        .message-row {
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 18px;
            margin-bottom: 2px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .message-sent {
            align-self: flex-end;
            background-color: var(--highlight-color);
            color: white;
            border-bottom-right-radius: 5px;
            margin-left: auto;
        }
        
        .message-received {
            align-self: flex-start;
            background-color: #e6e5eb;
            color: var(--primary-text);
            border-bottom-left-radius: 5px;
        }
        
        .message-time {
            font-size: 11px;
            color: var(--secondary-text);
            padding: 0 16px;
            margin-top: 3px;
        }
        
        /* Input area styles */
        .input-area {
            padding: 10px 16px;
            border-top: 1px solid var(--separator-color);
            background-color: var(--background-color);
            display: flex;
            align-items: center;
        }
        
        .message-input {
            flex: 1;
            border: 1px solid #e0e0e2;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 16px;
            outline: none;
        }
        
        .send-button {
            margin-left: 10px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--highlight-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .send-icon {
            font-size: 16px;
        }
        
        /* Empty state */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
            text-align: center;
            color: var(--secondary-text);
        }
        
        .empty-state-icon {
            font-size: 50px;
            margin-bottom: 15px;
            opacity: 0.5;
            color: var(--highlight-color);
        }
        
        .empty-state-text {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--primary-text);
        }
        
        .empty-state-subtext {
            font-size: 15px;
        }
        
        /* Loading spinner */
        .loading-spinner {
            width: 30px;
            height: 30px;
            margin: 20px auto;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top-color: var(--highlight-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
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
        @media (min-width: 768px) {
            .messages-container {
                max-width: 500px;
                margin: 0 auto;
                border-left: 1px solid var(--separator-color);
                border-right: 1px solid var(--separator-color);
            }
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <!-- Search Bar (at top as shown in the image) -->
        <div class="search-container">
            <div class="search-bar">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" id="user-search" class="search-input" placeholder="Search...">
            </div>
        </div>
        
        <!-- Chat List -->
        <div class="chat-list" id="chat-list">
            <!-- Chat items will be populated by JavaScript -->
            <div class="loading-spinner"></div>
        </div>

