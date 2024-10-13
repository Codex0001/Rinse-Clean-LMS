<?php
session_start();

// Check if the admin name is set in the session
if (isset($_SESSION['admin_name'])) {
    $admin_name = $_SESSION['admin_name'];
} else {
    $admin_name = 'Admin'; // Fallback name if not set
}

// Database connection settings
require_once '../includes/rinseclean_lms.php';

// Fetch total customers
$query = "SELECT COUNT(*) as total_customers FROM customers";
$result = $conn->query($query);
$total_customers = $result->fetch_assoc()['total_customers'];

// Fetch orders in progress (where status is 'In Progress')
$query = "SELECT COUNT(*) as orders_in_progress FROM orders WHERE status = 'In Progress'";
$result = $conn->query($query);
$orders_in_progress = $result->fetch_assoc()['orders_in_progress'];

// Fetch total revenue (calculate profit to loss)
$query = "SELECT SUM(cost) as total_revenue FROM orders WHERE status = 'Completed'";
$result = $conn->query($query);
$total_revenue = $result->fetch_assoc()['total_revenue'] ?: 0; // Fallback to 0 if NULL

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../admin/css/style.css"> 
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include '../admin/publics/sidebar.php'; ?>
    </div>

   <!-- Dashboard Section -->
<section class="home">
    <div class="dashboard">
        <div class="header-strip">
        <h1>
            <?php
            // Get the current hour
            $current_hour = date('G');

            // Determine the greeting based on the time of day
            if ($current_hour >= 5 && $current_hour < 12) {
                echo "Good Morning, " . htmlspecialchars($admin_name) . "!";
            } elseif ($current_hour >= 12 && $current_hour < 17) {
                echo "Good Afternoon, " . htmlspecialchars($admin_name) . "!";
            } elseif ($current_hour >= 17 && $current_hour < 21) {
                echo "Good Evening, " . htmlspecialchars($admin_name) . "!";
            } else {
                echo "Good Night, " . htmlspecialchars($admin_name) . "!";
            }
            ?>
        </h1>
        </div>
     
       <!-- Widgets Section -->
        <div class="row mt-4">
            <div class="col-lg-3">
                <div class="widget bg-primary text-white p-3">
                    <h2>Total Customers</h2>
                    <p id="total-customers"><?php echo htmlspecialchars($total_customers); ?></p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget bg-warning text-white p-3">
                    <h2>Orders In Progress</h2>
                    <p id="orders-in-progress"><?php echo htmlspecialchars($orders_in_progress); ?></p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget bg-success text-white p-3">
                    <h2>Profit / Loss</h2>
                    <p id="total-revenue"><?php echo htmlspecialchars($total_revenue); ?> (Profit)</p>
                    <!-- You can adjust the message to reflect whether it's profit or loss -->
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget bg-info text-white p-3">
                    <h2>No Active Staff</h2>
                    <p id="active-staff"><?php echo htmlspecialchars($active_staff); ?></p>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="activities-section mt-5">
            <h2 class="mb-4">Recent Activities</h2>
            <ul class="list-group">
                <li class="list-group-item">Customer John Doe registered.</li>
                <li class="list-group-item">Order #12345 completed.</li>
                <li class="list-group-item">Feedback received from Jane Smith.</li>
            </ul>
        </div>

        <!-- Customers Table Section -->
        <div class="customers-section mt-5">
            <h2 class="mb-4">Customers</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Customer ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Total Orders</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['id']); ?></td>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['total_orders']); ?></td>
                            <td>
                                <button class="btn btn-info">View</button>
                                <button class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add footer below main content -->
        <?php include '../admin/publics/footer.php'; ?>
    </div>
</section>

         
<div

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
