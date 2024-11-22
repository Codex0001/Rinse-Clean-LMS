<?php
// Include database connection
include '../includes/rinseclean_lms.php';

if (isset($_GET['salary_id'])) {
    $salaryId = $_GET['salary_id'];

    // SQL query to delete the record from the staff table
    $deleteQuery = "DELETE FROM staff WHERE reg_number = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('s', $salaryId);

    if ($stmt->execute()) {
        // Redirect back to the salary management page after deletion
        header("Location: salary.php?status=deleted");
        exit();
    } else {
        echo "Error deleting salary record.";
    }
}
?>
