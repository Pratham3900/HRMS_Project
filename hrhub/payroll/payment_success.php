<?php
require '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_id = $_POST['razorpay_payment_id'];
    $employee_id = $_POST['employee_id'];
    $salary = $_POST['salary'];
    $month_year = $_POST['salary_month_year'];
    $pay_date = $_POST['pay_date'];

    $query = "INSERT INTO payroll (employee_id, salary, payment_mode, month_year, pay_date) 
              VALUES ('$employee_id', '$salary', 'online', '$month_year', '$pay_date')";

    if (mysqli_query($con, $query)) {
        echo "Payment recorded successfully!";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
