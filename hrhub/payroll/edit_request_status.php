<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Check if ID is set
if (!isset($_GET['request_id'])) {
    header("Location: payroll_request.php");
    exit();
}

$request_id = $_GET['request_id'];

// Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason_of_rejection = mysqli_real_escape_string($con, $_POST['reason_of_rejection']); // Sanitize input

    // Update Query (Fixed Variable Name)
    $update_sql = "UPDATE payslip_requests SET status = 'Rejected', reason_of_rejection = '$reason_of_rejection' WHERE request_id = $request_id";

    if (mysqli_query($con, $update_sql)) {
        header("Location: payroll_request.php");
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reject Payslip Request</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        section{
            margin-top:100px;
        }
        /* Form Styling */
        .edit-form {
      
            width: 400px;
            background: white;
            padding: 20px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 20px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <?php include '../nav.php'; ?>

    <section class="apply_leave">
        <form class="edit-form" method="post" action="">
            <h2>Reject Payslip Request</h2>

            <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">

            <label>Reason for Rejection:</label>
            <input type="text" name="reason_of_rejection" required>

            <input type="submit" value="Reject Request">
        </form>
    </section>

</body>
</html>
