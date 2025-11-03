<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

if (isset($_GET["leave_id"])) {
    $leave_id = $_GET["leave_id"];

    // Step 1: Check leave status and details before deletion
    $query = "SELECT employee_id, leave_type_id, duration, leave_status FROM `leave` WHERE leave_id = $leave_id";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $employee_id = $row['employee_id'];
        $leave_type_id = $row['leave_type_id'];
        $duration = $row['duration'];
        $leave_status = $row['leave_status'];

        // Step 2: If leave was approved, restore leave balance before deleting
        if ($leave_status == "Approved") {
            $update_balance = "UPDATE employee_leave_balance 
                               SET remaining_leaves = remaining_leaves + $duration, 
                                   used_leaves = used_leaves - $duration 
                               WHERE employee_id = $employee_id AND leave_type_id = $leave_type_id";
            mysqli_query($con, $update_balance);
        }

        // Step 3: Now, delete the leave record
        $delete_leave = "DELETE FROM `leave` WHERE leave_id = $leave_id";
        mysqli_query($con, $delete_leave);
    }
}

// Redirect back to leave management page
header("location: leave.php");	
exit;
?>
