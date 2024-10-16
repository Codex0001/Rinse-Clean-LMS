<?php
// Include database connection
include '../includes/rinseclean_lms.php'; // Adjust the path as needed

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $reg_number = htmlspecialchars(trim($_POST['reg_number']));
    $name = htmlspecialchars(trim($_POST['name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Store password as plain text

    // Validate the required fields
    if (empty($reg_number) || empty($name) || empty($phone_number) || empty($username) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO staff (reg_number, name, phone_number, address, username, password) VALUES (?, ?, ?, ?, ?, ?)");

    // Adjust the number of parameters in bind_param to match the SQL statement
    $stmt->bind_param("ssssss", $reg_number, $name, $phone_number, $address, $username, $password);

    // Execute the statement and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect to staff management page with success message
        header("Location: staff_management.php?message=Staff added successfully!");
        exit;
    } else {
        // Handle error
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the staff management page if accessed directly
    header("Location: staff.php");
    exit;
}
