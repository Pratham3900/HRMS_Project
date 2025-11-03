<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Check if ID is set
if (!isset($_GET['holiday_id'])) {
    header("Location: holiday.php");
    exit();
}

$holiday_id = $_GET['holiday_id'];
$holiday_name = "";
$start_date = "";
$end_date = "";
$days = "";
$year = "";

// Fetch existing holiday data
$sql = "SELECT * FROM holiday WHERE holiday_id = '$holiday_id'";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $holiday_name = $row['holiday_name'];
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];
    $days = $row['days'];
    $year = $row['year'];
}

// Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $holiday_name = $_POST['holiday_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $days = $_POST['days'];
    $year = $_POST['year'];

    $update_sql = "UPDATE holiday SET 
                    holiday_name='$holiday_name', 
                    start_date='$start_date', 
                    end_date='$end_date', 
                    days='$days', 
                    year='$year' 
                   WHERE holiday_id = '$holiday_id'";

    mysqli_query($con, $update_sql);
    header("Location: holiday.php");
    exit();
  
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Holiday</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .add_holiday {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_holiday {
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
    input[type="date"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus {
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
    <section class="add_holiday">
        <div class="add_holiday-container">
        <form method="POST" action="">
            <h2>Update Holiday</h2>
            <hr>
            <div class="form-group">
                <label for="holiday_name">Name</label>
                <input type="text" id="holiday" name="holiday_name" value="<?php echo htmlspecialchars($holiday_name); ?>" required>
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
                <label for="day">How Many Days?</label>
                <input type="text" id="day" name="days" value="<?php echo $days; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="year">In which Year?</label>
                <input type="text" id="year" name="year" value="<?php echo $year; ?>" readonly>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Holiday">
            </div>
        </form>

        </div>
    </section>

    <script>
    document.getElementById("start-date").addEventListener("change", updateDateInfo);
    document.getElementById("end-date").addEventListener("change", updateDateInfo);

    function updateDateInfo() {
        let startDate = document.getElementById("start-date").value;
        let endDate = document.getElementById("end-date").value;

        if (startDate) {
            document.getElementById("year").value = new Date(startDate).getFullYear();
        }

        if (startDate && endDate) {
            let sDate = new Date(startDate);
            let eDate = new Date(endDate);
            let timeDiff = eDate - sDate;
            let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            document.getElementById("day").value = dayDiff >= 0 ? dayDiff : "";
        }
    }
    </script>


</body>

</html>