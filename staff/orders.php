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
$valid_statuses = ['In Progress', 'Completed'];

// Function to fetch the rate from the rates table based on laundry type
function getRate($laundry_type, $conn) {
    $sql = "SELECT * FROM rates ORDER BY id DESC LIMIT 1"; // Get the most recent rates
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rate = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Return the appropriate rate based on laundry type
    switch ($laundry_type) {
        case 'Wash & Fold':
            return $rate['wash_fold_rate'];
        case 'Dry Cleaning':
            return $rate['dry_cleaning_rate'];
        case 'Ironing Service':
            return $rate['ironing_rate'];
        case 'Bedding & Linens':
            return $rate['bedding_rate'];
        case 'Stain Removal':
            return $rate['stain_removal_rate'];
        case 'Specialty Fabrics':
            return $rate['specialty_fabric_rate'];
        default:
            return 0;
    }
}

// Function to calculate the total cost based on laundry type, weight, and any surcharges
function calculateTotalCost($order, $conn) {
    // Fetch the rate per kg from the rates table based on laundry type
    $laundry_type = $order['laundry_type'];
    $rate = getRate($laundry_type, $conn);

    // If no rate is found, return 0
    if ($rate === 0) {
        return 0;
    }

    // Calculate the cost based on weight (per kg)
    $weight_cost = $rate * $order['weight']; // Use 'weight' for total cost calculation

    // Special instructions surcharge (if applicable)
    $surcharge = 0;
    if ($order['special_instructions'] == 'Stain Removal') {
        $surcharge = 5; // Example surcharge for stain removal
    }

    // Calculate total cost
    $total_cost = $weight_cost + $surcharge;

    return $total_cost;
}

// Function to update order status, weight, and calculate the cost
function updateOrder($conn, $order_id, $new_status, $weight) {  // Weight parameter passed from form
    // Fetch the order details from the database
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    // Update the order with the new weight
    $order['weight'] = $weight; // Set the updated weight

    // Calculate the total cost based on the updated order details
    $total_cost = calculateTotalCost($order, $conn);

    // Update the order status, weight, and cost
    $update_sql = "UPDATE orders SET status = ?, weight = ?, cost = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sdii', $new_status, $weight, $total_cost, $order_id);

    return $update_stmt->execute();
}

// Change order status or update weight
if (isset($_POST['update_order'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $weight = (float)$_POST['weight']; // Ensure weight is a float

    // Validate the new status
    if (!in_array($new_status, $valid_statuses)) {
        $message = "Invalid status selected.";
    } else {
        // Update the order status, weight, and cost
        if (updateOrder($conn, $order_id, $new_status, $weight)) {
            $message = "Order updated successfully.";
        } else {
            $message = "Error updating order. Please try again.";
        }
    }

    // Redirect to the orders page with a message
    header("Location: orders.php?message=" . urlencode($message));
    exit();
}

// Confirm payments
if (isset($_POST['confirm_payment'])) {
    $order_id = (int)$_POST['order_id']; // Ensure it's an integer

    // Update payment status
    $payment_sql = "UPDATE orders SET payment_status = 'Confirmed' WHERE id = ?";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param('i', $order_id);

    if ($payment_stmt->execute()) {
        $message = "Payment confirmed successfully.";
    } else {
        $message = "Error confirming payment. Please try again.";
    }
    $payment_stmt->close();

    // Redirect to the orders page with a message
    header("Location: orders.php?message=" . urlencode($message));
    exit();
}

// Fetch all orders assigned to this staff member
$staff_id = (int)$_SESSION['user_id']; // Assuming user_id is the same as staff_id
$sql = "SELECT orders.id, orders.order_id, orders.customer_name, orders.pickup_time, orders.laundry_type, orders.status, orders.fabric_softener, orders.payment_status, orders.weight, orders.special_instructions, orders.cost 
        FROM orders 
        WHERE staff_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    $message = "No orders found for this staff member.";
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Manage Orders</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/css/style.css">
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
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
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
                        <th scope="col">Fabric Softener</th>
                        <th scope="col">Special Instructions</th>
                        <th scope="col">Status</th>
                        <th scope="col">Payment Status</th>
                        <th scope="col">Total Kgs</th>
                        <th scope="col">Cost</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
        <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($order['pickup_time']); ?></td>
            <td><?php echo htmlspecialchars($order['laundry_type']); ?></td>
            <td><?php echo htmlspecialchars($order['fabric_softener']); ?></td>
            <td><?php echo htmlspecialchars($order['special_instructions']); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
            <td><?php echo htmlspecialchars($order['weight']); ?> Kgs</td>
            <td><?php echo htmlspecialchars($order['cost']); ?></td>
            <td>
                <!-- Update Order Form -->
                <form method="POST" class="d-inline">
                    <input type="number" name="weight" placeholder="Enter Kgs" required min="0" value="<?php echo htmlspecialchars($order['weight']); ?>" />
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>" />
                    <select name="status" required>
                        <option value="In Progress" <?php echo $order['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                    <button type="submit" name="update_order" class="btn btn-primary">Update</button>
                </form>

                <!-- Confirm Payment Form -->
                <form method="POST" class="d-inline mt-2">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>" />
                    <button type="submit" name="confirm_payment" class="btn btn-success">Confirm Payment</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="11">No orders to display</td></tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</section>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
