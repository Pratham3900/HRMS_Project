<?php
session_start();
include 'email_conection.php';

if (isset($_SESSION['employee_email']) && isset($_SESSION['employee_otp'])) {
    $email = $_SESSION['employee_email'];
    $otp = $_SESSION['employee_otp'];

    // Send OTP in the background
    sendOTP($email, $otp);
}
?>
