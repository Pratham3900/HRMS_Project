<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if (isset($_GET["leave_type_id"])) {
    $leave_type_id = $_GET["leave_type_id"];

    // First, delete records from employee_leave_balance related to the leave_type_id
    $delete_balance_sql = "DELETE FROM employee_leave_balance WHERE leave_type_id = $leave_type_id";
    mysqli_query($con, $delete_balance_sql);

    // Then, delete the leave type from leave_type table
    $delete_leave_sql = "DELETE FROM leave_type WHERE leave_type_id = $leave_type_id";
    mysqli_query($con, $delete_leave_sql);
}

header("location: leave_type.php");	
exit;
?>
