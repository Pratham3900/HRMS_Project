<?php
session_start();
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!=true)
?>
<?php
$showError=false;
$login=false;
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    include 'connection.php';
    
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $sql = "SELECT email, password FROM hr_login WHERE email='$email' AND password='$password'";
    $result = mysqli_query($con, $sql);
    $num = mysqli_num_rows($result);

    if ($num == 1) {
        // Generate OTP
        $otp = rand(1000, 9999);

        // Store OTP in session
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;

            // Send OTP email immediately
   
        header("Location: verify_otp.php");
        exit();
    } else {
        $showError = "Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>admin login form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body style="background: #000;">

    <div class="container" style="max-width: 500px;">
              <!-- for error msg -->
              <?php 
if($showError){
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" ">
        <strong>Error!</strong>'.$showError.'
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
}  
?>
        <div class="form">
            <div class="form login">
                <span class="title">HR Login</span>

                <form name="f1" method="post">

                    <div class="input-field">

                        <input type="text" name="email" placeholder="Enter Your Email" >
                        <i class="fa-regular fa-envelope"></i>

                    </div>
                    <b id="id3" style="color: red;"></b>
                    <div class="input-field">
                        <input type="password" placeholder="Enter Your password" id="password" name="password">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div class="input-field button">
                        <input type="submit" name="submit" value="Login now"
                            required>

                    </div>
                </form>
              
            </div>
        </div>
    </div>
   
</body>
</html>

