<?php
require_once 'path/to/twilio/autoload.php'; // Load Twilio SDK
use Twilio\Rest\Client;

// Twilio Credentials
define('TWILIO_SID', 'your_twilio_sid');
define('TWILIO_AUTH_TOKEN', 'your_twilio_auth_token');
define('TWILIO_PHONE_NUMBER', 'your_twilio_phone_number');

// Function to send SMS
function sendSMS($phoneNumber, $message) {
    $client = new Client(TWILIO_SID, TWILIO_AUTH_TOKEN);

    try {
        $client->messages->create(
            $phoneNumber,
            [
                'from' => TWILIO_PHONE_NUMBER,
                'body' => $message
            ]
        );
        return true;
    } catch (Exception $e) {
        error_log("SMS Error: " . $e->getMessage());
        return false;
    }
}

// Assuming order status update comes from URL params or session
$orderId = $_GET['order_id'];
$customerName = $_GET['customer_name'];
$phoneNumber = $_GET['phone_number'];
$orderStatus = $_GET['order_status']; // "In Progress" or "Completed"

// Send order status update SMS
$smsMessage = "Hi $customerName, your order #$orderId status is now updated to '$orderStatus'. Thank you for choosing us!";
$smsSent = sendSMS($phoneNumber, $smsMessage);

// If SMS was sent successfully, show confirmation
if ($smsSent) {
    echo "<h1>Order Status Update</h1>";
    echo "<p>Your order #$orderId status has been updated to '$orderStatus'. An SMS notification has been sent to your phone number.</p>";
    echo "<a href='order_details.php?order_id=$orderId'>View Order Details</a>";
} else {
    echo "<h1>Status Update Failed</h1>";
    echo "<p>There was an error sending the status update SMS. Please try again later.</p>";
}
?>
