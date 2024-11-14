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

// Check if registration is successful
if (isset($_GET['registration_status']) && $_GET['registration_status'] == 'success') {
    // Get the customer info from the URL parameters or session
    $customerName = $_GET['customer_name'];
    $phoneNumber = $_GET['phone_number']; // Assuming phone number is passed in the URL or session

    // Send registration success SMS
    $smsMessage = "Welcome to Rinse Clean LMS, $customerName! Your account is registered successfully.";
    $smsSent = sendSMS($phoneNumber, $smsMessage);

    // If SMS was sent successfully, show a success message
    if ($smsSent) {
        echo "<h1>Registration Successful</h1>";
        echo "<p>Thank you for registering, $customerName! A confirmation SMS has been sent to your phone number.</p>";
        echo "<a href='login.php'>Click here to log in</a>";
    } else {
        echo "<h1>Registration Failed</h1>";
        echo "<p>There was an error sending the confirmation SMS. Please try again later.</p>";
    }
} else {
    // If registration status is not set, redirect to registration page or show an error
    header('Location: registration.php');
    exit();
}
?>
