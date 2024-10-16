<?php
session_start();
require '../includes/rinseclean_lms.php'; // Include your database connection

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../public/errors/403.php"); // Access denied if not admin
    exit();
}

// Fetch all customers from the database
$query = "SELECT * FROM customers ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <title>Admin | Manage Customers</title>
    <!-- Include your CSS and Bootstrap for styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/css/style.css">
</head>
<body>
        <div class="d-flex" id="wrapper">
            <!-- Sidebar -->
            <?php include '../admin/publics/sidebar.php'; ?>
        </div>

<!-- Manage Customers Section -->
<section class="home">
    <div class="container mt-5">
        <h2 class="mb-4">Manage Customers</h2>

        <!-- Combined Customers Management -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3>Customer Management</h3>
            </div>
            <div class="card-body">

                <!-- Success/Error Message -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php elseif (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <!-- Button to trigger Add Customer modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    Add Customer
                </button>

                <!-- Export Buttons -->
                <div class="mt-4">
                    <form method="POST" action="export_customers.php" style="display:inline;">
                        <button type="submit" name="export_csv" class="btn btn-secondary">Export as CSV</button>
                    </form>
                    <form method="POST" action="export_customers.php" style="display:inline;">
                        <button type="submit" name="export_pdf" class="btn btn-secondary">Export as PDF</button>
                    </form>
                </div>

                <!-- Table to display customers -->
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($customer = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo $customer['username']; ?></td>
                                <td><?php echo $customer['email']; ?></td>
                                <td><?php echo $customer['phone_number']; ?></td>
                                <td><?php echo date("Y-m-d", strtotime($customer['created_at'])); ?></td>
                                <td>
                                    <!-- Edit button trigger -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editCustomerModal" data-id="<?php echo $customer['id']; ?>">Edit</button>
                                    <!-- Delete button trigger -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal" data-id="<?php echo $customer['id']; ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" method="POST" action="add_customer_process.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" method="POST" action="edit_customer_process.php">
                    <input type="hidden" name="id" id="edit_customer_id">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="edit_phone_number" name="phone_number" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Customer Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCustomerModalLabel">Delete Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer?</p>
                <form id="deleteCustomerForm" method="POST" action="delete_customer_process.php">
                    <input type="hidden" name="id" id="delete_customer_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap and JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit Customer Modal - Populate the form with customer data
        document.getElementById('editCustomerModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            // Fetch customer details using AJAX and populate the form
            // Implement AJAX to fetch and populate customer data
        });

        // Delete Customer Modal - Set customer ID
        document.getElementById('deleteCustomerModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            document.getElementById('delete_customer_id').value = id;
        });
    </script>
</body>
</html>