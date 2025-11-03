<?php
require('fpdf.php'); // Include FPDF Library
include 'connection.php';
session_start();

if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    die("Access denied!");
}

$employee_id = $_SESSION['employee_id'];
$fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

// Fetch attendance records
$sql = "SELECT e.employee_name, a.date, a.sign_in, a.sign_out, a.working_hours
        FROM attendance a
        JOIN employee e ON a.employee_id = e.employee_id
        WHERE a.employee_id = '$employee_id'
        AND a.date BETWEEN '$fromDate' AND '$toDate'
        ORDER BY a.date ASC";

$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("No attendance records found.");
}

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Title
$pdf->Cell(190, 10, 'Attendance Report', 1, 1, 'C');

// Fetch employee name
$row = mysqli_fetch_assoc($result);
$employee_name = $row['employee_name'];

// Display Employee Name
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 10, "Employee: $employee_name", 0, 1, 'L');

// Display Date Range
$pdf->Cell(190, 10, "From: $fromDate  To: $toDate", 0, 1, 'L');

// Table Headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(40, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Sign In', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Sign Out', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Hours Worked', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);

// Table Data
mysqli_data_seek($result, 0); // Reset result pointer
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(40, 10, $row['date'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['sign_in'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['sign_out'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['working_hours'], 1, 1, 'C');
}

// Output PDF
$pdf->Output('D', 'Attendance_Report.pdf');
?>
