<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["holiday_id"])){
    $holiday_id=$_GET["holiday_id"];

    $sql="DELETE FROM holiday WHERE holiday_id=$holiday_id";
    mysqli_query($con, $sql);
}
header("location:holiday.php");	
exit;
?>
