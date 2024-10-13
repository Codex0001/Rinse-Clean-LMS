<?php
// Include your database connection
include '../includes/rinseclean_lms.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE settings SET 
        wash_fold_rate = ?, 
        dry_cleaning_rate = ?, 
        sms_notifications = ?, 
        email_notifications = ?, 
        opening_time = ?, 
        closing_time = ? 
        WHERE id = 1");
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ddssss", 
        $_POST['wash_fold_rate'], 
        $_POST['dry_cleaning_rate'], 
        $_POST['sms_notifications'], 
        $_POST['email_notifications'], 
        $_POST['opening_time'], 
        $_POST['closing_time']
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to settings page with success message
        header('Location: settings.php?update=success');
    } else {
        echo "Error updating settings: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // This is for debugging, but should not be visible in production
    echo "update_settings.php file reached."; 
}

// Close the database connection
$conn->close();
exit; // Stop execution here

