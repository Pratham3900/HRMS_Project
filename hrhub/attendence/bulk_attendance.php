<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Attendance</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .btn { padding: 10px 20px; margin: 10px; font-size: 18px; cursor: pointer; }
        h2{
            margin-top:40px;
        }
    </style>
</head>
<body>
<?php include '../nav.php'; ?>
    <h2>Choose an Action</h2>
    <a href="bulk_signin.php"><button class="btn">Sign In</button></a>
    <a href="bulk_signout.php"><button class="btn">Sign Out</button></a>
</body>
</html>