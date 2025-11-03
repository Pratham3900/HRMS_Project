<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}
include 'connection.php';
$employee_id = $_SESSION['employee_id'];
$today = date('Y-m-d'); // Get today's date

// Get selected month from POST or default to current month
$selected_month = isset($_POST['selected_month']) ? $_POST['selected_month'] : date('Y-m');

// Split selected month into year and month
$selected_year = date('Y', strtotime($selected_month));
$selected_month_num = date('m', strtotime($selected_month));

// Query to check if the employee has signed in today
$sql = "SELECT sign_in FROM attendance WHERE employee_id = '$employee_id' AND date = '$today'";
$result = mysqli_query($con, $sql);
$attendance_status = (mysqli_num_rows($result) > 0) ? "Present" : "Absent";

// Count Present Days for selected month
$sql_present = "SELECT COUNT(*) as present_days FROM attendance WHERE employee_id = '$employee_id' AND date LIKE '$selected_month%'";
$result_present = mysqli_query($con, $sql_present);
$row_present = mysqli_fetch_assoc($result_present);
$present_days = $row_present['present_days'];

// Count Total Working Days for selected month (Assuming company follows Mon-Fri schedule)
$start_date = date('Y-m-01', strtotime($selected_month)); // First day of the selected month
$end_date = date('Y-m-t', strtotime($selected_month)); // Last day of the selected month
$working_days = 0;

for ($date = strtotime($start_date); $date <= strtotime($end_date); $date += 86400) {
    if (date('N', $date) <= 5) { // Monday to Friday (Weekdays)
        $working_days++;
    }
}

// Fetch holidays for the selected month (excluding weekends)
$sql_holidays = "SELECT COUNT(*) as holiday_count FROM holiday 
                 WHERE MONTH(start_date) = '$selected_month_num' 
                 AND YEAR(start_date) = '$selected_year' 
                 AND WEEKDAY(start_date) < 5"; // Count Mon-Fri holidays

$result_holidays = mysqli_query($con, $sql_holidays);
$row_holidays = mysqli_fetch_assoc($result_holidays);
$holiday_count = $row_holidays['holiday_count'];

// Fetch approved leave days for the current employee in the selected month
$sql_leave = "SELECT COUNT(*) as leave_days FROM `leave`
              WHERE employee_id = '$employee_id' 
              AND MONTH(start_date) = '$selected_month_num' 
              AND YEAR(start_date) = '$selected_year' 
              AND leave_status = 'Approved'";

$result_leave = mysqli_query($con, $sql_leave);
$row_leave = mysqli_fetch_assoc($result_leave);
$leave_days = $row_leave['leave_days'];

// Adjust total working days after removing holidays and approved leave
$total_working_days = $working_days - $holiday_count - $leave_days;

// Ensure absent days are adjusted correctly
$adjusted_absent_days = max(0, $working_days - $present_days - $holiday_count - $leave_days);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    .container {
        display: flex;
        gap: 20px;
        justify-content: center;
        align-items: flex-start;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: auto;
    }

    .card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        width: 48%;
    }

    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    .present {
        background: green;
        color: white;
    }

    .absent {
        background: red;
        color: white;
    }

    .late {
        background: orange;
        color: white;
    }

    .chart-container {
        width: 100%;
        max-width: 300px;
        margin: auto;
    }

    button {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 15px;
    }

    button:hover {
        background: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }
    
    .month-selector {
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <section class="dashboard">
        <div class="container">
            <div class="card">
                <h3>Today's Attendance Status</h3>
                <p class="status <?php echo strtolower($attendance_status); ?>"><?php echo $attendance_status; ?></p>
                
                <div class="month-selector">
                    <form method="post" action="">
                        <label for="selected_month">Select Month:</label>
                        <input type="month" id="selected_month" name="selected_month" 
                               value="<?php echo $selected_month; ?>" 
                               max="<?php echo date('Y-m'); ?>">
                        <button type="submit">Update Chart</button>
                    </form>
                </div>
                
                <h3>Monthly Attendance Report (<?php echo date('F Y', strtotime($selected_month)); ?>)</h3>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
                <p>Attendance Summary:
                    Present <?php echo $present_days; ?> days,
                    Remaining Workdays <?php echo $adjusted_absent_days; ?> days
                    (Out of <?php echo $total_working_days; ?> working days)
                </p>
                <p>Includes: <?php echo $holiday_count; ?> holidays and <?php echo $leave_days; ?> approved leave days</p>
            </div>
            
            <div class="card">
                <h3>Attendance Report</h3>
                <div class="input-group">
                    <form method="post" action="">
                        <label>From:</label>
                        <input type="date" name="from_date">
                        <label>To:</label>
                        <input type="date" name="to_date">
                        <button class="btn" tyoe="submit">Submit</button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Hour</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['from_date']) || isset($_POST['selected_month']))) {
                                $employee_id = $_SESSION['employee_id']; 
                                $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
                                $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';
                                
                                if (!empty($fromDate) && !empty($toDate)) {
                                    // Fetch attendance records based on date range
                                    $sql = "SELECT e.employee_name, a.date, a.sign_in, a.sign_out, a.working_hours
                                            FROM attendance a
                                            JOIN employee e ON a.employee_id = e.employee_id
                                            WHERE a.employee_id = '$employee_id' 
                                            AND a.date BETWEEN '$fromDate' AND '$toDate' 
                                            ORDER BY a.date ASC";
                                    
                                    $result = mysqli_query($con, $sql);
                                    
                                    $firstRow = true; // Flag to track the first row
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            if ($firstRow) {
                                                echo "<td rowspan='".mysqli_num_rows($result)."'>{$row['employee_name']}</td>";
                                                $firstRow = false;
                                            }
                                            echo "<td>{$row['date']}</td>
                                                  <td>{$row['sign_in']}</td>
                                                  <td>{$row['sign_out']}</td>
                                                  <td>{$row['working_hours']}</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No attendance records found for the selected period.</td></tr>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($fromDate) && !empty($toDate)): ?>
                <form method="post" action="generate_attendance_pdf.php">
                    <input type="hidden" name="from_date" value="<?php echo $fromDate; ?>">
                    <input type="hidden" name="to_date" value="<?php echo $toDate; ?>">
                    <button type="submit">Download Report (PDF)</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <script>
    var ctx2 = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Present', 'Remaining Workdays', 'Holidays', 'Approved Leave'],
            datasets: [{
                data: [
                    <?php echo $present_days; ?>,
                    <?php echo $adjusted_absent_days; ?>,
                    <?php echo $holiday_count; ?>,
                    <?php echo $leave_days; ?>
                ],
                backgroundColor: ['green', 'red', 'blue', 'orange'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Attendance for <?php echo date('F Y', strtotime($selected_month)); ?>',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
</body>
</html>