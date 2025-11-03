<?php
session_start();
// Unset only employee session variables
unset($_SESSION['employee_loggedin']);
unset($_SESSION['employee_email']);
unset($_SESSION['employee_otp']);
unset($_SESSION['employee_id']);
unset($_SESSION['employee_name']);
unset($_SESSION['contact']);
unset($_SESSION['dob']);
unset($_SESSION['salary']);
unset($_SESSION['nationality']);
unset($_SESSION['marital_status']);
unset($_SESSION['address']);
unset($_SESSION['gender']);

// Redirect to employee login page
header("location: employee_login.php");
exit;
?>
