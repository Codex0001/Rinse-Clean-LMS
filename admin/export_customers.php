<?php
session_start();
require '../includes/rinseclean_lms.php'; // Include your database connection

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../public/errors/403.php"); // Access denied if not admin
    exit();
}

// Fetch all customers from the database
$query = "SELECT * FROM customers ORDER BY created_at DESC";
$result = $conn->query($query);

// Handle CSV Export
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="customers.csv"');

    $output = fopen('php://output', 'w');
    // Write the header
    fputcsv($output, ['ID', 'Username', 'Email', 'Phone Number', 'Registration Date']);
    
    // Write data rows
    while ($customer = $result->fetch_assoc()) {
        fputcsv($output, [
            $customer['id'], 
            $customer['username'], 
            $customer['email'], 
            $customer['phone_number'], 
            date("Y-m-d", strtotime($customer['created_at']))
        ]);
    }
    fclose($output);
    exit();
}

// Handle PDF Export
if (isset($_POST['export_pdf'])) {
    require_once('../fpdf/fpdf.php'); // Include FPDF library

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

    // Create PDF instance
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    // Add table headers with styling
    $pdf->SetFillColor(200, 220, 255); // Light blue background
    $pdf->Cell(40, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Username', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Email', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Phone Number', 1, 0, 'C', true);
    $pdf->Ln(); // Line break

    // Add data rows
    $pdf->SetFont('Arial', '', 10);
    while ($customer = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $customer['id'], 1);
        $pdf->Cell(50, 10, $customer['username'], 1);
        $pdf->Cell(50, 10, $customer['email'], 1);
        $pdf->Cell(40, 10, $customer['phone_number'], 1);
        $pdf->Ln(); // Line break after each row
    }

    // Output the PDF to the browser
    $pdf->Output('I', 'customers.pdf');
    exit();
}
?>
