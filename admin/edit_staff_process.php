<?php
// Include database connection
include '../includes/rinseclean_lms.php'; // Adjust the path as needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $staff_id = htmlspecialchars(trim($_POST['staff_id']));
    $name = htmlspecialchars(trim($_POST['name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Store password as plain text

    // Validate required fields
    if (empty($name) || empty($phone_number) || empty($username)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }

    // Prepare SQL statement to update staff details
    $update_stmt = $conn->prepare("UPDATE staff SET name = ?, phone_number = ?, address = ?, username = ?, password = ? WHERE staff_id = ?");
    $update_stmt->bind_param("sssssi", $name, $phone_number, $address, $username, $password, $staff_id); // 6 parameters for 6 placeholders

    // Execute the statement and check if the update was successful
    if ($update_stmt->execute()) {
        echo "<script>alert('Staff updated successfully!'); window.location.href = 'staff.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_stmt->error . "');</script>";
    }

    // Close the statement
    $update_stmt->close();
}

$conn->close();
