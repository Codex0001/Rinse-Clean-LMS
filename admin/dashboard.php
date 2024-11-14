<?php
session_start();

// Check if the admin name is set in the session
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; // Fallback name if not set

// Database connection settings
require_once '../includes/rinseclean_lms.php';

// Fetch total customers
$query = "SELECT COUNT(*) as total_customers FROM customers";
$total_customers = $conn->query($query)->fetch_assoc()['total_customers'];

// Fetch orders in progress (where status is 'In Progress')
$query = "SELECT COUNT(*) as orders_in_progress FROM orders WHERE status = 'In Progress'";
$orders_in_progress = $conn->query($query)->fetch_assoc()['orders_in_progress'];

// Fetch total revenue (calculate profit to loss)
$query = "SELECT SUM(cost) as total_revenue FROM orders WHERE status = 'Completed'";
$total_revenue = $conn->query($query)->fetch_assoc()['total_revenue'] ?: 0; // Fallback to 0 if NULL

// Fetch monthly revenue
$monthly_revenue = $conn->query("SELECT MONTH(created_at) as month, SUM(cost) as total FROM orders WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) GROUP BY MONTH(created_at)")->fetch_all(MYSQLI_ASSOC);
$months = array_map(fn($data) => date('F', mktime(0, 0, 0, $data['month'], 10)), $monthly_revenue);
$revenue = array_map(fn($data) => (float)$data['total'], $monthly_revenue);

// Fetch customer feedback scores
$feedback_scores = $conn->query("SELECT MONTH(created_at) as month, AVG(score) as average_score FROM feedback GROUP BY MONTH(created_at)")->fetch_all(MYSQLI_ASSOC);
$feedback_months = array_map(fn($data) => date('F', mktime(0, 0, 0, $data['month'], 10)), $feedback_scores);
$average_scores = array_map(fn($data) => (float)$data['average_score'], $feedback_scores);

// Fetch recent orders (limit to 5 for a quick view)
$recent_orders = $conn->query("SELECT order_id, customer_name, laundry_type, status FROM orders ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Fetch pending orders summary
$pending_orders_summary = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$pending_summary = [];
foreach ($pending_orders_summary as $order) {
    $pending_summary[$order['status']] = $order['count'];
}

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        .dashboard {
            padding: 20px;
        }
    </style>
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
                    </div>
                </div>
            </div>

            <!-- Revenue Over Time and Customer Feedback Scores Chart -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <h2>Revenue Over Time</h2>
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
                <div class="col-lg-6">
                    <h2>Customer Feedback Scores</h2>
                    <canvas id="feedbackChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="recent-orders-section mt-5">
                <h2 class="mb-4">Recent Orders</h2>
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Laundry Type</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_orders)): ?>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No recent orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pending Orders Summary Section -->
            <div class="pending-orders-summary mt-5">
                <h2 class="mb-4">Pending Orders Summary</h2>
                <ul class="list-group">
                    <?php
                    $statuses = ['Pending', 'In Progress', 'Completed'];
                    foreach ($statuses as $status) {
                        $count = isset($pending_summary[$status]) ? $pending_summary[$status] : 0;
                        echo "<li class='list-group-item'>${status}: ${count}</li>";
                    }
                    ?>
                </ul>
            </div>

                <!-- Active Staff Section -->
                <div class="active-staff-section mt-5">
                    <h2 class="mb-4">Active Staff</h2>
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Username</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Role</th>
                                <th scope="col">Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($active_staff)): ?>
                                <?php foreach ($active_staff as $staff): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($staff['username']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['role']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['payout'] ?? 'N/A'); ?></td> <!-- Updated line -->
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No active staff found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
    </section>



    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?php echo json_encode($revenue); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Feedback Scores Chart
        const feedbackCtx = document.getElementById('feedbackChart').getContext('2d');
        const feedbackChart = new Chart(feedbackCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($feedback_months); ?>,
                datasets: [{
                    label: 'Average Feedback Score',
                    data: <?php echo json_encode($average_scores); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
