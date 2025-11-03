<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Get attendance ID from URL
if (isset($_GET['attendance_id'])) {
    $attendance_id = $_GET['attendance_id'];

    // Fetch existing attendance details
    $query = "SELECT attendance.*, employee.employee_name 
          FROM attendance 
          JOIN employee ON attendance.employee_id = employee.employee_id 
          WHERE attendance.attendance_id = '$attendance_id'";
    $result = mysqli_query($con, $query);
    $attendance = mysqli_fetch_assoc($result);
}

// Update attendance record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee = $_POST['employee'];
    $date = $_POST['date'];
    $sign_in = $_POST['sign_in'];
    $sign_out = $_POST['sign_out'];
    $working_hours = $_POST['working_hour'];

    $update_query = "UPDATE attendance SET  date='$date', sign_in='$sign_in', sign_out='$sign_out', working_hours='$working_hours' WHERE attendance_id='$attendance_id'";
    
    if (mysqli_query($con, $update_query)) {
        header("location: attendence.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit attendance</title>
    </title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .add_attendence {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_attendence {
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

    input[type="text"],
    input[type="date"],
    input[type="time"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="time"]:focus {
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

    <?php include '../nav.php'; ?>

    <section class="add_attendence">
        <div class="attendance-container">
        <label for="employee">Employee</label>
            
        
           
            <form action="" method="post">
                <h2>Attendance</h2>
                <hr>
                <div class="form-group">
                    <label for="attendance_id">attendance id</label>
                    <input readonly type="text" name="attendance_id" id="attendance_id" value="<?php echo $attendance['attendance_id']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="employee">Employee</label>
                    <input readonly type="text" name="employee" id="employee" value="<?php echo $attendance['employee_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="date">Select Date:</label>
                    <input type="date" name="date" id="date" value="<?php echo $attendance['date']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="sign-in">Sign In Time</label>
                    <input type="time" name="sign_in" id="sign-in" value="<?php echo $attendance['sign_in']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="sign-out">Sign Out Time</label>
                    <input type="time" name="sign_out" id="sign-out" value="<?php echo $attendance['sign_out']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="working-hour">Working Hours</label>
                    <input type="text" name="working_hour" id="working-hour" value="<?php echo $attendance['working_hours']; ?>" readonly>
                </div>

                <div class="form-group">
                <input type="submit" value="Update Attendance">
                </div>
            </form>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const signInInput = document.getElementById("sign-in");
        const signOutInput = document.getElementById("sign-out");
        const workingHourInput = document.getElementById("working-hour");

        signInInput.addEventListener("change", calculateWorkingHours);
        signOutInput.addEventListener("change", calculateWorkingHours);

        function calculateWorkingHours() {
            let signInTime = signInInput.value;
            let signOutTime = signOutInput.value;

            if (signInTime && signOutTime) {
                let signIn = new Date("2000-01-01T" + signInTime);
                let signOut = new Date("2000-01-01T" + signOutTime);

                if (signOut > signIn) {
                    let timeDiff = (signOut - signIn) / (1000 * 60 * 60); // Convert milliseconds to hours
                    workingHourInput.value = timeDiff.toFixed(2) + " hrs"; // Display with 2 decimal places
                } else {
                    workingHourInput.value = ""; // Clear if sign-out is earlier than sign-in
                }
            }
        }
    });
    </script>

</body>


</html>