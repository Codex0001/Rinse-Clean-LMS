<?php
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
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(50); // Move to the right
        $this->Cell(0, 10, 'RinseClean Laundry Services', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(50);
        $this->Cell(0, 10, 'Laundry more, worry less.', 0, 1, 'C');

        // Add space before the table
        $this->Ln(5);

        // Address and current date/time
        $this->SetFont('Arial', '', 9);
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
$pdf->SetFont('Arial', '', 8); // Reduce font size to fit content

// Set Auto Page Break
$pdf->SetAutoPageBreak(true, 10); // Adds 10mm margin at the bottom for footer

// Add table headers with styling
$pdf->SetFillColor(200, 220, 255); // Light blue background
$pdf->Cell(25, 7, 'Order ID', 1, 0, 'C', true); 
$pdf->Cell(40, 7, 'Customer Name', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Laundry Type', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Status', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Cost', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Payment Status', 1, 1, 'C', true);

// Fetch order data from the orders table
$query = "SELECT order_id, customer_name, laundry_type, status, cost, payment_status
          FROM orders ORDER BY order_id DESC";
$result = $conn->query($query);

// Check for database query error
if ($result === false) {
    $pdf->Cell(0, 10, 'Error fetching data: ' . $conn->error, 1, 1, 'C');
} elseif ($result->num_rows > 0) {
    // Add data rows to the PDF
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(25, 7, $row['order_id'], 1);
        $pdf->Cell(40, 7, $row['customer_name'], 1);
        $pdf->Cell(35, 7, $row['laundry_type'], 1);
        $pdf->Cell(30, 7, $row['status'], 1);
        $pdf->Cell(30, 7, number_format($row['cost'], 2), 1);
        $pdf->Cell(30, 7, $row['payment_status'], 1, 1);
    }
} else {
    // No data message
    $pdf->Cell(0, 10, 'No records found', 1, 1, 'C');
}

// Output the PDF to the browser
$pdf->Output('I', 'orders_report.pdf'); // 'D' forces download, change to 'I' for inline viewing
exit;
?>
