<?php
// Include database connection
include '../includes/rinseclean_lms.php'; 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $reg_number = htmlspecialchars(trim($_POST['reg_number']));
    $name = htmlspecialchars(trim($_POST['name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Store password as plain text for now
    $role = 'staff'; // Role for users table entry

    // Validate required fields
    if (empty($reg_number) || empty($name) || empty($phone_number) || empty($username) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into staff table
        $stmt_staff = $conn->prepare("INSERT INTO staff (reg_number, name, phone_number, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt_staff->bind_param("sssss", $reg_number, $name, $phone_number, $username, $password);
        
        if (!$stmt_staff->execute()) {
            throw new Exception("Failed to insert into staff table: " . $stmt_staff->error);
        }

        // Insert into users table
        $stmt_users = $conn->prepare("INSERT INTO users (username, password, email, phone_number, role, reg_number, name, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $email = ''; // Placeholder for email if not provided
        $stmt_users->bind_param("sssssss", $username, $password, $email, $phone_number, $role, $reg_number, $name);
        
        if (!$stmt_users->execute()) {
            throw new Exception("Failed to insert into users table: " . $stmt_users->error);
        }

        // Commit transaction if both inserts are successful
        $conn->commit();

        // Redirect to staff management page with success message
        header("Location: staff.php?message=Staff added successfully!");
        exit;

    } catch (Exception $e) {
        // Roll back the transaction if any insert fails
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close statements and connection
    $stmt_staff->close();
    $stmt_users->close();
    $conn->close();
} else {
    // Redirect to the staff management page if accessed directly
    header("Location: staff.php");
    exit;
}
?>
