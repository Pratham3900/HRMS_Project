<?php
session_start();
// Unset only HR session variables
unset($_SESSION['logged_in']);
unset($_SESSION['email']);
unset($_SESSION['otp']);

// Redirect to HR login page
header("location: hr_login.php");
exit;
?>
