<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not authorized
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../admin/css/style.css"> 
</head>
<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <?php include '../admin/publics/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
            <h1 class="mt-4">Admin Dashboard</h1>
            <p>Welcome, Admin!</p>
        
        <!-- Order Status Widget -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Scheduled Orders</h5>
                        <p class="card-text">10</p>
                        <a href="#" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">In Progress</h5>
                        <p class="card-text">5</p>
                        <a href="#" class="btn btn-warning">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Completed Orders</h5>
                        <p class="card-text">20</p>
                        <a href="#" class="btn btn-success">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Order Status Widget -->
        
        <!-- Additional dashboard content goes here -->
    </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
