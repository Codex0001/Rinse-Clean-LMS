<?php
session_start();
require '../includes/rinseclean_lms.php'; // Include your database connection

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../public/errors/403.php"); // Access denied if not admin
    exit();
}

// Fetch orders for the reports, including payment_status
$query = "SELECT order_id, customer_name, laundry_type, status, cost, payment_status FROM orders ORDER BY order_id DESC";
$result = $conn->query($query);

// Fetch staff performance for the reports
$staffQuery = "SELECT staff_id, COUNT(*) as total_orders FROM orders GROUP BY staff_id";
$staffResult = $conn->query($staffQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Reports</title>
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

    <!-- Reports Section -->
    <section class="home">
        <div class="container mt-5">
            <h2 class="mb-4">Reports Overview</h2>

            <!-- Filters for Reports -->
            <div class="mb-4">
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="date_range" class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Select date range">
                        </div>
                        <div class="col-md-4">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type" name="report_type">
                                <option value="orders">Orders Summary</option>
                                <option value="revenue">Revenue Report</option>
                                <option value="staff_performance">Staff Performance</option>
                                <option value="customer_feedback">Customer Feedback</option>
                            </select>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Export Buttons -->
            <div class="mt-4">
                <form method="POST" action="export_reports.php" style="display:inline;">
                    <button type="submit" name="export_csv" class="btn btn-secondary">Export as CSV</button>
                </form>
                <form method="POST" action="export_reports.php" style="display:inline;">
                    <button type="submit" name="export_pdf" class="btn btn-secondary">Export as PDF</button>
                </form>
            </div>

            <!-- Orders Summary Table -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Orders Summary</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Laundry Type</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Payment Status</th> <!-- Updated column header -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['customer_name']; ?></td>
                                    <td><?php echo $order['laundry_type']; ?></td>
                                    <td><?php echo $order['status']; ?></td>
                                    <td><?php echo $order['cost']; ?></td>
                                    <td><?php echo $order['payment_status']; ?></td> <!-- Updated to payment_status -->
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Staff Performance Table -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Staff Performance</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Total Orders Handled</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($staff = $staffResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $staff['staff_id']; ?></td>
                                    <td><?php echo $staff['total_orders']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap and JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
