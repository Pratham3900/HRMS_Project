<?php
session_start();
include 'email_conection.php';

if (isset($_SESSION['email']) && isset($_SESSION['otp'])) {
    $email = $_SESSION['email'];
    $otp = $_SESSION['otp'];

    // Send OTP in the background
    sendOTP($email, $otp);
}
?>
