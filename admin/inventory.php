<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Inventory | LMS</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/css/style.css">
</head>
<body>

    <!-- Sidebar inclusion -->
    <?php include '../admin/publics/sidebar.php'; ?>

    <!-- Inventory Page Content -->
    <section class="home">
        <div class="container mt-5">
            <h2 class="mb-4">Inventory Management</h2>

            <!-- Inventory Overview Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Inventory Overview</h3>
                </div>
                <div class="card-body">
                    <!-- Overview of Items -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Stock Level</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populate rows with inventory data -->
                            <tr>
                                <td>Lavender Scent</td>
                                <td>Fabric Softener</td>
                                <td>25 Liters</td>
                                <td>2024-10-10</td>
                                <td>
                                    <a href="edit_inventory.php?item_id=1" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_inventory.php?item_id=1" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <!-- More rows here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add New Inventory Item -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3>Add New Item</h3>
                </div>
                <div class="card-body">
                    <form action="../process/add_inventory.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="item_name">Item Name</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="detergent">Detergent</option>
                                <option value="softener">Fabric Softener</option>
                                <option value="cleaning_agent">Cleaning Agent</option>
                                <!-- Add more categories as needed -->
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="stock_level">Stock Level</label>
                            <input type="number" class="form-control" id="stock_level" name="stock_level" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Item</button>
                    </form>
                </div>
            </div>

            <!-- Low Stock Notifications -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h3>Low Stock Alerts</h3>
                </div>
                <div class="card-body">
                    <!-- Display low stock items -->
                    <p>No low stock alerts at the moment.</p>
                    <!-- Populate dynamically from inventory data -->
                </div>
            </div>
        </div>
    </section>
</body>
</html>
