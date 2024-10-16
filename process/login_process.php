<?php
session_start();
require '../includes/rinseclean_lms.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare a single query to check both users and customers tables
    $stmt = $conn->prepare("
        SELECT id, username, password, role 
        FROM users 
        WHERE username = ?
        UNION ALL
        SELECT id, username, password, 'customer' AS role 
        FROM customers 
        WHERE username = ?
    ");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user/customer
    $user = $result->fetch_assoc();

    // Check if user/customer exists and password matches
    if ($user && $user['password'] === $password) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Set role from user/customer

        // Redirect based on role
        if ($user['role'] === 'admin' || $user['role'] === 'admin') {
            header('Location: ../admin/dashboard.php');
        } elseif ($user['role'] === 'staff') {
            header('Location: ../staff/dashboard.php');
        } elseif ($user['role'] === 'customer') {
            header('Location: ../customers/dashboard.php');
        } else {
            header('Location: ../index.php'); // Default redirection
        }
        exit();
    }

    // If we reach here, login failed
    $_SESSION['error'] = "Invalid username or password"; // Set error message
    header("Location: ../public/login/login.php"); // Redirect to login page
    exit();
}
