<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["project_id"])){
    $project_id=$_GET["project_id"];

   // Delete project_employee first
mysqli_query($con, "DELETE FROM project_employees WHERE project_id = $project_id");

// Now delete the project
mysqli_query($con, "DELETE FROM project WHERE project_id = $project_id");

}
header("location:project.php");	
exit;
?>