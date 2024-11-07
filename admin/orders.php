<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

// Initialize message variable
$message = '';

// Update order status and assign staff
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $staff_id = $_POST['staff_id']; // Get selected staff ID from the dropdown

    // Set status to 'Scheduled' when staff is assigned
    $new_status = 'Scheduled';

    // Update the order status and assign staff
    $update_sql = "UPDATE orders SET status = ?, staff_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssi', $new_status, $staff_id, $order_id);

    if ($update_stmt->execute()) {
        $message = "Order updated successfully.";
    } else {
        $message = "Error updating order. Please try again.";
    }

    // Clear cache and refresh orders
    header("Location: orders.php?message=" . urlencode($message)); // Redirect to the orders page with a message
    exit();
}

// Fetch all orders from the database
$sql = "SELECT orders.id, orders.customer_name, orders.pickup_time, orders.laundry_type, orders.status, orders.fabric_softener, orders.payment_status, users.username AS staff_name 
        FROM orders 
        LEFT JOIN users ON orders.staff_id = users.id"; // Join users to get staff names

$result = $conn->query($sql);

// Store the orders in an array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Check for search/filter
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $sql .= " WHERE customer_name LIKE '%$search_query%' OR laundry_type LIKE '%$search_query%'";
    $result = $conn->query($sql);

    // Store the filtered orders
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Fetch all staff members for the dropdown from the users table
$staff_sql = "SELECT id, username AS name FROM users WHERE role = 'staff'"; // Get only staff users
$staff_result = $conn->query($staff_sql);
$staff_members = [];
while ($row = $staff_result->fetch_assoc()) {
    $staff_members[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Orders</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/css/style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../admin/publics/sidebar.php'; ?>
</div>

<section class="home">
    <div class="container mt-5">
        <h1>Manage Orders</h1>

        <!-- Success/Error Message -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        
        <!-- Search Bar -->
        <form method="POST" class="mb-3">
            <input type="text" name="search_query" placeholder="Search by customer name or laundry type" class="form-control" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" name="search" class="btn btn-primary mt-2">Search</button>
        </form>

        <!-- Orders Table Section -->
        <div class="orders-section mt-5">
            <h2 class="mb-4">All Orders</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Fabric Softener</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Assigned Staff</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['pickup_time']); ?></td>
                            <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['fabric_softener']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                            <td><?php echo htmlspecialchars($order['staff_name'] ?? 'Unassigned'); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <select name="staff_id" class="form-select" required>
                                        <option value="">Assign Staff</option>
                                        <?php foreach ($staff_members as $staff): ?>
                                            <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="update_order" class="btn btn-success mt-1">Assign Staff</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
