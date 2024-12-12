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
    $customer_id = $_SESSION['user_id']; // Get customer ID from session
    $order_id = $_POST['order_id']; // Assuming order_id is passed as a hidden field in the form
    $score = $_POST['rating'];
    $comments = $_POST['message'];
    $nps_score = $_POST['nps_score'];
    $nps_reason = $_POST['nps_reason'];
    $cleanliness_rating = $_POST['cleanliness_rating'];
    $timeliness_rating = $_POST['timeliness_rating'];
    $customer_service_rating = $_POST['customer_service_rating'];

    // Validate ratings and feedback
    if (empty($score) || empty($comments) || empty($nps_score) || empty($nps_reason) || empty($cleanliness_rating) || empty($timeliness_rating) || empty($customer_service_rating)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare and execute the SQL insert statement
        $sql = "INSERT INTO feedback (customer_id, order_id, score, comments, nps_score, nps_reason, cleanliness_rating, timeliness_rating, customer_service_rating) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiisisiii', $customer_id, $order_id, $score, $comments, $nps_score, $nps_reason, $cleanliness_rating, $timeliness_rating, $customer_service_rating); // Adjusted to match your database structure

        if ($stmt->execute()) {
            header("Location: feedback.php?success=Feedback submitted successfully!");
            exit();
        } else {
            $error_message = "Error submitting feedback: " . $stmt->error;
        }
    }
}

// Fetch order details for the specific order_id (if available)
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$order_details = [];
if ($order_id) {
    $order_sql = "SELECT order_id, laundry_type, status, cost FROM orders WHERE order_id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param('i', $order_id); // Assuming order_id is an integer
    $order_stmt->execute();
    $result = $order_stmt->get_result();
    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();
    }
}

// Fetch all feedback from the database
$sql = "SELECT f.score, f.comments, f.created_at, c.username, o.order_id, f.nps_score, f.nps_reason, f.cleanliness_rating, f.timeliness_rating, f.customer_service_rating
        FROM feedback f 
        JOIN customers c ON f.customer_id = c.id 
        JOIN orders o ON f.order_id = o.order_id 
        ORDER BY f.created_at DESC";
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
    <style>
        .dashboard {
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../customers/public/sidebar.php'; ?>
</div>

<section class="home">
    <div class="dashboard">
        <div class="header-strip">
            <h1>Customer Feedback</h1>
        </div>

        <!-- Feedback Form -->
        <form method="POST" action="feedback.php" class="mt-5">
            <input type="hidden" name="order_id" value="<?php echo isset($order_details['order_id']) ? htmlspecialchars($order_details['order_id']) : ''; ?>">

            <?php if (!empty($order_details)): ?>
                <div class="order-details">
                    <h5>Order Details</h5>
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_details['order_id']); ?></p>
                    <p><strong>Laundry Type:</strong> <?php echo htmlspecialchars($order_details['laundry_type']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order_details['status']); ?></p>
                    <p><strong>Cost:</strong> <?php echo htmlspecialchars($order_details['cost']); ?></p>
                </div>
            <?php endif; ?>

            <!-- NPS Question Section -->
            <div class="mb-3">
                <label for="nps_score" class="form-label">On a scale of 0 to 10, how likely are you to recommend our laundry service to friends or family?</label>
                <input type="number" id="nps_score" name="nps_score" class="form-control" min="0" max="10" required>
                <div class="form-text">0 = Not at all likely, 10 = Extremely likely</div>
            </div>

            <!-- NPS Follow-up Question -->
            <div class="mb-3">
                <label for="nps_reason" class="form-label">What was the main reason for your rating?</label>
                <textarea id="nps_reason" name="nps_reason" class="form-control" required></textarea>
            </div>

            <!-- Service Rating Questions -->
            <div class="mb-3">
                <label for="cleanliness_rating" class="form-label">How satisfied are you with the cleanliness of your laundry?</label>
                <select id="cleanliness_rating" name="cleanliness_rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 (Very Dissatisfied)</option>
                    <option value="2">2 (Dissatisfied)</option>
                    <option value="3">3 (Neutral)</option>
                    <option value="4">4 (Satisfied)</option>
                    <option value="5">5 (Very Satisfied)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="timeliness_rating" class="form-label">How satisfied are you with the timeliness of delivery?</label>
                <select id="timeliness_rating" name="timeliness_rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 (Very Dissatisfied)</option>
                    <option value="2">2 (Dissatisfied)</option>
                    <option value="3">3 (Neutral)</option>
                    <option value="4">4 (Satisfied)</option>
                    <option value="5">5 (Very Satisfied)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="customer_service_rating" class="form-label">How would you rate the quality of customer service?</label>
                <select id="customer_service_rating" name="customer_service_rating" class="form-select" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 (Very Dissatisfied)</option>
                    <option value="2">2 (Dissatisfied)</option>
                    <option value="3">3 (Neutral)</option>
                    <option value="4">4 (Satisfied)</option>
                    <option value="5">5 (Very Satisfied)</option>
                </select>
            </div>

            <!-- General Feedback Question -->
            <div class="mb-3">
                <label for="message" class="form-label">What can we do to improve our service?</label>
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
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Username</th>
                            <th>Score</th>
                            <th>Comments</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['score']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

</body>
</html>
