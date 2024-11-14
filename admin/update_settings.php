<?php
// Include database connection
include '../includes/rinseclean_lms.php'; 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $wash_fold_rate = htmlspecialchars(trim($_POST['wash_fold_rate']));
    $dry_cleaning_rate = htmlspecialchars(trim($_POST['dry_cleaning_rate']));
    $ironing_rate = htmlspecialchars(trim($_POST['ironing_rate']));
    $bedding_rate = htmlspecialchars(trim($_POST['bedding_rate']));
    $stain_removal_rate = htmlspecialchars(trim($_POST['stain_removal_rate']));
    $specialty_fabric_rate = htmlspecialchars(trim($_POST['specialty_fabric_rate']));
    $opening_time = htmlspecialchars(trim($_POST['opening_time']));
    $closing_time = htmlspecialchars(trim($_POST['closing_time']));

    // Validate required fields
    if (empty($wash_fold_rate) || empty($dry_cleaning_rate) || empty($ironing_rate) || empty($bedding_rate) || empty($stain_removal_rate) || empty($specialty_fabric_rate) || empty($opening_time) || empty($closing_time)) {
        die("Please fill in all required fields.");
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL update query for the rates
        $stmt = $conn->prepare("UPDATE rates SET
            wash_fold_rate = ?, 
            dry_cleaning_rate = ?, 
            ironing_rate = ?,
            bedding_rate = ?, 
            stain_removal_rate = ?, 
            specialty_fabric_rate = ?, 
            opening_time = ?, 
            closing_time = ? 
            WHERE id = 1"); // Assuming there's only one row in the rates table

        if (!$stmt) {
            throw new Exception("Prepare failed: " . htmlspecialchars($conn->error));
        }

        // Bind parameters for the update
        $stmt->bind_param("ddddssss", 
            $wash_fold_rate, 
            $dry_cleaning_rate, 
            $ironing_rate, 
            $bedding_rate, 
            $stain_removal_rate, 
            $specialty_fabric_rate, 
            $opening_time, 
            $closing_time
        );

        // Execute the statement and check for errors
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . htmlspecialchars($stmt->error));
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to the settings page with success message
        header("Location: settings.php?update=success");
        exit;

    } catch (Exception $e) {
        // Roll back the transaction if an error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to settings page if accessed directly
    header("Location: settings.php");
    exit;
}
?>
