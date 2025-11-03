<?php
include 'connection.php';

if (isset($_POST['check_emailbtn'])) {
    $username = $_POST["email"];

    // Check whether this email exists
    $existsql = "SELECT email FROM employee WHERE email='$username'";
    $result = mysqli_query($con, $existsql) or die(mysqli_error($con));
    $num = mysqli_num_rows($result);

    if ($num != 1) {
        echo "Email not available.";
    }
}
?>
