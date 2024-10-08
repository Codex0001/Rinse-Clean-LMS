<?php
// Start the session
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Clear session data
    session_unset();
    session_destroy();

    // Optionally log the logout action (to a file, database, etc.)
    // logLogout($_SESSION['user_id']); // Example function for logging

    // Redirect to login page
    header("Location: ../login/login.php"); // Ensure the path is correct
    exit();
} else {
    // If no session exists, redirect to login
    header("Location: /login.php");
    exit();
}
