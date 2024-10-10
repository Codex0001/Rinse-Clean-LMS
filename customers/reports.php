<?php
session_start();

// Ensure the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Fetch the customer name from the session
$customer_name = $_SESSION['username']; // Get the username from the session

// Database connection
require_once '../includes/rinseclean_lms.php'; 

// Fetch customer ID from the session
$customer_id = $_SESSION['user_id'];

// SQL query to fetch orders for the logged-in customer
$sql = "SELECT orders.id, orders.pickup_time AS date, orders.laundry_type AS service, orders.status, orders.total_kgs, orders.cost 
        FROM orders 
        WHERE orders.customer_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $customer_name); // Bind customer_name
$stmt->execute();
$result = $stmt->get_result();

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Check if there are any orders
if (empty($orders)) {
    $message = "No orders found for your account.";
} else {
    $message = ""; // Reset message if orders exist
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer | Reports</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Customers CSS -->
    <link rel="stylesheet" href="../customers/css/style.css"> 
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../customers/public/sidebar.php'; ?>
</div>

<section class="home">
    <div class="dashboard">
        <div class="header-strip">
            <h1>Order History, <?php echo htmlspecialchars($customer_name); ?>!</h1>
        </div>

        <!-- Filters Section (Optional) -->
        <div class="filter-section mb-4">
            <input type="text" id="search" placeholder="Search by order ID or service">
            <button type="button" id="filter-button">Filter</button>
        </div>

        <!-- Orders Table Section -->
        <div class="orders-section mt-5">
            <h2 class="mb-4">Your Orders</h2>
            <?php if ($message): ?>
                <div class="alert alert-warning"><?php echo $message; ?></div>
            <?php endif; ?>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Total Kgs</th>
                        <th scope="col">Cost</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                        <td><?php echo htmlspecialchars($order['service']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_kgs']); ?></td>
                        <td><?php echo htmlspecialchars($order['cost']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (Placeholder for now) -->
        <div class="pagination mt-4">
            <button class="btn btn-primary">Previous</button>
            <button class="btn btn-primary">Next</button>
        </div>

        <!-- Export Options -->
        <div class="export-options mt-4">
            <button id="export-pdf" class="btn btn-danger">Export as PDF</button>
            <button id="export-csv" class="btn btn-success">Export as CSV</button>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
