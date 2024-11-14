<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

// Initialize message variable
$message = '';

// Fetch staff ID from session
$staff_id = $_SESSION['user_id'];

// Get total completed orders for this staff member
$total_orders_sql = "SELECT COUNT(*) AS total_orders, SUM(cost) AS total_earnings 
                     FROM orders 
                     WHERE staff_id = ? AND status = 'Completed'";
$total_orders_stmt = $conn->prepare($total_orders_sql);
$total_orders_stmt->bind_param('i', $staff_id);
$total_orders_stmt->execute();
$total_orders_result = $total_orders_stmt->get_result();
$total_orders_data = $total_orders_result->fetch_assoc();
$total_orders = $total_orders_data['total_orders'];
$total_earnings = $total_orders_data['total_earnings'];

// Fetch recent orders assigned to this staff member
$recent_orders_sql = "SELECT * FROM orders 
                      WHERE staff_id = ? 
                      ORDER BY pickup_time DESC 
                      LIMIT 5";
$recent_orders_stmt = $conn->prepare($recent_orders_sql);
$recent_orders_stmt->bind_param('i', $staff_id);
$recent_orders_stmt->execute();
$recent_orders_result = $recent_orders_stmt->get_result();

// Store the recent orders in an array
$recent_orders = [];
while ($row = $recent_orders_result->fetch_assoc()) {
    $recent_orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Reports</title>
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
        <h1>Staff Reports</h1>

        <!-- Success/Error Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Total Orders and Earnings -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Completed Orders</h5>
                        <p class="card-text"><?php echo htmlspecialchars($total_orders); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Earnings</h5>
                        <p class="card-text">KSH <?php echo htmlspecialchars(number_format($total_earnings, 2)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table Section -->
        <div class="recent-orders-section mt-5">
            <h2 class="mb-4">Recent Orders</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No recent orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['pickup_time']); ?></td>
                            <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>KSH <?php echo htmlspecialchars(number_format($order['cost'], 2)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
