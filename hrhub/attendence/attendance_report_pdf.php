<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

require('../fpdf/fpdf.php');
include '../connection.php';

// Validate parameters
if (!isset($_GET['employee_name'], $_GET['from_date'], $_GET['to_date'])) {
    die("Invalid Request: Missing Parameters.");
}

$employeeName = trim($_GET['employee_name']);
$fromDate = trim($_GET['from_date']);
$toDate = trim($_GET['to_date']);

// Fetch employee ID
$empQuery = "SELECT employee_id FROM employee WHERE employee_name = ?";
$stmt = mysqli_prepare($con, $empQuery);
mysqli_stmt_bind_param($stmt, "s", $employeeName);
mysqli_stmt_execute($stmt);
$empResult = mysqli_stmt_get_result($stmt);
$empRow = mysqli_fetch_assoc($empResult);
mysqli_stmt_close($stmt);

if (!$empRow) {
    die("Employee not found.");
}

$employeeId = $empRow['employee_id'];

// Fetch attendance data
$sql = "SELECT e.employee_name, a.date, a.sign_in, a.sign_out, a.working_hours
        FROM attendance a
        JOIN employee e ON a.employee_id = e.employee_id
        WHERE a.employee_id = ? 
        AND a.date BETWEEN ? AND ?
        ORDER BY a.date ASC";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sss", $employeeId, $fromDate, $toDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Extend FPDF Class for Header & Footer
class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('compnay_name.png', 10, 6, 30); // Adjust the path to your logo
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(190, 10, "Tech Danawala", 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(190, 10, "Attendance Report", 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Initialize PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Employee Info
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(50, 10, "Employee Name:", 1, 0, 'L', true);
$pdf->Cell(140, 10, $employeeName, 1, 1, 'L');
$pdf->Cell(50, 10, "From Date:", 1, 0, 'L', true);
$pdf->Cell(140, 10, $fromDate, 1, 1, 'L');
$pdf->Cell(50, 10, "To Date:", 1, 0, 'L', true);
$pdf->Cell(140, 10, $toDate, 1, 1, 'L');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(40, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Sign In', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Sign Out', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Hours Worked', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(255, 255, 255);
$rowExists = false;

// Fill Table Data
while ($row = mysqli_fetch_assoc($result)) {
    $rowExists = true;
    $pdf->Cell(40, 10, $row['date'], 1);
    $pdf->Cell(40, 10, $row['sign_in'], 1);
    $pdf->Cell(40, 10, $row['sign_out'], 1);
    $pdf->Cell(40, 10, $row['working_hours'], 1, 1);
}

// No Records Found Message
if (!$rowExists) {
    $pdf->Cell(160, 10, 'No attendance records found.', 1, 1, 'C');
}

// Output PDF
$pdf->Output();
?>
