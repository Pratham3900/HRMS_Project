<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_type_name = $_POST['leave_type_name'];
    $number_of_days = $_POST['number_of_days'];

    // Insert new leave type
    $sql = "INSERT INTO leave_type (leave_type_name, number_of_days) VALUES ('$leave_type_name', '$number_of_days')";
    if (mysqli_query($con, $sql)) {
        // Get the last inserted leave_type_id
        $leave_type_id = mysqli_insert_id($con);

        // Fetch all employee IDs
        $emp_sql = "SELECT employee_id FROM employee";
        $emp_result = mysqli_query($con, $emp_sql);

        if (mysqli_num_rows($emp_result) > 0) {
            while ($row = mysqli_fetch_assoc($emp_result)) {
                $employee_id = $row['employee_id'];

                // Insert into employee_leave_balance
                $insert_balance = "INSERT INTO employee_leave_balance (employee_id, leave_type_id, remaining_leaves, used_leaves) 
                                   VALUES ('$employee_id', '$leave_type_id', '$number_of_days', 0)";
                mysqli_query($con, $insert_balance);
            }
        }
    }

    header("Location: leave_type.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Leave Type</title>
    <style>
 /* General Styles */
 body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .add_leave_type{
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_leave_type {
        margin-left: 200px;
    }

    /* Attendance Form Styles */
    form {
        background: #fff;
        padding: 20px;

        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        /* border: 3px solid red; */

    }

    h2 {

        color: #333;
        margin-bottom: 20px;
    }
    
    hr {

        border: 2px solid #5f3a99;
        margin-right: -20px;
        margin-left: -20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #444;
    }

    input[type="text"],[type="number"]{
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,[type="number"]:focus {
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Submit Button Styling */
    input[type="submit"] {
        width: 100%;
        padding: 10px;
        background: #007BFF;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    input[type="submit"]:hover {
        background: #0056b3;
    }
   
    </style>
</head>

<body>

    <?php 
    include '../nav.php';
    ?>
    <section class="add_leave_type">
        <div class="add_leave_type-container">
            <form action="" method="post">
                <h2>Leave Type</h2>
                <hr>
                <div class="form-group">
                    <label for="holiday_name">Leave Type Name</label>
                    <input type="text" id="holiday" name="leave_type_name" placeholder="Enter leave type name">
                </div>
                <div class="form-group">
                <label for="number_of_days">Number of Days:</label>
<input type="number" name="number_of_days" required min="1" max="365">
</div>
          
                
                <div class="form-group">
                    <input type="submit">
                </div>
            </form>
        </div>
    </section>


</body>
</html>
