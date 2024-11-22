<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

require_once '../includes/rinseclean_lms.php'; 

$staff_id = $_SESSION['user_id'];
$current_time = date('Y-m-d H:i:s'); // Get current time

// Insert clock-in record
$sql = "INSERT INTO staff_clock_in_out (staff_id, clock_in_time) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $staff_id, $current_time);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Clock In Successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error clocking in']);
}

$stmt->close();
?>
