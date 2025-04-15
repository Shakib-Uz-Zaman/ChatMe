<?php
session_start();

// Redirect to messenger if logged in, otherwise to login page
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
