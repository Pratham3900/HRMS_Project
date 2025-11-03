<?php
session_start();
require('../fpdf/fpdf.php'); // Include FPDF library
include '../connection.php'; // Database connection

// Create PDF Object
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Employee Attendance Report', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 7, 'Date: ' . date('Y-m-d'), 0, 1, 'C');
$pdf->Ln(5); // Line break

$current_date = date('Y-m-d');

// ------------------------- Present Employees Section -------------------------
$pdf->SetFillColor(0, 102, 255); // Blue background
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Present Employees', 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0); // Reset to black
$pdf->Cell(30, 10, 'Att. ID', 1);
$pdf->Cell(50, 10, 'Employee Name', 1);
$pdf->Cell(30, 10, 'Date', 1);
$pdf->Cell(30, 10, 'Sign In', 1);
$pdf->Cell(30, 10, 'Sign Out', 1);
$pdf->Cell(20, 10, 'Hours', 1);
$pdf->Ln(); // Move to next line

$pdf->SetFont('Arial', '', 10);
$sql_present = "SELECT e.employee_id, e.employee_name, a.attendance_id, a.date, 
        a.sign_in, a.sign_out, a.working_hours 
        FROM attendance a
        LEFT JOIN employee e ON e.employee_id = a.employee_id
        WHERE a.date = '$current_date'";

$result_present = mysqli_query($con, $sql_present);

if (mysqli_num_rows($result_present) > 0) {
    while ($row = mysqli_fetch_assoc($result_present)) {
        $pdf->Cell(30, 10, $row['attendance_id'], 1);
        $pdf->Cell(50, 10, $row['employee_name'], 1);
        $pdf->Cell(30, 10, $row['date'], 1);
        $pdf->Cell(30, 10, $row['sign_in'], 1);
        $pdf->Cell(30, 10, $row['sign_out'], 1);
        $pdf->Cell(20, 10, $row['working_hours'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No employees present', 1, 1, 'C');
}

// ------------------------- Absent Employees Section -------------------------
$pdf->Ln(5); // Space between tables
$pdf->SetFillColor(255, 0, 0); // Red background
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Absent Employees', 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0); // Reset to black
$pdf->Cell(50, 10, 'Employee ID', 1);
$pdf->Cell(140, 10, 'Employee Name', 1);
$pdf->Ln(); // Move to next line

$pdf->SetFont('Arial', '', 10);
$sql_absent = "
SELECT e.employee_id, e.employee_name 
FROM employee e
LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date = '$current_date'
LEFT JOIN `leave` l ON e.employee_id = l.employee_id 
    AND '$current_date' BETWEEN l.start_date AND l.end_date
WHERE a.employee_id IS NULL OR l.employee_id IS NOT NULL
ORDER BY e.employee_name";

$result_absent = mysqli_query($con, $sql_absent);

if (mysqli_num_rows($result_absent) > 0) {
    while ($row = mysqli_fetch_assoc($result_absent)) {
        $pdf->Cell(50, 10, $row['employee_id'], 1);
        $pdf->Cell(140, 10, $row['employee_name'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No absent employees', 1, 1, 'C');
}

// Output PDF
$pdf->Output('D', 'attendance_report.pdf'); // Forces download
exit();
?>
