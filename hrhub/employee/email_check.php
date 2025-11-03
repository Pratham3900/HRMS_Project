<?php
include '../connection.php';

if (isset($_POST['check_emailbtn'])) {
    $email = $_POST["email"];

    $existsql = "SELECT email FROM employee WHERE email = '$email'";
    $result = mysqli_query($con, $existsql);

    if (!$result) {
        die("Query Failed: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        echo "Email already exists";
    } else {
        echo "";
    }
    exit(); // Stop further execution
}
?>
