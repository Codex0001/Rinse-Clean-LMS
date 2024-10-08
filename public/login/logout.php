<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: ../public/login/login.php"); // Ensure the path is correct
exit();
