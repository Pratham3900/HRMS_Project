<?php
require('../fpdf/fpdf.php'); // Include FPDF
include '../connection.php'; // Include database connection

class PDF extends FPDF {
    function Header() {
        // Title
        $this->SetFont('Arial', 'B', 14);
        $this->SetFillColor(100, 149, 237); // Cornflower Blue
        $this->SetTextColor(255);
        $this->Cell(190, 10, 'Employee List', 1, 1, 'C', true);
        $this->Ln(5);
    }
}

// Create a new PDF instance
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

// Table Headers with Color
$pdf->SetFillColor(0, 102, 204); // Dark Blue
$pdf->SetTextColor(255); // White text
$pdf->Cell(20, 10, 'Emp ID', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Name', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Email', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Contact', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Department', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Designation', 1, 1, 'C', true);

// Reset text color for content
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);

// Fetch Data from Database
$sql = "SELECT e.employee_id, e.employee_name, e.email, e.contact, 
        d.department_name, des.designation_name
        FROM employee e
        LEFT JOIN department d ON e.department_id = d.department_id
        LEFT JOIN designation des ON e.designation_id = des.designation_id";

$result = mysqli_query($con, $sql);
$fill = false; // Alternate row colors

// Display Data in Table
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(224, 235, 255); // Light Blue for alternate rows
    $pdf->Cell(20, 10, $row['employee_id'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 10, $row['employee_name'], 1, 0, 'L', $fill);
    $pdf->Cell(50, 10, substr($row['email'], 0, 30), 1, 0, 'L', $fill);
    $pdf->Cell(25, 10, $row['contact'], 1, 0, 'C', $fill);
    $pdf->Cell(30, 10, $row['department_name'], 1, 0, 'L', $fill);
    $pdf->Cell(30, 10, substr($row['designation_name'], 0, 15), 1, 1, 'L', $fill);
    
    $fill = !$fill; // Toggle row color
}

// Output the PDF
$pdf->Output();
?>
