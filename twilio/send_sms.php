<?php
use Twilio\Rest\Client;

function sendCustomSMS($phone_number, $scenario, $data = []) {
    // Twilio credentials
    $sid = 'AC1992b614cfad63aeb10b1906789a68a5';
    $token = '1038b5c5006c9cdc7f53614e6cca4739';
    $twilio_number = '+18178139263';

    // Message template for different scenarios
    switch ($scenario) {
        case 'registration_success':
            $message = "Welcome, {$data['username']}! Your account has been successfully created.";
            break;
        case 'order_status_update':
            $message = "Dear {$data['username']}, your order {$data['order_id']} is now {$data['order_status']}. Thank you for choosing us!";
            break;
        case 'pickup_reminder':
            $message = "Reminder: Your pickup is scheduled for {$data['pickup_time']}. Please be ready. Thank you!";
            break;
        default:
            $message = "Hello {$data['username']}, this is a message from RinseClean Laundry Services.";
            break;
    }

    try {
        $client = new Client($sid, $token);
        $client->messages->create(
            $phone_number, // The customer phone number
            array(
                'from' => $twilio_number,
                'body' => $message
            )
        );
        return true; // Success
    } catch (Exception $e) {
        error_log("Error sending SMS: " . $e->getMessage());
        return false; // Failure
    }
}
