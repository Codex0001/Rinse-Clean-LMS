<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php');
    exit();
}

// Database connection
require_once '../includes/rinseclean_lms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['user_id'];
    $laundry_type = $_POST['laundry_type'];
    $fabric_softener = $_POST['fabric_softener'];
    $special_instructions = $_POST['special_instructions'];
    $pickup_time = $_POST['pickup_time'];

    // SQL to insert new order
    $sql = "INSERT INTO orders (customer_name, laundry_type, fabric_softener, pickup_time, special_instructions, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $_SESSION['username'], $laundry_type, $fabric_softener, $pickup_time, $special_instructions);
    $stmt->execute();

    // Redirect or show a success message
    header('Location: orders.php?success=1');
    exit();
}
