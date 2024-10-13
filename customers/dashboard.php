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

// SQL query to fetch orders for the logged-in customer, including payment status
$sql = "SELECT orders.id, orders.pickup_time AS date, orders.laundry_type AS service, orders.status, orders.fabric_softener, orders.payment_status 
        FROM orders 
        WHERE orders.customer_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $customer_name); // Bind customer_name instead of customer_id
$stmt->execute();
$result = $stmt->get_result();

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Calculate total orders, orders in progress, and loyalty points
$total_orders = count($orders);
$orders_in_progress = count(array_filter($orders, fn($order) => $order['status'] === 'In Progress'));

// Assuming each completed order gives 10 loyalty points
$loyalty_points = count(array_filter($orders, fn($order) => $order['status'] === 'Completed')) * 10;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Customers CSS -->
    <link rel="stylesheet" href="../customers/css/style.css"> 
    <style>
        .dashboard {
            padding: 20px;
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
            <h1>At your service, <?php echo htmlspecialchars($customer_name); ?>!</h1>
        </div>
        <!-- Widgets Section -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="widget bg-primary text-white p-3">
                    <h2>Total Orders</h2>
                    <p id="total-orders"><?php echo $total_orders; ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="widget bg-warning text-white p-3">
                    <h2>Orders In Progress</h2>
                    <p id="orders-in-progress"><?php echo $orders_in_progress; ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="widget bg-success text-white p-3">
                    <h2>Loyalty Points</h2>
                    <p id="loyalty-points"><?php echo $loyalty_points; ?></p>
                </div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="orders-section mt-5">
            <h2 class="mb-4">Your Orders</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Fabric Softener</th>
                        <th scope="col">Payment Status</th> <!-- Added Payment Status -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found.</td> <!-- Updated colspan to 6 -->
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['date']); ?></td>
                            <td><?php echo htmlspecialchars($order['service']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['fabric_softener']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_status']); ?></td> <!-- Display Payment Status -->
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle'); // Button to toggle sidebar
    const homeSection = document.querySelector('.home'); // Your home section

    sidebarToggle.addEventListener('click', () => {
        // Toggle the 'collapsed' class on the home section
        homeSection.classList.toggle('collapsed');
    });
</script>
</body>
</html>
