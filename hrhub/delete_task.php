<?php
include 'connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["todo_id"])){
    $todo_id=$_GET["todo_id"];

    $sql="DELETE FROM hr_todo WHERE todo_id=$todo_id";
    mysqli_query($con, $sql);
}
header("location:dashboard.php");	
exit;
?>
