<?php
require('../fpdf/fpdf.php');
include '../connection.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->SetFillColor(100, 149, 237);
        $this->SetTextColor(255);
        $this->Cell(0, 10, 'Payroll Payment Summary', 0, 1, 'C', true);
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Query for Paid Employees
$sql_paid = "SELECT 
    e.employee_name,
    COALESCE(e.salary, ds.base_salary) AS salary,
    d.department_name,
    ds.designation_name,
    p.razorpay_payment_id,
    p.payment_mode
FROM payroll p 
JOIN employee e ON p.employee_id = e.employee_id 
JOIN department d ON e.department_id = d.department_id
JOIN designation ds ON e.designation_id = ds.designation_id
WHERE p.razorpay_payment_id IS NOT NULL
AND MONTH(p.pay_date) = MONTH(CURRENT_DATE())
AND YEAR(p.pay_date) = YEAR(CURRENT_DATE())";

$result_paid = mysqli_query($con, $sql_paid);
$totalPaidSalary = 0;
$paidCount = 0;

$pdf->SetFillColor(50, 205, 50);
$pdf->SetTextColor(255);
$pdf->Cell(0, 10, 'Paid Employees', 0, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 10, 'Employee Name', 1, 0, 'C');
$pdf->Cell(30, 10, 'Department', 1, 0, 'C');
$pdf->Cell(35, 10, 'Designation', 1, 0, 'C');
$pdf->Cell(25, 10, 'Salary', 1, 0, 'C');
$pdf->Cell(40, 10, 'Razorpay ID', 1, 0, 'C');
$pdf->Cell(15, 10, 'Mode', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

while ($row = mysqli_fetch_assoc($result_paid)) {
    $pdf->Cell(45, 10, $row['employee_name'], 1);
    $pdf->Cell(30, 10, $row['department_name'], 1);
    $pdf->Cell(35, 10, $row['designation_name'], 1);
    $pdf->Cell(25, 10, number_format($row['salary'], 2), 1);
    $pdf->Cell(40, 10, $row['razorpay_payment_id'], 1);
    $pdf->Cell(15, 10, $row['payment_mode'], 1, 1);
    $totalPaidSalary += $row['salary'];
    $paidCount++;
}

$pdf->SetFillColor(173, 216, 230);
$pdf->Cell(135, 10, 'Total Paid Employees:', 1, 0, 'C', true);
$pdf->Cell(55, 10, $paidCount, 1, 1, 'C', true);
$pdf->Cell(135, 10, 'Total Paid Salary:', 1, 0, 'C', true);
$pdf->Cell(55, 10, number_format($totalPaidSalary, 2), 1, 1, 'C', true);
$pdf->Ln(10);

// Query for Unpaid Employees
$sql_unpaid = "
  SELECT e.employee_name, COALESCE(e.salary, ds.base_salary) AS salary, d.department_name, ds.designation_name 
  FROM employee e
  JOIN department d ON e.department_id = d.department_id
  JOIN designation ds ON e.designation_id = ds.designation_id
  WHERE NOT EXISTS (
      SELECT 1 FROM payroll p 
      WHERE p.employee_id = e.employee_id 
      AND p.razorpay_payment_id IS NOT NULL
      AND MONTH(p.pay_date) = MONTH(CURRENT_DATE())
      AND YEAR(p.pay_date) = YEAR(CURRENT_DATE())
  )";

$result_unpaid = mysqli_query($con, $sql_unpaid);
$totalUnpaidSalary = 0;
$unpaidCount = 0;

$pdf->SetFillColor(220, 20, 60);
$pdf->SetTextColor(255);
$pdf->Cell(0, 10, 'Unpaid Employees', 0, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(55, 10, 'Employee Name', 1, 0, 'C');
$pdf->Cell(40, 10, 'Department', 1, 0, 'C');
$pdf->Cell(40, 10, 'Designation', 1, 0, 'C');
$pdf->Cell(55, 10, 'Salary', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

while ($row = mysqli_fetch_assoc($result_unpaid)) {
    $pdf->Cell(55, 10, $row['employee_name'], 1);
    $pdf->Cell(40, 10, $row['department_name'], 1);
    $pdf->Cell(40, 10, $row['designation_name'], 1);
    $pdf->Cell(55, 10, number_format($row['salary'], 2), 1, 1);
    $totalUnpaidSalary += $row['salary'];
    $unpaidCount++;
}

$pdf->SetFillColor(255, 182, 193);
$pdf->Cell(135, 10, 'Total Unpaid Employees:', 1, 0, 'C', true);
$pdf->Cell(55, 10, $unpaidCount, 1, 1, 'C', true);
$pdf->Cell(135, 10, 'Total Unpaid Salary:', 1, 0, 'C', true);
$pdf->Cell(55, 10, number_format($totalUnpaidSalary, 2), 1, 1, 'C', true);

$pdf->Output('D', 'payroll_report.pdf');
?>
