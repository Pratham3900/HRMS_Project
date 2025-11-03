<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if(isset($_GET["leave_id"])){
    $leave_id = $_GET["leave_id"];

    // Get leave details
    $leave_query = "SELECT employee_id, leave_type_id, duration FROM `leave` WHERE leave_id = $leave_id";
    $leave_result = mysqli_query($con, $leave_query);
    $leave_row = mysqli_fetch_assoc($leave_result);

    if ($leave_row) {
        $employee_id = $leave_row['employee_id'];
        $leave_type_id = $leave_row['leave_type_id'];
        $duration = $leave_row['duration'];

        // Update leave status to 'Approved'
        $update_query = "UPDATE `leave` SET leave_status = 'Approved' WHERE leave_id = $leave_id";
        mysqli_query($con, $update_query);

        // Update Employee Leave Balance
        $balance_query = "UPDATE employee_leave_balance 
                          SET used_leaves = used_leaves + $duration, 
                              remaining_leaves = remaining_leaves - $duration 
                          WHERE employee_id = $employee_id AND leave_type_id = $leave_type_id";
        mysqli_query($con, $balance_query);
    }
}

header("location:leave.php");
exit;
?>
