<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

require('../fpdf/fpdf.php');
include '../connection.php';

// Fetch holiday data
$sql = "SELECT * FROM holiday WHERE end_date >= CURDATE() ORDER BY start_date ASC";
$result = mysqli_query($con, $sql);

// Extend FPDF for header and footer
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 51, 102);
        
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(190, 10, "Holiday List Report", 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Table Header
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(30, 10, 'Holiday ID', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Holiday Name', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Start Date', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'End Date', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Days', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Year', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(255, 255, 255);

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(30, 10, $row['holiday_id'], 1);
    $pdf->Cell(50, 10, $row['holiday_name'], 1);
    $pdf->Cell(30, 10, $row['start_date'], 1);
    $pdf->Cell(30, 10, $row['end_date'], 1);
    $pdf->Cell(20, 10, $row['days'], 1);
    $pdf->Cell(20, 10, $row['year'], 1, 1);
}

$pdf->Output('D', 'Holiday_List.pdf');
?>
