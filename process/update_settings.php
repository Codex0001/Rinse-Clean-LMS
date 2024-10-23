<?php
// Include your database connection
include '../includes/rinseclean_lms.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE rates SET 
        wash_fold_rate = ?, 
        ironing_rate = ?, 
        bedding_rate = ?, 
        dry_cleaning_rate = ?, 
        stain_removal_rate = ?, 
        specialty_fabric_rate = ?, 
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
    $stmt->bind_param("ddddddssss", 
        $_POST['wash_fold_rate'], 
        $_POST['ironing_rate'], 
        $_POST['bedding_rate'], 
        $_POST['dry_cleaning_rate'], 
        $_POST['stain_removal_rate'], 
        $_POST['specialty_fabric_rate'], 
        $_POST['sms_notifications'], 
        $_POST['email_notifications'], 
        $_POST['opening_time'], 
        $_POST['closing_time']
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to settings page with success message
        header('Location: ../admin/settings.php?update=success');
    } else {
        echo "Error updating settings: " . $stmt->error; // Show error if update fails
    }

    // Close the statement
    $stmt->close();
} else {
    echo "update_settings.php file reached."; 
}

// Close the database connection
$conn->close();
exit; // Stop execution here
