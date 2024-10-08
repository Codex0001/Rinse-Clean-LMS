<?php
session_start();

// Ensure the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Assuming you want to fetch the customer name from the session or database
$customer_name = $_SESSION['username']; // Get the username from the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Customers CSS -->
    <link rel="stylesheet" href="../customers/css/style.css"> 
</head>
<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <?php include '../customers/public/sidebar.php'; ?>
        
        <!-- Dashboard Content -->
        <div class="dashboard">
            <div class="header-strip">
                <h1>At your service, <?php echo htmlspecialchars($customer_name); ?>!</h1>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
