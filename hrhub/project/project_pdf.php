<?php
session_start();
require('../fpdf/fpdf.php'); // Include FPDF library
include '../connection.php'; // Include database connection

class PDF extends FPDF {
    // Header
    function Header() {
        $this->SetFillColor(50, 50, 150); // Dark Blue
        $this->SetTextColor(255, 255, 255); // White
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(190, 12, 'Project Report', 1, 1, 'C', true);

        // Table Headers
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(100, 149, 237); // Cornflower Blue
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(55, 10, 'Title', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Employees', 1, 0, 'C', true); // Increased width for better visibility
        $this->Cell(30, 10, 'Status', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Start Date', 1, 0, 'C', true);
        $this->Cell(30, 10, 'End Date', 1, 1, 'C', true);
    }

    // Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'C');
    }
}

// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Fetch project data
$sql = "SELECT p.project_id, p.project_title, p.status, p.start_date, p.end_date, 
        GROUP_CONCAT(e.employee_name SEPARATOR ', ') AS employees
        FROM project p
        LEFT JOIN project_employees pe ON p.project_id = pe.project_id
        LEFT JOIN employee e ON pe.employee_id = e.employee_id
        GROUP BY p.project_id
        ORDER BY p.start_date DESC";
$result = mysqli_query($con, $sql);

// Set default text color
$pdf->SetTextColor(0, 0, 0);
$fill = false; // Alternate row colors

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(224, 235, 255); // Light blue row background

    // Convert employee names to a **newline-separated** list instead of commas
    $employees = $row['employees'] ? str_replace(', ', "\n", $row['employees']) : 'N/A';

    // Calculate max row height based on the number of employees
    $linesEmployees = substr_count($employees, "\n") + 1;
    $rowHeight = max(10, $linesEmployees * 5); // Adjusted row height dynamically

    $pdf->Cell(20, $rowHeight, $row['project_id'], 1, 0, 'C', $fill);
    $pdf->Cell(55, $rowHeight, $row['project_title'], 1, 0, 'L', $fill);
    
    // Use MultiCell for employees to show them line by line
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(40, 5, $employees, 1, 'L', $fill);
    $pdf->SetXY($x + 40, $y); // Move to the next column manually

    // Status Color Coding
    if ($row['status'] == 'Planned') {
        $pdf->SetFillColor(255, 165, 0); // Orange
    } elseif ($row['status'] == 'In Process') {
        $pdf->SetFillColor(30, 144, 255); // Blue
    } elseif ($row['status'] == 'Completed') {
        $pdf->SetFillColor(50, 205, 50); // Green
    } else {
        $pdf->SetFillColor(255, 215, 0); // Yellow
    }
    
    $pdf->Cell(30, $rowHeight, ucfirst($row['status']), 1, 0, 'C', true);
    $pdf->Cell(25, $rowHeight, $row['start_date'], 1, 0, 'C', $fill);
    $pdf->Cell(30, $rowHeight, $row['end_date'], 1, 1, 'C', $fill);

    $fill = !$fill; // Alternate row colors
}

// Output PDF
$pdf->Output('D', 'project_report.pdf'); // Forces download
?>
