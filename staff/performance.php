<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

// Initialize performance metrics
$total_orders = 0;
$completed_orders = 0;
$in_progress_orders = 0;
$average_completion_time = 0;

// Fetch total orders
$total_orders_sql = "SELECT COUNT(*) AS total_orders FROM orders";
$result = $conn->query($total_orders_sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_orders = $row['total_orders'];
}

// Fetch completed orders
$completed_orders_sql = "SELECT COUNT(*) AS completed_orders FROM orders WHERE status = 'Completed'";
$result = $conn->query($completed_orders_sql);
if ($result) {
    $row = $result->fetch_assoc();
    $completed_orders = $row['completed_orders'];
}

// Fetch in-progress orders
$in_progress_orders_sql = "SELECT COUNT(*) AS in_progress_orders FROM orders WHERE status = 'In Progress'";
$result = $conn->query($in_progress_orders_sql);
if ($result) {
    $row = $result->fetch_assoc();
    $in_progress_orders = $row['in_progress_orders'];
}

// Fetch average completion time for completed orders
$average_time_sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, pickup_time, delivery_time)) AS average_time FROM orders WHERE status = 'Completed'";
$result = $conn->query($average_time_sql);
if ($result) {
    $row = $result->fetch_assoc();
    $average_completion_time = $row['average_time'] ?? 0;
}

// Convert average completion time from minutes to hours and minutes
$average_hours = floor($average_completion_time / 60);
$average_minutes = $average_completion_time % 60;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Performance</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../staff/css/style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../staff/public/sidebar.php'; ?>
</div>

<section class="home">
    <div class="container mt-5">
        <h1>Performance Metrics</h1>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text"><?php echo htmlspecialchars($total_orders); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed Orders</h5>
                        <p class="card-text"><?php echo htmlspecialchars($completed_orders); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">In Progress Orders</h5>
                        <p class="card-text"><?php echo htmlspecialchars($in_progress_orders); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Average Completion Time</h5>
                        <p class="card-text"><?php echo htmlspecialchars($average_hours) . ' hours ' . htmlspecialchars($average_minutes) . ' minutes'; ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
