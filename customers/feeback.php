<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php'); // Redirect to login if not authorized
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

// Handle form submission for feedback
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $customer_name = $_SESSION['username'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];

    // Prepare and execute the SQL insert statement
    $sql = "INSERT INTO feedback (customer_name, email, rating, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssis', $customer_name, $email, $rating, $message);

    if ($stmt->execute()) {
        header("Location: feedback.php?success=Feedback submitted successfully!");
        exit();
    } else {
        $error_message = "Error submitting feedback: " . $stmt->error;
    }
}

// Fetch all feedback from the database
$sql = "SELECT customer_name, rating, message, created_at FROM feedback ORDER BY created_at DESC";
$result = $conn->query($sql);

// Store feedback in an array
$feedbacks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer | Feedback</title>
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
            <h1>Feedback</h1>
        </div>

        <!-- Feedback Form -->
        <form method="POST" action="feedback.php" class="mt-5">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address (optional)</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select id="rating" name="rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Feedback Message</label>
                <textarea class="form-control" id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <!-- Display Previous Feedback -->
        <div class="feedback-section mt-5">
            <h2 class="mb-4">Previous Feedback</h2>
            <div class="list-group">
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="list-group-item">
                        <h5><?php echo htmlspecialchars($feedback['customer_name']); ?> (<?php echo htmlspecialchars($feedback['rating']); ?> Stars)</h5>
                        <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                        <small><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($feedback['created_at']))); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
