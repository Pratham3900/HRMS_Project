<?php
session_start();
if (!isset($_SESSION['employee_otp'])) {
    header("location: employee_login.php"); // Redirect if OTP session doesn't exist
    exit();
}
include 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];
    
    if ($entered_otp == $_SESSION['employee_otp']) {
        $_SESSION['employee_loggedin'] = true;
       // Check if the user has changed the password
       $email = $_SESSION["employee_email"];
       $query = "SELECT password_changed FROM employee WHERE email='$email'";
       $result = mysqli_query($con, $query);
       $user = mysqli_fetch_assoc($result);

       if ($user["password_changed"] == 0) {
        header("Location: change_password.php"); // Redirect to change password page
    } else {
        header("Location: dashboard.php"); // Redirect to dashboard
    }
    exit();
    } else {
        echo "<script>alert('Invalid OTP. Try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify OTP</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    form {
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 320px;

    }

    h2 {
        margin-bottom: 15px;
        color: #333;
        font-size: 20px;
    }

    input {
        width: 90%;
        padding: 12px;
        margin: 12px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        text-align: center;
        outline: none;
        transition: 0.3s;

    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.4);
    }

    button {
        background: #007bff;
        color: #fff;
        padding: 12px;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        width: 50%;
        transition: 0.3s;
    }

    button:hover {
        background: #0056b3;
    }

    .note {
        margin-top: 10px;
        font-size: 14px;
        color: #666;
    }
    </style>
</head>

<body style="background: #000;">
    <form method="post">
        <h2>OTP Verification</h2>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify OTP</button>
        <p class="note">Check your email for the OTP code.</p>
    </form>

</body>
<script>
// Call send_otp.php in the background (async)
fetch('send_otp.php');
</script>

</html>