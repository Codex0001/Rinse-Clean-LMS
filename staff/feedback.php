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

// Fetch all feedback from the database
$feedback_sql = "SELECT feedback.id, customers.username AS customer_name, feedback.comments, feedback.score, feedback.created_at 
                 FROM feedback 
                 JOIN customers ON feedback.customer_id = customers.id 
                 ORDER BY feedback.created_at DESC";

$feedback_result = $conn->query($feedback_sql);

// Store the feedback in an array
$feedbacks = [];
while ($row = $feedback_result->fetch_assoc()) {
    $feedbacks[] = $row;
}

// Update feedback status if needed
if (isset($_POST['update_feedback'])) {
    $feedback_id = $_POST['feedback_id'];
    $new_status = $_POST['status'];

    // Update the feedback status
    $update_sql = "UPDATE feedback SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('si', $new_status, $feedback_id);

    if ($update_stmt->execute()) {
        $message = "Feedback status updated successfully.";
    } else {
        $message = "Error updating feedback status. Please try again.";
    }

    // Clear cache and refresh feedbacks
    header("Location: feedback.php?message=" . urlencode($message)); // Redirect to feedback page with a message
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Feedback</title>
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
    <div class="container mt-5">
        <h1>Customer Feedback</h1>

        <!-- Success/Error Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Feedback Table Section -->
        <div class="feedback-section mt-5">
            <h2 class="mb-4">All Feedback</h2>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Comments</th>
                        <th scope="col">Score</th>
                        <th scope="col">Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No feedback found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['score']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($feedback['created_at']))); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <select name="status" class="form-select" required>
                                        <option value="">Change Status</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                    </select>
                                    <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                    <button type="submit" name="update_feedback" class="btn btn-success mt-1">Update</button>
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
