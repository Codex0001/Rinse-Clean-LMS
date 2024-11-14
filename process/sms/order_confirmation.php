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

// Assuming order data comes from URL params or session
$orderId = $_GET['order_id'];
$customerName = $_GET['customer_name'];
$phoneNumber = $_GET['phone_number'];
$laundryType = $_GET['laundry_type'];
$pickupTime = $_GET['pickup_time'];

// Send order confirmation SMS
$smsMessage = "Hi $customerName, your order #$orderId for $laundryType has been placed successfully. Pickup time: $pickupTime.";
$smsSent = sendSMS($phoneNumber, $smsMessage);

// If SMS was sent successfully, show confirmation
if ($smsSent) {
    echo "<h1>Order Confirmation</h1>";
    echo "<p>Your order #$orderId has been placed successfully. A confirmation SMS has been sent to your phone number.</p>";
    echo "<a href='order_details.php?order_id=$orderId'>View Order Details</a>";
} else {
    echo "<h1>Order Failed</h1>";
    echo "<p>There was an error sending the confirmation SMS. Please try again later.</p>";
}
?>