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
        $mail->Username = 'hrmsbyprathamdanawala@gmail.com'; // Your Gmail address
        $mail->Password = 'hqxc hdhg bdpm mtnc'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('hrmsbyprathamdanawala@gmail.com', 'HRMS - Employee Verification');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Employee Verification - OTP Required';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background:#f7f9fc; padding:20px;'>
          <div style='max-width:600px; margin:auto; background:#fff; border-radius:10px; 
            box-shadow:0 6px 20px rgba(0,0,0,0.1); overflow:hidden;'>

            <!-- Header -->
            <div style='background: linear-gradient(135deg, #ff512f, #dd2476); 
              padding:20px; text-align:center; color:white;'>
              <h2 style='margin:0;'>Employee Verification</h2>
              <p style='margin:5px 0 0;'>Secure Login to HRMS</p>
            </div>

            <!-- Body -->
            <div style='padding:30px; color:#333;'>
              <p style='font-size:16px;'>Dear Employee,</p>
              <p style='font-size:16px;'>
                To verify your identity and complete the login process, please use the 
                following <b>One-Time Password (OTP)</b>:
              </p>

              <div style='margin:30px 0; text-align:center;'>
                <span style='display:inline-block; background:#ff512f; color:white; 
                  padding:15px 35px; font-size:24px; font-weight:bold; 
                  letter-spacing:4px; border-radius:8px;'>
                  $otp
                </span>
              </div>

              <p style='font-size:14px; color:#555;'>
                ⚠️ This OTP is valid for <b>5 minutes</b>. Do not share it with anyone.
              </p>
            </div>

            <!-- Footer -->
            <div style='background:#f0f0f0; padding:15px; text-align:center; 
              font-size:12px; color:#777;'>
              © ".date("Y")." HRMS by <b>Pratham Danawala</b>. All Rights Reserved.
            </div>
          </div>
        </div>
        ";

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}
?>
