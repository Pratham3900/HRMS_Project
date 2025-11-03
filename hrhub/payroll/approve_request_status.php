<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["request_id"])){
    $request_id=$_GET["request_id"];

    $sql="UPDATE payslip_requests SET status = 'Approved',`reason_of_rejection` = '' WHERE request_id = $request_id";
    mysqli_query($con, $sql);
}
header("location:payroll_request.php");	
exit;
?>
