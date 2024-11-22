<?php
// Include database connection
include '../includes/rinseclean_lms.php'; 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $name = htmlspecialchars(trim($_POST['name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Store password as plain text for now
    $role = 'staff'; // Role for users table entry
    $payout = 0.00; // Default payout for new staff

    // If email is not provided, set a placeholder or default
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : 'no-email@example.com';

    // Validate required fields
    if (empty($name) || empty($phone_number) || empty($username) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Get the last reg_number from the staff table
        $result = $conn->query("SELECT reg_number FROM staff ORDER BY reg_number DESC LIMIT 1");
        $last_reg_number = $result->fetch_assoc();
        
        // Increment reg_number
        if ($last_reg_number) {
            // Extract the number part and increment it
            preg_match('/\d+/', $last_reg_number['reg_number'], $matches);
            $new_number = (int)$matches[0] + 1; // Increment the number
        } else {
            // If no staff exists, start from 000 (or whatever number you want)
            $new_number = 1;
        }

        // Generate the new reg_number
        $reg_number = 'RCML-EMP-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

        // Insert into staff table
        $stmt_staff = $conn->prepare("INSERT INTO staff (reg_number, name, phone_number, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt_staff->bind_param("sssss", $reg_number, $name, $phone_number, $username, $password);
        
        if (!$stmt_staff->execute()) {
            throw new Exception("Failed to insert into staff table: " . $stmt_staff->error);
        }

        // Insert into users table
        $stmt_users = $conn->prepare("INSERT INTO users (username, password, email, phone_number, role, reg_number, name, status, payout) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', ?)");
        $stmt_users->bind_param("sssssssd", $username, $password, $email, $phone_number, $role, $reg_number, $name, $payout);
        
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
