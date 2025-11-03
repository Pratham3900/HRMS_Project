<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

require('../fpdf/fpdf.php'); // Include FPDF library
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['employee_name'];
    $selected_month_year = $_POST['salary_month_year'];

    // Convert selected month-year into payroll month (next month)
    $date = DateTime::createFromFormat('F Y', $selected_month_year);
    $date->modify('+1 month');  // Move to next month to get payroll month-year
    $payroll_month_year = $date->format('F Y'); // Payroll month-year

    $query = "SELECT 
               p.payroll_id, 
               e.employee_name, 
               d.department_name, 
               des.designation_name, 
               p.present_days, 
               p.total_hours, 
               p.salary, 
               p.payment_mode, 
               p.month_year, 
               p.pay_date, 
               p.razorpay_payment_id
           FROM payroll p
           JOIN employee e ON p.employee_id = e.employee_id
           JOIN department d ON e.department_id = d.department_id
           JOIN designation des ON e.designation_id = des.designation_id
           WHERE e.employee_name = '$employee_name' 
           AND p.month_year = '$payroll_month_year'";

    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Create PDF document
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // HEADER DESIGN
        $pdf->SetFillColor(0, 102, 204); // Dark Blue
        $pdf->SetTextColor(255, 255, 255); // White
        $pdf->Cell(190, 15, "Payroll Report -  $selected_month_year", 0, 1, 'C', true);
        $pdf->Ln(10);

        // TABLE HEADER
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(100, 149, 237); // Cornflower Blue
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(25, 10, 'Payroll ID', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Employee', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Department', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Designation', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Salary', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Pay Mode', 1, 1, 'C', true);

        // TABLE DATA
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $rowColor = false; // Alternate row colors

        while ($row = mysqli_fetch_assoc($result)) {
            if ($rowColor) {
                $pdf->SetFillColor(224, 235, 255); // Light Blue
            } else {
                $pdf->SetFillColor(255, 255, 255); // White
            }
            $rowColor = !$rowColor; // Toggle row color

            $pdf->Cell(25, 10, $row['payroll_id'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $row['employee_name'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $row['department_name'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $row['designation_name'], 1, 0, 'C', true);
            $pdf->Cell(25, 10, $row['salary'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $row['payment_mode'], 1, 1, 'C', true);
        }

        // Output PDF
        $pdf->Output('D', "Payroll_Report_ $selected_month_year.pdf");
        exit();
    } else {
        echo "No payroll records found.";
    }
}
?>
