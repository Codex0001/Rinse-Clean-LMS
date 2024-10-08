<?php
session_start();
require_once '../includes/rinseclean_lms.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = trim($_POST['password']); // No hashing applied

    // Validate the input data
    if (empty($username) || empty($email) || empty($phone_number) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../public/registration/registration.php");
        exit();
    }

    // Check if the username or email already exists
    $query = "SELECT * FROM customers WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        header("Location: ../public/registration/registration.php");
        exit();
    }

    // Insert the new customer into the database without hashing the password
    $insert_query = "INSERT INTO customers (username, password, email, phone_number) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ssss", $username, $password, $email, $phone_number);
    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: ../public/login/login.php"); // Redirect to login page
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: ../public/registration/registration.php"); // Redirect back to registration page
        exit();
    }
}

// Close the prepared statements and database connection
$stmt->close();
$insert_stmt->close();
$conn->close();
