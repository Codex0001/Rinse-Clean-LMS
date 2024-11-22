<?php 
// Include database connection
include '../includes/rinseclean_lms.php';

// Initialize variables for salary statistics
$totalSalaries = $totalPayout = $pendingSalaries = $paidSalaries = $failedSalaries = 0;

// Query to fetch salary statistics from the staff table
$statsQuery = "SELECT 
                COUNT(DISTINCT reg_number) as totalSalaries, 
                SUM(salary) as totalPayout, 
                SUM(CASE WHEN salary_status = 'Pending' THEN salary ELSE 0 END) as pendingSalaries, 
                SUM(CASE WHEN salary_status = 'Paid' THEN salary ELSE 0 END) as paidSalaries,
                SUM(CASE WHEN salary_status = 'Failed' THEN salary ELSE 0 END) as failedSalaries 
            FROM staff";
$statsResult = $conn->query($statsQuery);

if ($statsResult && $statsResult->num_rows > 0) {
    $stats = $statsResult->fetch_assoc();
    $totalSalaries = $stats['totalSalaries'];
    $totalPayout = $stats['totalPayout'];
    $pendingSalaries = $stats['pendingSalaries'];
    $paidSalaries = $stats['paidSalaries'];
    $failedSalaries = $stats['failedSalaries'];
}

// Fetch salary overview data from staff table
$salariesQuery = "SELECT reg_number, name, salary, salary_status, salary_payout_date 
                FROM staff"; 
$salariesResult = $conn->query($salariesQuery);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Salaries | LMS</title>
        <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../admin/css/style.css">
        <!-- DataTables CSS -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    </head>
    <body>

        <!-- Sidebar inclusion -->
        <?php include '../admin/publics/sidebar.php'; ?>

        <!-- Salaries Page Content -->
        <section class="home">
            <div class="container mt-5">
                <h2 class="mb-4">Salary Management</h2>

                <!-- Salary Statistics Section -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Total Salaries</h5>
                                <h3><?php echo number_format($totalSalaries, 0); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Total paid</h5>
                                <h3>Ksh <?php echo number_format($paidSalaries, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Pending Salaries</h5>
                                <h3>Ksh <?php echo number_format($pendingSalaries, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title">Failed Salaries</h5>
                                <h3>Ksh <?php echo number_format($failedSalaries, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="search" placeholder="Search by Staff ID or Name">
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="status_filter">
                            <option value="">Filter by Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="date_filter" placeholder="Filter by Payout Date">
                    </div>
                </div>

              <!-- Salary Overview Table -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Salaries Overview</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="salariesTable">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Salary (Ksh)</th>
                                <th>Last Paid Date</th>
                                <th>Payout Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Modify the query based on the schema to fetch staff and salary information
                                $query = "SELECT reg_number, name, phone_number, salary, salary_status, last_paid_date 
                                        FROM staff";
                                
                                // Execute the query
                                $salariesResult = $conn->query($query);

                                if ($salariesResult && $salariesResult->num_rows > 0): 
                                    while ($salary = $salariesResult->fetch_assoc()): 
                            ?>
                                        <tr>
                                            <td><?php echo $salary['reg_number']; ?></td>
                                            <td><?php echo $salary['name']; ?></td>
                                            <td><?php echo $salary['phone_number']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $salary['salary_status'] == 'Paid' ? 'success' : ($salary['salary_status'] == 'Pending' ? 'warning' : 'danger'); ?>">
                                                    <?php echo $salary['salary_status']; ?>
                                                </span>
                                            </td>
                                            <td>Ksh <?php echo number_format($salary['salary'], 2); ?></td>
                                            <td><?php echo $salary['last_paid_date'] ? $salary['last_paid_date'] : 'N/A'; ?></td>
                                            <td>
                                                <!-- Change Status Form -->
                                                <form action="../admin/change_status.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="salary_id" value="<?php echo $salary['reg_number']; ?>">
                                                    <select name="status" class="form-control d-inline w-auto" onchange="this.form.submit()">
                                                        <option value="Pending" <?php echo $salary['salary_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Paid" <?php echo $salary['salary_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                                        <option value="Failed" <?php echo $salary['salary_status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                            <a href="../admin/delete_salary.php echo $salary['reg_number']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this salary record?');">Delete</a>
                                            </td>
                                            
                                            
                                        </tr>
                            <?php 
                                    endwhile;
                                else: 
                            ?>
                                    <tr>
                                        <td colspan="8">No salary records found.</td>
                                    </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <!-- Download Report Button -->
                <a href="../admin/download_salaries_report.php" class="btn btn-warning" role="button"> Download Salary Report </a>

                <!-- Add Salary Modal Trigger -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#salaryModal">
                    Add Salary
                </button>
            </div>
        </section>

        <!-- Add/Edit Salary Modal -->
        <div class="modal fade" id="salaryModal" tabindex="-1" aria-labelledby="salaryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salaryModalLabel">Add/Edit Salary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../admin/save_salary.php" method="POST">
                            <!-- Form Fields for Adding/Editing Salary -->
                            <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff Name</label>
                            <select class="form-control" id="staff_id" name="staff_name" required>
                                <option value="">Select Staff</option>
                                <?php
                                // Fetch staff names and IDs from the staff table
                                $staffQuery = "SELECT reg_number, name FROM staff";
                                $staffResult = $conn->query($staffQuery);
                                while ($staff = $staffResult->fetch_assoc()) {
                                    echo "<option value='" . $staff['reg_number'] . "'>" . $staff['name'] . "</option>";
                                }
                                ?>
                            </select>
                            </div>
                            <div class="mb-3">
                                <label for="salary_amount" class="form-label">Salary Amount (Ksh)</label>
                                <input type="number" class="form-control" id="salary_amount" name="salary_amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Salary Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="payout_date" class="form-label">Payout Date</label>
                                <input type="date" class="form-control" id="payout_date" name="payout_date" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Salary</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS and DataTables JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

        <!-- DataTable Initialization -->
        <script>
        $(document).ready(function() {
            $('#salariesTable').DataTable();
        });

        // Filtering logic
        $('#search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#salariesTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#status_filter').on('change', function() {
            var status = $(this).val().toLowerCase();
            $('#salariesTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(status) > -1)
            });
        });

        $('#date_filter').on('change', function() {
            var date = $(this).val();
            $('#salariesTable tbody tr').filter(function() {
                $(this).toggle($(this).text().indexOf(date) > -1)
            });
        });
        </script>
    </body>
</html>
