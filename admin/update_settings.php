<?php
// Include database connection
include '../includes/rinseclean_lms.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prepare and bind parameters
    $wash_fold_rate = $_POST['wash_fold_rate'];
    $dry_cleaning_rate = $_POST['dry_cleaning_rate'];
    $ironing_rate = $_POST['ironing_rate'];
    $bedding_rate = $_POST['bedding_rate'];
    $stain_removal_rate = $_POST['stain_removal_rate'];
    $specialty_fabric_rate = $_POST['specialty_fabric_rate'];
    $sms_notifications = $_POST['sms_notifications'];
    $email_notifications = $_POST['email_notifications'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'];

    // Debug: Check if we are connected to the correct database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update query for the settings table
    $query = "UPDATE settings SET
        wash_fold_rate = ?, 
        dry_cleaning_rate = ?, 
        ironing_rate = ?,
        bedding_rate = ?, 
        stain_removal_rate = ?, 
        specialty_fabric_rate = ?,
        sms_notifications = ?, 
        email_notifications = ?,
        opening_time = ?, 
        closing_time = ? 
        WHERE id = 1"; // Assuming there's only one row to update

    // Debug: Output the query to check its correctness
    echo "Query: " . $query . "<br>";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("ddddssssss", 
        $wash_fold_rate, 
        $dry_cleaning_rate, 
        $ironing_rate, 
        $bedding_rate, 
        $stain_removal_rate, 
        $specialty_fabric_rate, 
        $sms_notifications, 
        $email_notifications, 
        $opening_time, 
        $closing_time);
    
    if ($stmt->execute()) {
        header("Location: settings.php?update=success");
        exit(); // It's a good practice to exit after a redirect
    } else {
        echo "Error updating settings: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $conn->close();
}

