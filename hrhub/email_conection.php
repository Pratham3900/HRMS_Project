<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hrmsbyprathamdanawala@gmail.com';
        $mail->Password = 'hqxc hdhg bdpm mtnc'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Timeout in case of network issues
        $mail->Timeout = 15; 

        // Email Content
        $mail->setFrom('hrmsbyprathamdanawala@gmail.com', 'HR System');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        
        // Nice colorful template
        $mail->isHTML(true);
    
$mail->Body = "
  <div style='font-family: Arial, sans-serif; padding:20px; background:#f4f6f9;'>
    <div style='max-width:600px; margin:auto; background:white; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1); overflow:hidden;'>
      
      <!-- Header -->
      <div style='background: linear-gradient(135deg, #6a11cb, #2575fc); padding:20px; text-align:center; color:white;'>
        <h1 style='margin:0; font-size:24px;'>🔑 HRMS OTP Verification</h1>
      </div>
      
      <!-- Body -->
      <div style='padding:30px; color:#333;'>
        <p style='font-size:16px; margin-bottom:15px;'>Hello,</p>
        <p style='font-size:16px;'>
          To continue logging in to <b>HR Management System</b>, please use the One-Time Password (OTP) below:
        </p>

        <!-- OTP Box -->
        <div style='margin:30px 0; text-align:center;'>
          <span style='display:inline-block; background:#2575fc; color:white; padding:15px 30px; 
          font-size:22px; font-weight:bold; letter-spacing:3px; border-radius:8px;'>
            $otp
          </span>
        </div>

        <p style='font-size:14px; color:#555;'>
          ⚠️ This OTP is valid for <b>5 minutes</b>. If you did not request this, please ignore this email.
        </p>
      </div>
      
      <!-- Footer -->
      <div style='background:#f0f0f0; padding:15px; text-align:center; font-size:12px; color:#777;'>
        © ".date("Y")." HRMS by <b>Pratham Danawala</b>. All rights reserved.
      </div>
    </div>
  </div>
";

        // Try sending
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        if (strpos($mail->ErrorInfo, 'SMTP connect() failed') !== false) {
            echo "❌ Network issue: Unable to connect to mail server. Please check your internet.";
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
        return false;
    }
}
?>
