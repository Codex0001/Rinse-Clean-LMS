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

// Insert clock-out record
$sql = "UPDATE staff_clock_in_out SET clock_out_time = ? WHERE staff_id = ? AND clock_out_time IS NULL ORDER BY clock_in_time DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $current_time, $staff_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Clock Out Successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error clocking out']);
}

$stmt->close();
?>
