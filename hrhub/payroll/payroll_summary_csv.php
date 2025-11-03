<?php
include '../connection.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=payroll_summary.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Employee Name', 'Department', 'Designation', 'Salary', 'Razorpay ID', 'Payment Mode', 'Status']);

// Fetch Paid Employees
$sql_paid = "
    SELECT e.employee_name, d.department_name, ds.designation_name, e.salary, p.razorpay_payment_id, p.payment_mode 
    FROM payroll p 
    JOIN employee e ON p.employee_id = e.employee_id 
    JOIN department d ON e.department_id = d.department_id
    JOIN designation ds ON e.designation_id = ds.designation_id
    WHERE p.razorpay_payment_id IS NOT NULL
";
$result_paid = mysqli_query($con, $sql_paid);
while ($row = mysqli_fetch_assoc($result_paid)) {
    fputcsv($output, [$row['employee_name'], $row['department_name'], $row['designation_name'], $row['salary'], $row['razorpay_payment_id'], $row['payment_mode'], 'Paid']);
}

// Fetch Unpaid Employees
$sql_unpaid = "
    SELECT e.employee_name, d.department_name, ds.designation_name, e.salary 
    FROM employee e
    JOIN department d ON e.department_id = d.department_id
    JOIN designation ds ON e.designation_id = ds.designation_id
    WHERE NOT EXISTS 
    (SELECT 1 FROM payroll p WHERE p.employee_id = e.employee_id AND p.razorpay_payment_id IS NOT NULL)
";
$result_unpaid = mysqli_query($con, $sql_unpaid);
while ($row = mysqli_fetch_assoc($result_unpaid)) {
    fputcsv($output, [$row['employee_name'], $row['department_name'], $row['designation_name'], $row['salary'], 'N/A', 'N/A', 'Unpaid']);
}

fclose($output);
?>
