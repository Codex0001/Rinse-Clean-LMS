<?php
session_start();

// Ensure the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php'; 

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Handle form submission for new order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $laundry_type = trim($_POST['laundry_type']);
    $fabric_softener = trim($_POST['fabric_softener']);
    $pickup_time = $_POST['pickup_time'];
    $special_instructions = trim($_POST['special_instructions']);
    $customer_name = $_SESSION['username']; // Get the customer name from the session

    // Validation
    if (empty($laundry_type) || empty($pickup_time)) {
        $error_message = "Laundry type and pickup time are required.";
    } else {
        // Prepare and execute the SQL insert statement
        $sql = "INSERT INTO orders (customer_name, laundry_type, fabric_softener, pickup_time, special_instructions, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $customer_name, $laundry_type, $fabric_softener, $pickup_time, $special_instructions);

        if ($stmt->execute()) {
            // Optionally redirect or display success message
            $success_message = "Order placed successfully!";
            header("Location: orders.php?success=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error placing order: " . $stmt->error;
        }
    }
}

// Fetch orders for the logged-in customer
$customer_name = $_SESSION['username']; // Get the username from the session
$sql = "SELECT orders.id, orders.pickup_time AS date, orders.laundry_type AS service, orders.status 
        FROM orders 
        WHERE orders.customer_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $customer_name); // Bind customer_name
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
    <title>Customer | Orders</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../customers/css/style.css"> 
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../customers/public/sidebar.php'; ?>
</div>

<section class="home">
    <div class="dashboard">
        <div class="header-strip">
            <h1>Your Orders, <?php echo htmlspecialchars($customer_name); ?>!</h1>
        </div>

        <!-- New Order Form -->
        <form method="POST" action="orders.php" class="mt-5">
            <div class="mb-3">
                <label for="laundry_type" class="form-label">Laundry Type</label>
                <select id="laundry_type" name="laundry_type" class="form-select" required>
                    <option value="">Select Laundry Type</option>
                    <option value="Wash & Fold">Wash & Fold</option>
                    <option value="Dry Cleaning">Dry Cleaning</option>
                    <option value="Ironing Service">Ironing Service</option>
                    <option value="Stain Removal">Stain Removal</option>
                    <option value="Bedding & Linens">Bedding & Linens</option>
                    <option value="Curtains & Drapes">Curtains & Drapes</option>
                    <option value="Specialty Fabrics">Specialty Fabrics</option>
                    <option value="Bulk Laundry">Bulk Laundry</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="fabric_softener" class="form-label">Fabric Softener</label>
                <select id="fabric_softener" name="fabric_softener" class="form-select">
                    <option value="">Select Fabric Softener</option>
                    <option value="Lavender Scent">Lavender Scent</option>
                    <option value="Fresh Linen">Fresh Linen</option>
                    <option value="Coconut Breeze">Coconut Breeze</option>
                    <option value="Baby Soft">Baby Soft</option>
                    <option value="Clean Cotton">Clean Cotton</option>
                    <option value="Jasmine Blossom">Jasmine Blossom</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="pickup_time" class="form-label">Pickup Time</label>
                <input type="datetime-local" class="form-control" id="pickup_time" name="pickup_time" required>
            </div>
            <div class="mb-3">
                <label for="special_instructions" class="form-label">Special Instructions</label>
                <textarea class="form-control" id="special_instructions" name="special_instructions" placeholder="Any special instructions..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>

        <!-- Display Success or Error Messages -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif (!empty($success_message)): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['date']); ?></td>
                        <td><?php echo htmlspecialchars($order['service']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (Placeholder for now) -->
        <div class="pagination mt-4">
            <button class="btn btn-primary">Previous</button>
            <button class="btn btn-primary">Next</button>
        </div>

    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
