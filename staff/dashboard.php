<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Fetch the staff name from the session
$staff_name = htmlspecialchars($_SESSION['username']);

// Database connection
require_once '../includes/rinseclean_lms.php'; 

// Fetch staff ID from the session
$staff_id = $_SESSION['user_id'];

// Initialize widget variables
$total_orders = 0;
$orders_in_progress = 0;
$total_payouts = 0;

// Query to get the total orders assigned to the staff member
$sql = "SELECT COUNT(*) AS total_orders FROM orders WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$stmt->bind_result($total_orders);
$stmt->fetch();
$stmt->close();

// Query to get orders in progress
$sql = "SELECT COUNT(*) AS orders_in_progress FROM orders WHERE staff_id = ? AND status = 'In Progress'";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$stmt->bind_result($orders_in_progress);
$stmt->fetch();
$stmt->close();

// Step 1: Fetch the reg_number using the staff_id
$sql = "SELECT reg_number FROM staff WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}
$stmt->bind_param('i', $staff_id); // Use the staff_id from session
$stmt->execute();
$stmt->bind_result($staff_reg_number); // Fetch the reg_number
$stmt->fetch();
$stmt->close();

// Step 2: Fetch the salary using the reg_number
$sql = "SELECT salary FROM staff WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}
$stmt->bind_param('s', $staff_reg_number); // Bind reg_number as a string
$stmt->execute();
$stmt->bind_result($total_salary); // Store the result in $total_salary
$stmt->fetch();
$stmt->close();
// Query to fetch orders for the logged-in staff member
$sql = "SELECT id AS order_id, customer_name, pickup_time, laundry_type, status, fabric_softener
        FROM orders 
        WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$result = $stmt->get_result();

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../staff/css/style.css"> 
    <style>
        .dashboard {
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include '../staff/public/sidebar.php'; ?>
    </div>

<section class="home">
    <div class="dashboard">
        <div class="header-strip d-flex justify-content-between align-items-center">
            <h1>Welcome, <?php echo $staff_name; ?>!</h1>
        </div>
        
        <!-- Widgets Section -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="widget bg-primary text-white p-3 rounded">
                    <h2>Total Orders</h2>
                    <p class="h1" id="total-orders"><?php echo $total_orders; ?></p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="widget bg-warning text-white p-3 rounded">
                    <h2>Orders In Progress</h2>
                    <p class="h1" id="orders-in-progress"><?php echo $orders_in_progress; ?></p>
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
                        <th scope="col">Customer Name</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Fabric Softener</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                      <td><?php echo htmlspecialchars($order['pickup_time']); ?></td>
                            <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['fabric_softener']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
