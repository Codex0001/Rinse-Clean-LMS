<?php
// Include database connection
include '../includes/rinseclean_lms.php'; // Adjust the path as needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all necessary POST variables are set
    if (isset($_POST['staff_id'], $_POST['name'], $_POST['phone_number'], $_POST['address'], $_POST['username'], $_POST['pay_rate'])) {
        // Retrieve and sanitize form data
        $staff_id = htmlspecialchars(trim($_POST['staff_id']));
        $name = htmlspecialchars(trim($_POST['name']));
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));
        $address = htmlspecialchars(trim($_POST['address']));
        $username = htmlspecialchars(trim($_POST['username']));
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : ''; // Store password as plain text if provided
        $pay_rate = htmlspecialchars(trim($_POST['pay_rate'])); // Pay rate for the staff

        // Validate required fields
        if (empty($name) || empty($phone_number) || empty($username) || empty($pay_rate)) {
            echo "<script>alert('Please fill in all required fields.');</script>";
            exit;
        }

        // Prepare SQL statement to update staff details
        if (!empty($password)) {
            // If password is provided, update it as plain text
            $update_stmt = $conn->prepare(
                "UPDATE users 
                 SET name = ?, phone_number = ?, address = ?, username = ?, password = ?, pay_rate = ? 
                 WHERE id = ?"
            );
            $update_stmt->bind_param("ssssssi", $name, $phone_number, $address, $username, $password, $pay_rate, $staff_id);
        } else {
            // Update details without changing the password
            $update_stmt = $conn->prepare(
                "UPDATE users 
                 SET name = ?, phone_number = ?, address = ?, username = ?, pay_rate = ? 
                 WHERE id = ?"
            );
            $update_stmt->bind_param("ssssdi", $name, $phone_number, $address, $username, $pay_rate, $staff_id);
        }

        // Execute the statement and check if the update was successful
        if ($update_stmt->execute()) {
            // Success, redirect to staff.php
            echo "<script>alert('Staff updated successfully!'); window.location.href = 'staff.php';</script>";
        } else {
            echo "<script>alert('Error: " . $update_stmt->error . "');</script>";
        }

        // Close the statement
        $update_stmt->close();
    } else {
        echo "<script>alert('Missing required form data.');</script>";
    }
}

$conn->close();
?>
