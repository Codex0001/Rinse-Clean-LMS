<?php
session_start();
require '../includes/rinseclean_lms.php'; // Ensure to include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check in the users table first (for admin and staff)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?"); // Check in users table
    $stmt->bind_param("s", $username); // "s" indicates the type is string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If user not found in users table, check customers table
    if (!$user) {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ?"); // Check in customers table
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    }

    // Check if user exists and password matches
    if ($user && $user['password'] === $password) {
        // Password matches, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Set role based on whether the user is from the users or customers table
        if (isset($user['role'])) {
            $_SESSION['role'] = $user['role'];
        } else {
            $_SESSION['role'] = 'customer'; // Default role for customers
        }

        // Redirect based on role
        switch ($_SESSION['role']) {
            case 'admin':
                header('Location: ../admin/dashboard.php');
                break;
            case 'staff':
                header('Location: ../staff/dashboard.php');
                break;
            case 'customer':
                header('Location: ../customers/dashboard.php');
                break;
            default:
                header('Location: ../index.php'); // Default redirection
                break;
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password"; // Set error message
        header("Location: ../public/login/login.php"); // Redirect to login page
        exit();
    }
}
