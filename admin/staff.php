<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Staff Management</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/css/style.css">
</head>
<style>
    .dashboard {
        padding: 20px;
    }
</style>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../admin/publics/sidebar.php'; ?>
</div>

<!-- Staff Management Section -->
<section class="home">
    <div class="dashboard">
        <div class="header-strip">
            <h1>Staff Management</h1>
        </div>

        <!-- Add Staff Button -->
        <div class="text-end mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add Staff</button>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStaffModalLabel">Add New Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Alert Placeholder -->
                        <div id="alertPlaceholder"></div>

                        <form action="add_staff_process.php" method="POST">
                            <?php
                            // Include database connection
                            include '../includes/rinseclean_lms.php';

                            // Fetch the last registration number from the database
                            $sql = "SELECT reg_number FROM staff ORDER BY reg_number DESC LIMIT 1";
                            $result = $conn->query($sql);
                            $newRegNumber = 'RCML-EMP-0001'; // Default registration number for the first staff

                            if ($result->num_rows > 0) {
                                $lastRegNumber = $result->fetch_assoc()['reg_number'];
                                $lastNum = (int)substr($lastRegNumber, strrpos($lastRegNumber, '-') + 1);
                                $nextNum = $lastNum + 1;

                                // Ensure the registration number is formatted as RCML-EMP-XXXX
                                $newRegNumber = 'RCML-EMP-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                            }

                            $conn->close(); // Close the database connection
                            ?>
                            <div class="mb-3">
                                <label for="reg_number" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" name="reg_number" value="<?php echo $newRegNumber; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" name="address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Staff</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Staff Table -->
        <div class="current-staff-section mt-5">
            <h2>Current Staff</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Registration Number</th>
                        <th scope="col">Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Include database connection
                    include '../includes/rinseclean_lms.php';

                    // Fetch current staff from the database
                    $sql = "SELECT * FROM staff";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($staff = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($staff['reg_number']) . "</td>
                                    <td>" . htmlspecialchars($staff['name']) . "</td>
                                    <td>" . htmlspecialchars($staff['phone_number']) . "</td>
                                    <td>" . htmlspecialchars($staff['status']) . "</td>
                                    <td>
                                        <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editStaffModal" . htmlspecialchars($staff['reg_number']) . "'>Edit</button>
                                        <a href='deactivate_staff.php?id=" . htmlspecialchars($staff['reg_number']) . "' class='btn btn-danger' onclick='return confirmDeactivate();'>Deactivate</a>
                                    </td>
                                  </tr>";

                            // Edit Staff Modal
                            ?>
                            <div class="modal fade" id="editStaffModal<?php echo htmlspecialchars($staff['reg_number']); ?>" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="edit_staff_process.php" method="POST">
                                                <input type="hidden" name="reg_number" value="<?php echo htmlspecialchars($staff['reg_number']); ?>">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="phone_number" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($staff['phone_number']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars(isset($staff['email']) ? $staff['email'] : ''); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Address</label>
                                                    <textarea class="form-control" name="address"><?php echo htmlspecialchars(isset($staff['address']) ? $staff['address'] : ''); ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No staff found.</td></tr>";
                    }

                    $conn->close(); // Close the database connection
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDeactivate() {
        return confirm("Are you sure you want to deactivate this staff?");
    }
</script>

</body>
</html>
