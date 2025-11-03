<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Check if ID is set
if (!isset($_GET['leave_id'])) {
    header("Location: leave.php");
    exit();
}
$leave_id = $_GET['leave_id'];

// Fetch existing holiday data
$sql = "SELECT 
    l.leave_id, 
    e.employee_name, 
    lt.leave_type_name, 
    l.apply_date, 
    l.start_date, 
    l.end_date, 
    l.duration
FROM `leave` l
JOIN employee e ON l.employee_id = e.employee_id
JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id
 WHERE leave_id = '$leave_id'
";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $employee_name = $row['employee_name'];
    $leave_type_name=$row['leave_type_name'];
    $apply_date = $row['apply_date'];
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];
    $duration=$row['duration'];
}



    // Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_type_id = $_POST['leaveType'];
    $apply_date = $_POST['apply_date'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];

    $update_sql = "UPDATE `leave` SET 
                    leave_type_id='$leave_type_id',
                    apply_date='$apply_date', 
                    start_date='$start_date', 
                    end_date='$end_date', 
                    duration='$duration' 
                   WHERE leave_id = '$leave_id'";

    mysqli_query($con, $update_sql);
    header("Location: leave.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
   
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .apply_leave {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.apply_leave {
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
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select {
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

    <section class="apply_leave">
        <form method="post" action="">
            <h2>Edit Leave</h2>
            <hr>
            <div class="form-group">
                <label for="employee">Employee</label>
                <input type="text" id="employee_name" name="employee_name"  value="<?php echo $employee_name; ?>" readonly>
               
            </div>

            <div class="form-group">
                <label for="leaveType">Leave Type:</label>
                <select id="leaveType"  name="leaveType" required>
                    <?php
                                // Fetch all departments
                                $sql = "SELECT * FROM leave_type";
                                $result = mysqli_query($con, $sql);
                                while ($num = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $num['leave_type_id'] . "'>" . $num['leave_type_name'] . "</option>";
                                }
                            ?>
                </select>
            </div>

            <div class="form-group">
                <label for="apply-date">Apply Date:</label>
                <input type="date" id="apply-date" name="apply_date" value="<?php echo $apply_date; ?>" required>
            </div>
            <div class="form-group">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" name="start_date" value="<?php echo $start_date; ?>" required>
            </div>
            <div class="form-group">
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" name="end_date" value="<?php echo $end_date; ?>" required>
            </div>

            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration"  value="<?php echo $duration; ?>"  readonly>
            </div>

            <div class="form-group">
                <input type="submit">
            </div>
        </form>

    </section>


    <script>
    document.getElementById("start-date").addEventListener("change", updateDateInfo);
    document.getElementById("end-date").addEventListener("change", updateDateInfo);

    function updateDateInfo() {
        let startDate = document.getElementById("start-date").value;
        let endDate = document.getElementById("end-date").value;

        if (startDate && endDate) {
            let sDate = new Date(startDate);
            let eDate = new Date(endDate);
            let timeDiff = eDate - sDate;
            let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            document.getElementById("duration").value = dayDiff >= 0 ? dayDiff : "";
        }
    }
    </script>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date().toISOString().split('T')[0];

        // Apply Date: Must be today or earlier
        document.getElementById("apply-date").setAttribute("max", today);

        // Start Date: Must be today or later
        document.getElementById("start-date").setAttribute("min", today);

        // End Date: Must be today or later
        document.getElementById("end-date").setAttribute("min", today);

        // Ensure End Date is after or equal to Start Date
        // Ensure End Date starts at least one day after Start Date
        document.getElementById("start-date").addEventListener("change", function() {
            let startDate = new Date(this.value);
            if (!isNaN(startDate.getTime())) {
                let nextDay = new Date(startDate);
                nextDay.setDate(nextDay.getDate() + 1);
                let formattedNextDay = nextDay.toISOString().split('T')[0];

                document.getElementById("end-date").setAttribute("min", formattedNextDay);
            }
        });
    });
    </script>

</body>

</html>