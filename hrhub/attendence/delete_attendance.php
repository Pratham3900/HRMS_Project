<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["attendance_id"])){
    $attendance_id=$_GET["attendance_id"];

    $sql="DELETE FROM attendance WHERE attendance_id=$attendance_id";
    mysqli_query($con, $sql);
}
header("location:attendence.php");	
exit;
?>
