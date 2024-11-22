<?php
require('../fpdf/fpdf.php'); 
require '../includes/rinseclean_lms.php'; 

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->Image('../assets/images/icons/laundry-machine.png', 10, 6, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(50);
        $this->Cell(0, 10, 'RinseClean Laundry Services', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(50);
        $this->Cell(0, 10, 'Laundry more, worry less.', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Business Address: Moi Drive, Nairobi', 0, 1, 'L');
        $this->Cell(0, 10, 'Generated On: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
        $this->Ln(5);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Add dynamic row
    function AddRow($data, $headers, $widths, $fill = false)
    {
        foreach ($data as $i => $value) {
            $this->Cell($widths[$i], 10, $value, 1, 0, 'C', $fill);
        }
        $this->Ln();
    }

    // Add paid stamp
    function AddPaidStamp($x, $y)
    {
        $this->SetTextColor(255, 0, 0);  // Red color
        $this->SetFont('Arial', 'B', 40); // Bold font, large size
        $this->SetXY($x, $y);
        $this->Cell(0, 10, 'PAID', 0, 1, 'C');
    }
}

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../public/login/login.php');
    exit();
}

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die('Order ID is missing.');
}

$order_id = $_GET['order_id'];

$sql = "SELECT order_id, customer_name, laundry_type, fabric_softener, pickup_time,
               status, cost, payment_status, special_instructions, weight 
        FROM orders 
        WHERE order_id = ? AND customer_name = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

$stmt->bind_param('ss', $order_id, $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die('Order not found.');
}

$pickup_time = date('F j, Y, g:i a', strtotime($order['pickup_time']));
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Header and Title
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Order Receipt", 0, 1, 'C');
$pdf->Ln(5);

// Table Headers
$headers = ['Order ID', 'Customer Name', 'Laundry Type', 'Fabric Softener'];
$widths = [50, 50, 50, 40];
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->AddRow($headers, $headers, $widths, true);

// Table Data
$pdf->SetFont('Arial', '', 10);
$order_data = [
    $order['order_id'],
    $order['customer_name'],
    $order['laundry_type'],
    $order['fabric_softener'] ?: 'N/A'
];
$pdf->AddRow($order_data, $headers, $widths);

// More Details
$details_headers = ['Pickup Time', 'Status', 'Payment Status'];
$details_widths = [50, 50, 40];
$pdf->AddRow($details_headers, $details_headers, $details_widths, true);

$details_data = [
    $pickup_time,
    $order['status'],
    $order['payment_status']
];
$pdf->AddRow($details_data, $details_headers, $details_widths);

// Add "Paid" Stamp if Payment is Made
if (strtolower($order['payment_status']) === 'paid') {
    $pdf->AddPaidStamp(130, 40); // Position the "PAID" stamp at x=130, y=40
}

// Cost Section
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Total Cost: Ksh ' . number_format($order['cost'], 2), 0, 1, 'L');
$pdf->Cell(0, 10, 'Special Instructions: ' . ($order['special_instructions'] ?: 'N/A'), 0, 1, 'L');
$pdf->Cell(0, 10, 'Weight: ' . ($order['weight'] ? number_format($order['weight'], 2) . ' kg' : 'N/A'), 0, 1, 'L');

// Footer Note
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 10, "Thank you for choosing RinseClean Laundry Services!", 0, 1, 'C');

$pdf->Output('I', 'Order_Receipt_' . $order['order_id'] . '.pdf');
?>
    