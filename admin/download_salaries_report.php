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

// Add table headers with styling
$pdf->SetFillColor(200, 220, 255); // Light blue background
$pdf->Cell(40, 10, 'Reg No', 1, 0, 'C', true); // Changed 'Staff ID' to 'Reg No'
$pdf->Cell(50, 10, 'Name', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Salary Status', 1, 0, 'C', true); // Add salary status
$pdf->Cell(30, 10, 'Salary', 1, 1, 'C', true);

// Fetch salary data from the staff table
$query = "SELECT s.reg_number, s.name, s.salary_status, s.salary
          FROM staff s";
$result = $conn->query($query);

// Check for database query error
if ($result === false) {
    $pdf->Cell(0, 10, 'Error fetching data: ' . $conn->error, 1, 1, 'C');
} elseif ($result->num_rows > 0) {
    // Add data rows to the PDF
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['reg_number'], 1);
        $pdf->Cell(50, 10, $row['name'], 1);
        $pdf->Cell(40, 10, $row['salary_status'], 1);
        $pdf->Cell(30, 10, number_format($row['salary'], 2), 1, 1); // Display the salary
    }
} else {
    // No data message
    $pdf->Cell(0, 10, 'No records found', 1, 1, 'C');
}

// Output the PDF to the browser
$pdf->Output('I', 'salaries_report.pdf'); // 'D' forces download, change to 'I' for inline viewing
exit;
?>
