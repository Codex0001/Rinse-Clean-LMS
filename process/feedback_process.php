<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $customer_id = $_SESSION['user_id']; // Get customer ID from session
    $order_id = $_POST['order_id']; // Hidden field in the form
    $nps_score = $_POST['nps_score'];
    $nps_reason = $_POST['nps_reason'];
    $cleanliness_rating = $_POST['cleanliness_rating'];
    $timeliness_rating = $_POST['timeliness_rating'];
    $customer_service_rating = $_POST['customer_service_rating'];
    $comments = $_POST['message'];

    // Prepare and execute the SQL insert statement
    $sql = "INSERT INTO feedback (customer_id, order_id, nps_score, nps_reason, cleanliness_rating, timeliness_rating, customer_service_rating, comments) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'iissiiis',
        $customer_id,
        $order_id,
        $nps_score,
        $nps_reason,
        $cleanliness_rating,
        $timeliness_rating,
        $customer_service_rating,
        $comments
    );

    if ($stmt->execute()) {
        $_SESSION['feedback_message'] = "Feedback submitted successfully!";
        $_SESSION['feedback_type'] = "success"; // for styling (e.g., green for success)
    } else {
        $_SESSION['feedback_message'] = "Error submitting feedback: " . $stmt->error;
        $_SESSION['feedback_type'] = "error"; // for styling (e.g., red for errors)
    }

    header("Location: ../customers/feedback.php");
    exit();
}
?>
