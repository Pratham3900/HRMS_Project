<?php
session_start();
require('../fpdf/fpdf.php'); // Include FPDF Library
include '../connection.php'; // Include Database Connection

class PDF extends FPDF {
    // Page Header
    function Header() {
        // Background color
        $this->SetFillColor(50, 50, 150); // Dark Blue
        $this->Rect(0, 0, 210, 40, 'F'); // Header background
    
        // Logo (optional)
        // $this->Image('../logo.png', 10, 8, 30); 
    
        // Header Title
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(255, 255, 255); // White Text
        $this->Cell(190, 15, 'Notice Report', 0, 1, 'C');
    
        // **Add extra space to prevent table from overlapping**
        $this->Ln(20); // Increase spacing
    
        // Sub-header Line
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(100, 149, 237); // Cornflower Blue
        $this->SetTextColor(255, 255, 255);
        $this->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(55, 10, 'Title', 1, 0, 'C', true);
        $this->Cell(90, 10, 'Description', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Date', 1, 1, 'C', true);
    }
    
    // Page Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'C');
    }
}

// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Fetch Notices from Database
$sql = "SELECT * FROM notice ORDER BY date DESC";
$result = mysqli_query($con, $sql);

// Default Text Color
$pdf->SetTextColor(0, 0, 0);
$fill = false; // Alternate row colors

while ($row = mysqli_fetch_assoc($result)) {
    // Alternating Row Colors
    if ($fill) {
        $pdf->SetFillColor(224, 235, 255); // Light Blue
    } else {
        $pdf->SetFillColor(255, 255, 255); // White
    }

    $pdf->Cell(20, 10, $row['notice_id'], 1, 0, 'C', $fill);
    $pdf->Cell(55, 10, $row['title'], 1, 0, 'C', $fill);
    $pdf->Cell(90, 10, substr($row['description'], 0, 50) . '...', 1, 0, 'C', $fill);
    $pdf->Cell(25, 10, $row['date'], 1, 1, 'C', $fill);

    $fill = !$fill; // Toggle row colors
}

// Output PDF as Download
$pdf->Output('D', 'notice_report.pdf'); // Forces download
?>
