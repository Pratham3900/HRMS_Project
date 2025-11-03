<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}


$showError = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    include 'connection.php';  
    $username = $_POST["e1"];
    $psw = $_POST["psw"];
    $cpsw = $_POST["cpsw"];

    // Check whether this email ID exists
    $existsql = "SELECT email FROM employee WHERE email='$username'";
    $result = mysqli_query($con, $existsql) or die(mysqli_error($con));
    $num = mysqli_num_rows($result);

    if ($num != 1) {
        $showError = "Email does not exist";
    } else {
        $sql = "UPDATE employee SET password = '$psw',password_changed=1 WHERE email='$username'";
        $result = mysqli_query($con, $sql) or die(mysqli_error($con));

        if (mysqli_affected_rows($con) == 1) {
            header("location: dashboard.php");
            exit; // Always add exit after header redirection
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Changepassword form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/form.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body style="background:#000;">
    <div class="container" style="max-width: 500px; ">
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
            <a class='btn btn-primary btn-sm' href='dashboard.php '>Home</a>
            <div class=".form login">

                <span class="title">Change Password</span>

                <form action="change_password.php" method="post" name="f1" onsubmit="return confirmPassword();">

                    <div>

                    </div>
                    <div class="input-field">
                        <input type="text" class="email_id" name="e1" placeholder="Enter Your Username">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <b class="email_error" style="color: red;"></b>


                    <div class="input-field">
                        <input type="password" placeholder="Enter New Your password" name="psw">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div class="input-field">
                        <input type="password" placeholder="Confirm Your password" onblur="confirmPassword();"
                            name="cpsw">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <b id="id5" style="color: red;"></b>



                    <div class="input-field button">
                        <input type="submit" value="Create now">
                    </div>


                </form>

            </div>

        </div>
    </div>

    </div>


</body>
<script>
$(document).ready(function() {
    $('.email_id').keyup(function(e) {

        var email = $('.email_id').val();
        //console.log(email);

        $.ajax({
            type: "POST",
            url: "code.php",
            data: {
                'check_emailbtn': 1,
                'email': email,
            },
            success: function(response) {
                // console.log(response);
                $('.email_error').text(response);
            }
        });
    });
});
// Compare password
function confirmPassword() {
    var psw = document.f1.psw.value;
    var cpsw = document.f1.cpsw.value;

    if (cpsw == "") {
        document.getElementById("id5").innerText = "*Password confirmation cannot be empty";
        return false; // Prevent form submission
    }
    if (cpsw != psw) {
        document.getElementById("id5").innerText = "*Both passwords do not match";
        return false; // Prevent form submission
    }
    document.getElementById("id5").innerText = ""; // Clear error if matched
    return true;
}
</script>


</html>