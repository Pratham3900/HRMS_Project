<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['from_date'], $_POST['to_date'], $_POST['employee_name'])) {
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    $employeeName = $_POST['employee_name'];

    // Get employee_id from employee table
    $empQuery = "SELECT employee_id FROM employee WHERE employee_name = '$employeeName'";
    $empResult = mysqli_query($con, $empQuery);
    $empRow = mysqli_fetch_assoc($empResult);

    if ($empRow) {
        $employeeId = $empRow['employee_id'];

        // Fetch total working hours and days
        $sqlTotal = "SELECT SUM(working_hours) AS total_hours, COUNT(DISTINCT date) AS total_days
                     FROM attendance
                     WHERE employee_id = '$employeeId' 
                     AND date BETWEEN '$fromDate' AND '$toDate'";

        $resultTotal = mysqli_query($con, $sqlTotal);
        $rowTotal = mysqli_fetch_assoc($resultTotal);

        $totalHours = $rowTotal['total_hours'] ?? 0;
        $uniqueDays = $rowTotal['total_days'] ?? 0;

        echo "<h2>Employee</h2><br>";
        echo "<p><strong>Total Worked  {$totalHours} Hours in {$uniqueDays} Days</strong></p>";
    } else {
        echo "<p style='color:red;'>Employee not found.</p>";
    }
}
?>
