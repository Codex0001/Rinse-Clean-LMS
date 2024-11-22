<?php
ob_start(); // Start output buffering to prevent the "already output" error

session_start(); // Start the session to use $_SESSION variables

require('../fpdf/fpdf.php'); // Include the FPDF library
require '../includes/rinseclean_lms.php'; // Update with your actual DB connection file

// Extend the FPDF class for header and footer customization
class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Company branding
        $this->Image('../assets/images/icons/laundry-machine.png', 10, 6, 30); // Logo
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(50); // Move to the right
        $this->Cell(0, 10, 'RinseClean Laundry Services', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(50);
        $this->Cell(0, 10, 'Laundry more, worry less.', 0, 1, 'C');

        // Add space before the table
        $this->Ln(5);

        // Address and current date/time
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Business Address: Moi Drive, Nairobi', 0, 1, 'L');
        $this->Cell(0, 10, 'Report Generated On: ' . date('Y-m-d H:i:s'), 0, 1, 'L');

        // Add space before the table
        $this->Ln(5);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Create a new PDF instance
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Add a title
$pdf->Cell(200, 10, "Staff Orders Report", 0, 1, 'C');

// Set auto page breaks for better handling of long tables
$pdf->SetAutoPageBreak(true, 10); // Adjust the bottom margin for auto page breaks

// Fetch the orders assigned to the staff member
$staff_id = (int)$_SESSION['user_id']; // Assuming user_id is the same as staff_id
$sql = "SELECT orders.id, orders.order_id, orders.customer_name, orders.pickup_time, orders.laundry_type, orders.status, orders.fabric_softener, orders.payment_status, orders.weight, orders.special_instructions, orders.cost 
        FROM orders 
        WHERE staff_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$result = $stmt->get_result();

// Table header
$pdf->Ln(10);
$colWidths = [24, 30, 35, 24, 28, 24, 24]; // Array of column widths
$header = ['Order ID', 'Customer Name', 'Pickup Time', 'Laundry Type', 'Fabric Softener', 'Status', 'Cost'];

// Set header row
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($colWidths[$i], 10, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

// Add data to the table
while ($order = $result->fetch_assoc()) {
    $pdf->Cell($colWidths[0], 10, $order['order_id'], 1);
    $pdf->Cell($colWidths[1], 10, $order['customer_name'], 1);
    $pdf->Cell($colWidths[2], 10, $order['pickup_time'], 1);
    $pdf->Cell($colWidths[3], 10, $order['laundry_type'], 1);
    $pdf->Cell($colWidths[4], 10, $order['fabric_softener'], 1);
    $pdf->Cell($colWidths[5], 10, $order['status'], 1);
    $pdf->Cell($colWidths[6], 10, $order['cost'], 1);
    $pdf->Ln();
}

// Close the statement and result
$stmt->close();
$result->close();

// Output the PDF as a download
$pdf->Output('I', 'staff_orders_report.pdf'); // 'I' for inline display, 'D' for download
exit();
?>
