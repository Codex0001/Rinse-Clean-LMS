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

// Updated SQL query to fetch orders for the logged-in customer, including payment status
$sql = "SELECT orders.order_id AS order_number, orders.pickup_time AS date, orders.laundry_type AS service, 
        orders.status, orders.weight, orders.cost, orders.payment_status 
        FROM orders 
        WHERE orders.customer_name = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Check if the prepare failed
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

// Bind parameters
$stmt->bind_param('s', $customer_name); // Bind customer_name

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Check if there are any orders
$message = empty($orders) ? "No orders found for your account." : "";
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
    <style>
        /* Custom styles for the reports page */
        body {
            background-color: #f8f9fa;
        }
        .dashboard {
            padding: 20px;
        }
        .header-strip {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #search {
            width: 70%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        #filter-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #filter-button:hover {
            background-color: #0056b3;
        }
        .orders-section {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .table {
            margin-top: 10px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .btn {
            margin: 0 5px;
        }
        .export-options {
            margin-top: 20px;
        }
        .export-options button {
            width: 150px;
        }
    </style>
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

        <!-- Filters Section -->
        <div class="filter-section mb-4">
            <input type="text" id="search" placeholder="Search by order number or service" class="form-control">
            <button type="button" id="filter-button" class="btn">Filter</button>
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
                        <th scope="col">Order Number</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">weight</th>
                        <th scope="col">Cost</th>
                        <th scope="col">Status</th>
                        <th scope="col">Payment Status</th> <!-- Added Payment Status -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                        <td><?php echo htmlspecialchars($order['service']); ?></td>
                        <td><?php echo htmlspecialchars($order['weight']); ?></td>
                        <td><?php echo htmlspecialchars($order['cost']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_status']); ?></td> <!-- Displaying Payment Status -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
