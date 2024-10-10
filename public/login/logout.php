<?php
// Start the session
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Optional: Log the logout action before destroying the session
    // logLogout($_SESSION['user_id']); // Example logging function

    // Clear session data
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: ../login/login.php"); // Ensure the path is correct
    exit();
} else {
    // If no session exists, redirect to login
    header("Location: /login.php"); // Ensure the path is correct
    exit();
}
