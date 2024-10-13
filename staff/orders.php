<?php
session_start();

// Ensure the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Fetch the staff name from the session or database
$staff_name = $_SESSION['username'];

// Database connection
require_once '../includes/rinseclean_lms.php'; 

// Fetch staff ID from the session
$staff_id = $_SESSION['user_id'];

// Fetch orders for the logged-in staff member
$sql = "SELECT orders.id, orders.pickup_time AS date, orders.laundry_type AS service, orders.status, orders.fabric_softener, orders.delivery_time, orders.payment_status 
        FROM orders 
        WHERE orders.staff_id = ?"; // Assuming orders are linked to staff by staff_id

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
                        <th scope="col">Payment Status</th>
                        <th scope="col">Actions</th>
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
                        <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                        <td>
                            <button class="btn btn-success btn-sm confirm-payment" data-order-id="<?php echo htmlspecialchars($order['id']); ?>" data-status="Paid">Confirm Payment</button>
                            <button class="btn btn-warning btn-sm confirm-payment" data-order-id="<?php echo htmlspecialchars($order['id']); ?>" data-status="Unpaid">Unconfirm Payment</button>
                            <button class="btn btn-primary btn-sm update-status" data-order-id="<?php echo htmlspecialchars($order['id']); ?>" data-status="In Progress">In Progress</button>
                            <button class="btn btn-success btn-sm update-status" data-order-id="<?php echo htmlspecialchars($order['id']); ?>" data-status="Completed">Completed</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Confirm payment functionality
document.querySelectorAll('.confirm-payment').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const newStatus = this.dataset.status;

        // AJAX call to update payment status
        fetch('../includes/update_payment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ orderId, newStatus }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment status updated successfully!');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error updating payment status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating payment status.');
        });
    });
});

// Update order status functionality (same as before)
document.querySelectorAll('.update-status').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const newStatus = this.dataset.status;

        // AJAX call to update order status
        fetch('../includes/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ orderId, newStatus }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order status updated successfully!');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error updating order status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status.');
        });
    });
});
</script>
</body>
</html>
