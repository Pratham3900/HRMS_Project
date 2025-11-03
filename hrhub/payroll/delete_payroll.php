<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["payroll_id"])){
    $payroll_id=$_GET["payroll_id"];

    $sql="DELETE FROM payroll WHERE payroll_id=$payroll_id";
    mysqli_query($con, $sql);
}
header("location:payroll.php");	
exit;
?>
