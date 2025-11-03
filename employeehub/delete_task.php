<?php
include 'connection.php';
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}

if(isset($_GET["employee_todo_id"])){
    $employee_todo_id=$_GET["employee_todo_id"];

    $sql="DELETE FROM employee_todo WHERE employee_todo_id=$employee_todo_id";
    mysqli_query($con, $sql);
}
header("location:dashboard.php");	
exit;
?>
