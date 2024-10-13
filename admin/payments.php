<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Payments | LMS</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/css/style.css">
</head>
<body>

    <!-- Sidebar inclusion -->
    <?php include '../admin/publics/sidebar.php'; ?>

    <!-- Payments Page Content -->
    <section class="home">
        <div class="container mt-5">
            <h2 class="mb-4">Payments Management</h2>

            <!-- Payment Statistics Section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Total Payments</h5>
                            <h3>120</h3> <!-- Fetch dynamically -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Amount</h5>
                            <h3>Ksh 150,000</h3> <!-- Fetch dynamically -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Payments</h5>
                            <h3>10</h3> <!-- Fetch dynamically -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Failed Payments</h5>
                            <h3>5</h3> <!-- Fetch dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="search" placeholder="Search by Order ID or Customer Name">
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="status_filter">
                        <option value="">Filter by Status</option>
                        <option value="Successful">Successful</option>
                        <option value="Pending">Pending</option>
                        <option value="Failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="date_filter" placeholder="Filter by Payment Date">
                </div>
            </div>

            <!-- Payment Overview Table -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Payments Overview</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Amount (Ksh)</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populate rows dynamically from the database -->
                            <tr>
                                <td>RCLMS001</td>
                                <td>John Doe</td>
                                <td>1500</td>
                                <td><span class="badge bg-success">Successful</span></td>
                                <td>2024-10-10</td>
                                <td>
                                    <a href="edit_payment.php?payment_id=1" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_payment.php?payment_id=1" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <!-- Add more rows dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Alerts Section -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h3>Payment Alerts</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li><strong>5 Pending Payments</strong> need confirmation.</li>
                        <li><strong>2 Failed Payments</strong> require attention.</li>
                        <li><strong>3 Payments</strong> received in the last 24 hours.</li>
                    </ul>
                </div>
            </div>

            <!-- Download Report Button -->
            <a href="download_payments_report.php" class="btn btn-outline-secondary mb-4">Download Payment Report (PDF)</a>

            <!-- Add/Edit Payment Modal Trigger -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                Add Payment
            </button>

        </div>
    </section>

    <!-- Add/Edit Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Add/Edit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../process/add_or_edit_payment.php" method="POST">
                        <!-- Form Fields -->
                        <div class="form-group mb-3">
                            <label for="order_id">Order ID</label>
                            <select class="form-control" id="order_id" name="order_id" required>
                                <!-- Populate Order IDs dynamically -->
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Successful">Successful</option>
                                <option value="Pending">Pending</option>
                                <option value="Failed">Failed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer inclusion -->
    <?php include '../admin/publics/footer.php'; ?>

    <!-- JS files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="path_to_jquery.js"></script>
    <script src="path_to_custom_scripts.js"></script>
</body>
</html>
