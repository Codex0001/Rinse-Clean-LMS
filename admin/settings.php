<?php
// Include database connection
include '../includes/rinseclean_lms.php'; // Adjust the path as necessary

// Fetch current settings
$query = "SELECT * FROM settings LIMIT 1"; // Assuming there is only one row in settings table
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Settings | LMS</title>
    <link rel="shortcut icon" href="../assets/images/icons/laundry-machine.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/css/style.css"> 
</head>
<body>

    <!-- Sidebar inclusion -->
    <?php include '../admin/publics/sidebar.php'; ?>

    <!-- Settings Page Content -->
    <section class="home">
        <div class="container mt-5">
            <h2 class="mb-4">Business Settings</h2>

            <!-- Combined Settings Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Update Settings</h3>
                </div>
                <div class="card-body">
                    <form action="../process/update_settings.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- Service Rates Section -->
                                <h4>Service Rates</h4>
                                <div class="form-group mb-3">
                                    <label for="wash_fold_rate">Wash & Fold Rate (per kg)</label>
                                    <input type="number" class="form-control" id="wash_fold_rate" name="wash_fold_rate" value="<?php echo $settings['wash_fold_rate']; ?>" step="0.01" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="ironing_rate">Ironing Service Rate (per item)</label>
                                    <input type="number" class="form-control" id="ironing_rate" name="ironing_rate" value="<?php echo $settings['ironing_rate']; ?>" step="0.01">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="bedding_rate">Bedding & Linens Rate (per item)</label>
                                    <input type="number" class="form-control" id="bedding_rate" name="bedding_rate" value="<?php echo $settings['bedding_rate']; ?>" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- Additional Service Rates -->
                                <h4>More Rates</h4>
                                <div class="form-group mb-3">
                                    <label for="dry_cleaning_rate">Dry Cleaning Rate (per item)</label>
                                    <input type="number" class="form-control" id="dry_cleaning_rate" name="dry_cleaning_rate" value="<?php echo $settings['dry_cleaning_rate']; ?>" step="0.01">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="stain_removal_rate">Stain Removal Rate (per item)</label>
                                    <input type="number" class="form-control" id="stain_removal_rate" name="stain_removal_rate" value="<?php echo $settings['stain_removal_rate']; ?>" step="0.01">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="specialty_fabric_rate">Specialty Fabric Rate (per kg)</label>
                                    <input type="number" class="form-control" id="specialty_fabric_rate" name="specialty_fabric_rate" value="<?php echo $settings['specialty_fabric_rate']; ?>" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <h4>Notification Settings</h4>
                        <div class="form-group mb-3">
                            <label for="sms_notifications">SMS Notifications</label>
                            <select class="form-control" id="sms_notifications" name="sms_notifications">
                                <option value="enabled" <?php echo isset($settings['sms_notifications']) && $settings['sms_notifications'] == 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="disabled" <?php echo isset($settings['sms_notifications']) && $settings['sms_notifications'] == 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email_notifications">Email Notifications</label>
                            <select class="form-control" id="email_notifications" name="email_notifications">
                                <option value="enabled" <?php echo isset($settings['email_notifications']) && $settings['email_notifications'] == 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="disabled" <?php echo isset($settings['email_notifications']) && $settings['email_notifications'] == 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                        </div>

                        <!-- Operational Hours -->
                        <h4>Operational Hours</h4>
                        <div class="form-group mb-3">
                            <label for="opening_time">Opening Time</label>
                            <input type="time" class="form-control" id="opening_time" name="opening_time" value="<?php echo $settings['opening_time']; ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="closing_time">Closing Time</label>
                            <input type="time" class="form-control" id="closing_time" name="closing_time" value="<?php echo $settings['closing_time']; ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Update All Settings</button>
                    </form>
                    <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
                        <div class="alert alert-success mt-3">Settings updated successfully!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer inclusion -->
    <?php include '../admin/publics/footer.php'; ?>

    <!-- Add any additional JS files -->
    <script src="path_to_jquery.js"></script> <!-- Replace with actual path -->
    <script src="path_to_bootstrap.js"></script> <!-- Replace with actual path -->
</body>
</html>
