<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["project_id"])){
    $project_id=$_GET["project_id"];

    $sql="UPDATE project SET status = 'Completed' WHERE project_id = $project_id";
    mysqli_query($con, $sql);
}
header("location:project.php");	
exit;
?>
