<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["employee_id"])){
    $employee_id=$_GET["employee_id"];

    // $sql="DELETE FROM employee WHERE employee_id=$employee_id";
    // mysqli_query($con, $sql);

     // Delete records from related tables first
     mysqli_query($con, "DELETE FROM attendance WHERE employee_id = $employee_id");
     mysqli_query($con, "DELETE FROM payroll WHERE employee_id = $employee_id");
     mysqli_query($con, "DELETE FROM `leave` WHERE employee_id = $employee_id");
     mysqli_query($con, "DELETE FROM project_employees WHERE employee_id = $employee_id");
     mysqli_query($con, "DELETE FROM payslip_requests WHERE employee_id = $employee_id");
     mysqli_query($con, "DELETE FROM employee_leave_balance  WHERE employee_id = $employee_id");
     // Now delete from employee table
     mysqli_query($con, "DELETE FROM employee WHERE employee_id = $employee_id");
}
header("location:employee.php");	
exit;
?>
