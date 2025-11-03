<?php
require('fpdf.php'); // Include FPDF Library
include 'connection.php';

session_start();
$employee_id = $_SESSION['employee_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Fetch Leave Data
    $query = "SELECT l.apply_date, l.start_date, l.end_date, lt.leave_type_name, 
              COALESCE(l.leave_status, 'Pending') AS status 
              FROM `leave` l
              JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id
              WHERE l.employee_id = '$employee_id'
              AND (l.apply_date BETWEEN '$from_date' AND '$to_date')
              ORDER BY l.apply_date DESC";
    
    $result = mysqli_query($con, $query);

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'Employee Leave Report', 1, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Apply Date', 1);
    $pdf->Cell(40, 10, 'Start Date', 1);
    $pdf->Cell(40, 10, 'End Date', 1);
    $pdf->Cell(40, 10, 'Leave Type', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(40, 10, $row['apply_date'], 1);
        $pdf->Cell(40, 10, $row['start_date'], 1);
        $pdf->Cell(40, 10, $row['end_date'], 1);
        $pdf->Cell(40, 10, $row['leave_type_name'], 1);
        $pdf->Cell(30, 10, $row['status'], 1);
        $pdf->Ln();
    }

    $pdf->Output();
}
?>
