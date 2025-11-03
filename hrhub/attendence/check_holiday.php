<?php

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
  // Connect to database
  include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])) {
    $selectedDate = $_POST['selected_date'];
    $dayOfWeek = date('l', strtotime($selectedDate)); // Get the day name (e.g., Saturday, Sunday)

    // Check if it's a weekend (Saturday or Sunday)
    if ($dayOfWeek == "Saturday" || $dayOfWeek == "Sunday") {
        echo "<p style='color:red;'>$selectedDate ($dayOfWeek) is a weekly holiday.</p>";
        exit;
    }

    // Check if the selected date is a holiday
    $holidayQuery = "SELECT holiday_name, start_date, end_date FROM holiday WHERE start_date <= '$selectedDate' AND end_date >= '$selectedDate'";
    $holidayResult = mysqli_query($con, $holidayQuery);
    $holidayRow = mysqli_fetch_assoc($holidayResult);

    if ($holidayRow) {
        if ($holidayRow['start_date'] == $selectedDate) {
            echo "<p style='color:red;'>$selectedDate is a holiday: " . $holidayRow['holiday_name'] . "</p>";
        } elseif ($holidayRow['end_date'] == $selectedDate) {
            echo "<p style='color:green;'>$selectedDate is a working day after " . $holidayRow['holiday_name'] . "</p>";
        }
        exit;
    }

    echo "<p style='color:green;'>$selectedDate is a working day.</p>";
}

?>