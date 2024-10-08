<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Fetch the staff name from the session or database
$staff_name = $_SESSION['username']; // Get the username from the session

// Database connection
require_once '../includes/rinseclean_lms.php'; 

// Fetch staff ID from the session
$staff_id = $_SESSION['user_id'];

// Fetch data for widgets (dummy data for example)
$total_orders = 0; // You will fetch this from the database
$orders_in_progress = 0; // You will fetch this from the database
$total_payouts = 0; // You will fetch this from the database

// SQL query to fetch orders for the logged-in staff member
$sql = "SELECT orders.id, orders.pickup_time AS date, orders.laundry_type AS service, orders.status, orders.fabric_softener, orders.delivery_time 
        FROM orders 
        WHERE orders.staff_id = ?"; // Assuming orders are linked to staff by staff_id

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $staff_id); // Bind staff_id
$stmt->execute();
$result = $stmt->get_result();

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Staff CSS -->
    <link rel="stylesheet" href="../staff/css/style.css"> 
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include '../staff/public/sidebar.php'; ?>
    </div>

<section class="home">
    <div class="dashboard">
        <div class="header-strip">
            <h1>Welcome, <?php echo htmlspecialchars($staff_name); ?>!</h1>
            <div class="time-display" id="current-time"></div>
        </div>
        
        <!-- Clock-in/Clock-out Section -->
        <div class="clock-section">
            <button class="btn btn-success" id="clock-in-btn">Clock In</button>
            <button class="btn btn-danger" id="clock-out-btn">Clock Out</button>
        </div>

        <!-- Widgets Section -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="widget bg-primary text-white p-3">
                    <h2>Schedule Pickups</h2>
                    <p id="schedule-pickups"><?php echo $total_orders; ?></p>
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
                    <h2>Total Earned Payouts</h2>
                    <p id="total-payouts"><?php echo $total_payouts; ?></p>
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
                        <th scope="col">Delivery Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                        <td><?php echo htmlspecialchars($order['service']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['fabric_softener']); ?></td>
                        <td><?php echo htmlspecialchars($order['delivery_time']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Performance Overview Section -->
        <div class="performance-section mt-5">
            <h2>Performance Overview</h2>
            <p>KPIs and Targets: <!-- Add your KPI and target details here --></p>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Function to update current time
    function updateCurrentTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('current-time').innerHTML = now.toLocaleDateString('en-US', options);
    }

    setInterval(updateCurrentTime, 1000); // Update time every second

    // Add functionality for clock-in and clock-out buttons here
</script>
</body>
</html>
