<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $new_salary = $_POST['new_salary'];

    $update_sql = "UPDATE employee SET salary = '$new_salary' WHERE employee_id = '$employee_id'";
    
    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('Salary updated successfully!'); window.location.href='employee.php';</script>";
    } else {
        echo "<script>alert('Error updating salary.'); window.location.href='employee.php';</script>";
    }
}
?>
