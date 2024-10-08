<?php
session_start();
require '../includes/rinseclean_lms.php'; // Ensure to include your database connection file

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../public/login/login.php"); // Redirect to login page
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $reg_number = trim($_POST['reg_number']);
    $password = trim($_POST['password']); // No hashing applied
    $name = trim($_POST['name']); // Collecting staff name
    $department = trim($_POST['department']); // Collecting staff department
    $address = trim($_POST['address']); // Collecting staff address (optional)

    // Validate the input data
    if (empty($username) || empty($email) || empty($phone_number) || empty($reg_number) || empty($password) || empty($name) || empty($department)) {
        $_SESSION['error'] = "All fields except address are required.";
        header("Location: ../admin/add_staff.php");
        exit();
    }

    // Check if the username or email already exists in the users table
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        // Cleanup before redirecting
        $stmt->close();
        $conn->close();
        header("Location: ../admin/add_staff.php");
        exit();
    }

    // Insert the new staff into the users table
    $insert_query = "INSERT INTO users (username, password, email, phone_number, role) VALUES (?, ?, ?, ?, 'staff')";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ssss", $username, $password, $email, $phone_number);

    if ($insert_stmt->execute()) {
        // Get the last inserted user ID for the staff record
        $user_id = $insert_stmt->insert_id;

        // Insert additional staff information into the staff table
        $insert_staff_query = "INSERT INTO staff (reg_number, name, department, phone_number, address, created_at, user_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $insert_staff_stmt = $conn->prepare($insert_staff_query);
        $insert_staff_stmt->bind_param("sssssi", $reg_number, $name, $department, $phone_number, $address, $user_id);

        if ($insert_staff_stmt->execute()) {
            $_SESSION['success'] = "Staff member added successfully!";
            // Cleanup before redirecting
            $stmt->close();
            $insert_stmt->close();
            $insert_staff_stmt->close();
            $conn->close();
            header("Location: ../admin/staff_list.php"); // Redirect to staff list
            exit();
        } else {
            $_SESSION['error'] = "Failed to add staff details.";
            // Cleanup before redirecting
            $stmt->close();
            $insert_stmt->close();
            $insert_staff_stmt->close();
            $conn->close();
            header("Location: ../admin/add_staff.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        // Cleanup before redirecting
        $stmt->close();
        $insert_stmt->close();
        $conn->close();
        header("Location: ../admin/add_staff.php"); // Redirect back to add staff page
        exit();
    }
}

// Ensure cleanup is done if POST was not executed
if (isset($stmt)) $stmt->close();
if (isset($insert_stmt)) $insert_stmt->close();
if (isset($insert_staff_stmt)) $insert_staff_stmt->close();
$conn->close();
