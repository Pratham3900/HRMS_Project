<?php
ob_start(); // Prevent any output before generating the PDF

require('fpdf.php');
include 'connection.php';

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Delete the request after successful download
    $delete_query = "DELETE FROM payslip_requests WHERE request_id = '$request_id'";
    mysqli_query($con, $delete_query);
}

if (!isset($_GET['payroll_ids']) || empty($_GET['payroll_ids'])) {
    ob_end_clean();
    die("Payroll ID(s) required.");
}

$payroll_ids = explode(',', $_GET['payroll_ids']);
$payroll_ids = array_map('intval', $payroll_ids); // Ensure they are integers
$valid_payrolls = 0;

class PDF extends FPDF
{
    function Header()
{
    $this->SetFillColor(255, 255, 255); // White background
    $this->Rect(0, 0, 210, 40, 'F'); // Remove blue background
    $this->Image('compnay_name.png', 10, 6, 30);
    $this->SetTextColor(0, 0, 0); // Black text
    $this->SetFont('Arial', 'B', 18);
    $this->Cell(190, 10, 'Tech Danawala', 0, 1, 'C');
    $this->SetFont('Arial', '', 12);
    $this->Cell(190, 6, 'Employee Payslip', 0, 1, 'C');
    $this->SetFont('Arial', 'I', 10);
    $this->Cell(190, 6, 'Goharbagh Bilimora-396321', 0, 1, 'C');
    $this->Ln(10);
    
}


    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'This is a computer-generated payslip. No signature required.', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->SetTitle('Merged Payslip');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$sql = "SELECT 
            p.payroll_id,
            e.employee_name,
            p.present_days,
            p.total_hours,
            p.salary,
            p.payment_mode,
            p.pay_date,
            p.razorpay_payment_id,
            d.department_name,
            des.designation_name,
              -- Subtract 1 month from pay_date and format as 'Month Year'
            DATE_FORMAT(DATE_SUB(p.pay_date, INTERVAL 1 MONTH), '%M %Y') AS correct_month_year
        FROM payroll p
        JOIN employee e ON p.employee_id = e.employee_id
        JOIN department d ON e.department_id = d.department_id
        JOIN designation des ON e.designation_id = des.designation_id
        WHERE p.payroll_id IN (" . implode(',', $payroll_ids) . ")
        ORDER BY p.pay_date ASC";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    ob_end_clean();
    die("No valid payroll records found.");
}

$row = mysqli_fetch_assoc($result);

$pdf->SetFillColor(240, 240, 240);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'Employee Details', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(50, 10, 'Employee Name:', 1, 0);
$pdf->Cell(140, 10, $row['employee_name'], 1, 1);

$pdf->Cell(50, 10, 'Department:', 1, 0);
$pdf->Cell(140, 10, $row['department_name'], 1, 1);

$pdf->Cell(50, 10, 'Designation:', 1, 0);
$pdf->Cell(140, 10, $row['designation_name'], 1, 1);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(100, 149, 237);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(190, 10, 'Salary Details', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0);

$pdf->Cell(40, 10, 'Month & Year', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Pay Date', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Present Days', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Total Hours', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Salary', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Mode', 1, 1, 'C', true);

mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(40, 10, $row['correct_month_year'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['pay_date'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['present_days'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['total_hours'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['salary'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['payment_mode'], 1, 1, 'C');
}

$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(255, 99, 71);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(190, 10, 'Transaction Details', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0);

mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    $payment_id = !empty($row['razorpay_payment_id']) ? $row['razorpay_payment_id'] : "Not Applicable";
    $month_year = $row['correct_month_year'];

    $pdf->Cell(60, 10, "Payment ID ($month_year):", 1, 0);
    $pdf->Cell(130, 10, $payment_id, 1, 1);
}


$pdf->Ln(15); // Add some space before signature block
$signatureY = $pdf->GetY(); // Capture Y position

// Position X for right alignment
$rightX = 150;

// Stamp (Top-right)
$pdf->Image('company_stamp.jpg', $rightX, $signatureY, 35); // Stamp first

// Signature just below stamp
$pdf->Image('hr_signature.png', $rightX, $signatureY + 35, 35); // Signature below stamp

// Authorized Signatory text just below the signature
$pdf->SetFont('Arial', '', 11);
$pdf->SetXY($rightX, $signatureY + 50); // Slightly below the signature
$pdf->Cell(40, 8, 'Authorized Signatory', 0, 1, 'C');

ob_end_clean();
$pdf->Output('D', 'Payslip_Merged.pdf');
?>