<?php
// Include database connection
include '../includes/rinseclean_lms.php';

if (isset($_POST['salary_id']) && isset($_POST['status'])) {
    $salaryId = $_POST['salary_id'];
    $status = $_POST['status'];

    // SQL query to update the salary status in the staff table
    $updateQuery = "UPDATE staff SET salary_status = ? WHERE reg_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ss', $status, $salaryId);

    if ($stmt->execute()) {
        // Redirect back to the salary management page after status update
        header("Location: salary.php?status=updated");
        exit();
    } else {
        echo "Error updating salary status.";
    }
}
?>
