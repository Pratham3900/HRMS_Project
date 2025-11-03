<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["notice_id"])){
    $notice_id=$_GET["notice_id"];

    $sql="DELETE FROM notice WHERE notice_id=$notice_id";
    mysqli_query($con, $sql);
}
header("location:notice.php");	
exit;
?>
