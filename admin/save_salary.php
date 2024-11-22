<?php
// Include database connection
include '../includes/rinseclean_lms.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $staff_id = $_POST['staff_id'];    // Make sure your form has an input with name="staff_id"
    $salary = $_POST['amount'];        // The amount is the salary
    $salary_status = $_POST['status']; // Status of the salary (Pending, Paid, Failed)
    $last_paid_date = $_POST['payout_date']; // Payout date

    // Validate inputs
    if (empty($staff_id) || empty($salary) || empty($salary_status) || empty($last_paid_date)) {
        // Redirect back with an error message
        header('Location: salary.php?error=All fields are required');
        exit;
    }

    // Ensure salary is a numeric value
    if (!is_numeric($salary)) {
        header('Location: salary.php?error=Salary must be a valid number');
        exit;
    }

    // Prepare an SQL statement to update the salary in the staff table
    $stmt = $conn->prepare("UPDATE staff SET salary = ?, salary_status = ?, last_paid_date = ? WHERE reg_number = ?");
    $stmt->bind_param("dsss", $salary, $salary_status, $last_paid_date, $staff_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back with a success message
        header('Location: salary.php?success=Salary updated successfully');
    } else {
        // Redirect back with an error message
        header('Location: salary.php?error=Failed to update salary');
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the form is not submitted, redirect to the salary management page
    header('Location: salary.php');
    exit;
}
