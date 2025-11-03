<?php
session_start();
require('../fpdf/fpdf.php'); // Include FPDF library
include '../connection.php'; // Include database connection

class PDF extends FPDF {
    // Add Colors
    function Header() {
        $this->SetFillColor(50, 50, 150); // Dark Blue Background
        $this->SetTextColor(255, 255, 255); // White Text
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(190, 10, 'Leave Report', 1, 1, 'C', true); // Title

        // Column Titles
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(100, 149, 237); // Cornflower Blue
        $this->SetTextColor(255, 255, 255); // White Text
        
        $this->Cell(25, 10, 'Leave ID', 1, 0, 'C', true);
        $this->Cell(45, 10, 'Employee Name', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Leave Type', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Start Date', 1, 0, 'C', true);
        $this->Cell(25, 10, 'End Date', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Status', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'C');
    }
}

// Create PDF instance
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Fetch leave data
$sql = "SELECT l.leave_id, e.employee_name, lt.leave_type_name, l.start_date, l.end_date, l.leave_status 
        FROM `leave` l
        JOIN employee e ON l.employee_id = e.employee_id
        JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id";
$result = mysqli_query($con, $sql);

// Set default text color
$pdf->SetTextColor(0, 0, 0);

// Fill table with leave data
$fill = false; // Alternate row colors
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(224, 235, 255); // Light blue row color
    $pdf->Cell(25, 10, $row['leave_id'], 1, 0, 'C', $fill);
    $pdf->Cell(45, 10, $row['employee_name'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 10, $row['leave_type_name'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 10, $row['start_date'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 10, $row['end_date'], 1, 0, 'C', $fill);

    // Leave Status - Color Coding
    if ($row['leave_status'] == 'approved') {
        $pdf->SetFillColor(50, 205, 50); // Green
    } elseif ($row['leave_status'] == 'rejected') {
        $pdf->SetFillColor(255, 69, 0); // Red
    } else {
        $pdf->SetFillColor(255, 215, 0); // Yellow
    }
    $pdf->Cell(35, 10, ucfirst($row['leave_status']), 1, 1, 'C', true);

    $fill = !$fill; // Alternate row colors
}

// Output PDF
$pdf->Output('D', 'leave_report.pdf'); // Forces download
?>
