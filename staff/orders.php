<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

// Initialize message variable
$message = '';

// Change order status to In Progress or Completed
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status']; // Get selected status

    // Update the order status
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('si', $new_status, $order_id);

    if ($update_stmt->execute()) {
        $message = "Order status updated successfully.";
    } else {
        $message = "Error updating order status. Please try again.";
    }

    // Clear cache and refresh orders
    header("Location: staff_orders.php?message=" . urlencode($message)); // Redirect to the staff orders page with a message
    exit();
}

// Confirm payments
if (isset($_POST['confirm_payment'])) {
    $order_id = $_POST['order_id'];

    // Update payment status
    $payment_sql = "UPDATE orders SET payment_status = 'Confirmed' WHERE id = ?";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param('i', $order_id);

    if ($payment_stmt->execute()) {
        $message = "Payment confirmed successfully.";
    } else {
        $message = "Error confirming payment. Please try again.";
    }

    // Clear cache and refresh orders
    header("Location: staff_orders.php?message=" . urlencode($message)); // Redirect to the staff orders page with a message
    exit();
}

// Fetch all orders assigned to this staff member
$staff_id = $_SESSION['user_id']; // Assuming user_id is the same as staff_id
$sql = "SELECT orders.id, orders.customer_name, orders.pickup_time, orders.laundry_type, orders.status, orders.fabric_softener, orders.payment_status 
        FROM orders 
        WHERE staff_id = ?"; // Get orders assigned to the staff member

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $staff_id);
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
    <title>Staff | Orders</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/css/style.css">
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
    <div class="container mt-5">
        <h1>Manage Your Orders</h1>

        <!-- Success/Error Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Orders Table Section -->
        <div class="orders-section mt-5">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Pickup Time</th>
                        <th scope="col">Laundry Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['pickup_time']); ?></td>
                            <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                    <select name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    <button type="submit" name="update_order" class="btn btn-success btn-sm">Update</button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                    <button type="submit" name="confirm_payment" class="btn btn-primary btn-sm">Confirm Payment</button>
                                </form>
                            </td>
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
</body>
</html>