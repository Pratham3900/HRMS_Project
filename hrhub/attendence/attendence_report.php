<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';
// Initialize variables 
$totalHours = 0;
$uniqueDays = 0;
$employeeId = "";
$fromDate = "";
$toDate = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_REQUEST['from_date'], $_REQUEST['to_date'], $_REQUEST['employee_name'])) {
    $fromDate = $_REQUEST['from_date'];
    $toDate = $_REQUEST['to_date'];
    $employeeName = $_REQUEST['employee_name'];


    // Get employee_id from employee table
    $empQuery = "SELECT employee_id FROM employee WHERE employee_name = '$employeeName'";
    $empResult = mysqli_query($con, $empQuery);
    $empRow = mysqli_fetch_assoc($empResult);

    if ($empRow) {
        $employeeId = $empRow['employee_id'];

        // Only run this query if $employeeId is valid
        $sqlTotal = "SELECT SUM(working_hours) AS total_hours, COUNT(DISTINCT date) AS total_days
                     FROM attendance
                     WHERE employee_id = '$employeeId' 
                     AND date BETWEEN '$fromDate' AND '$toDate'";

        $resultTotal = mysqli_query($con, $sqlTotal);
        $rowTotal = mysqli_fetch_assoc($resultTotal);

        if ($rowTotal) {
            $totalHours = $rowTotal['total_hours'] ?? 0;
            $uniqueDays = $rowTotal['total_days'] ?? 0;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])) {
    $absentDate = $_POST['selected_date'];
    
    $absentQuery = "SELECT employee_name FROM employee WHERE employee_id NOT IN (SELECT employee_id FROM attendance WHERE date = '$absentDate')";
    $absentResult = mysqli_query($con, $absentQuery);
    
    while ($row = mysqli_fetch_assoc($absentResult)) {
        $absentEmployees[] = $row['employee_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f8fa;
        margin: 0;
        padding: 0;
    }

    .attendence_report {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        border-radius: 10px;
    }

    nav.open~.attendence_report {
        margin-left: 200px;
    }

    .container {
        width: 100%;
        margin: auto;
        padding: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
        margin: 0;
        font-size: 25px;
        color: #333;
    }

    hr {
        border: 2px solid #5f3a99;
        margin-right: -20px;
        margin-left: -20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .input-group {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        align-items: center;
    }

    .input-group label {
        white-space: nowrap;
        font-weight: bold;
        color: #5f3a99;
    }

    input[type="text"],
    [type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #eef2f7;
        width: 100%;
    }

    .btn {
        background-color: #28a745;
        color: white;
        padding: 8px 15px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }

    .btn:hover {
        background-color: #218838;
    }

    .table-container {
        background: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        background-color: #f4f4f4;

    }

    .employee-section {
        margin-top: 30px;
        background: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
    }

    .employee-section h2 {
        color: #5f3a99;
    }

    /* Dropdown Styling */
    .suggestions {
        border: 1px solid #ccc;
        background: white;
        position: absolute;
        width: 33%;
        max-height: 150px;
        max-width: 600px;
        overflow-y: auto;
        display: none;
        z-index: 1000;
        margin-left: 900px;
        margin-top: 200px;
    }

    .search-item {
        padding: 10px;
        cursor: pointer;

    }

    .search-item:hover {
        background: #007BFF;
        color: white;
    }
    </style>
</head>

<body>
    <?php 
    include '../nav.php';
    ?>
    <section class="attendence_report">
        <div class="container">

        <div class="card">
                <h2>Absent Employees</h2>
                <hr>
                <form method="POST" action="">
                    <div class="input-group">
                        <label>Select Date</label>
                        <!-- <input type="date" name="absent_date" required> -->
                        <input type="date" id="selected_date" name="selected_date">
                     <p id="holiday-message" style="color:red;"></p>

                        <button type="submit" class="btn">Check Absentees</button>
                    </div>
                </form>
                <br>
                <?php if (!empty($absentEmployees)): ?>
                    <ul>
                        <?php foreach ($absentEmployees as $employee): ?>
                            <li><?php echo $employee; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])): ?>
                    <p>No absentees found for the selected date.</p>
                <?php endif; ?>
            </div>
            <div class="card">
                <h2>Attendance Report</h2>
                <hr>
                <form id="attendanceForm" method="POST" action="">
                <div class="input-group">
                        <label>From</label>
                        <input type="date" name="from_date" id="from_date" required>


                        <label>To</label>
                        <input type="date" name="to_date" id="to_date" required>


                        <label>Employee</label>
                        <input type="text" id="search-query" name="employee_name" placeholder="Search Employee Name"
                            required>
                        <div id="search-results" class="suggestions"></div>

                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>

            </div>
            <div class="card employee-section">
                <h2>Employee</h2><br>
                <p><strong>Total Worked  <?php echo $totalHours; ?> Hours in <?php echo $uniqueDays; ?> Days</strong></p>
            </div>
            <div class="table-container">
    <h2>Full Attendance</h2>
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
            <?php
            include '../connection.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['employee_name'], $_POST['from_date'], $_POST['to_date'])) {
                $employeeName = $_POST['employee_name'];
                $fromDate = $_POST['from_date'];
                $toDate = $_POST['to_date'];

                // Get employee_id from employee table
                $empQuery = "SELECT employee_id FROM employee WHERE employee_name = '$employeeName'";
                $empResult = mysqli_query($con, $empQuery);
                $empRow = mysqli_fetch_assoc($empResult);

                if (!$empRow) {
                    echo "<tr><td colspan='6'>Employee not found.</td></tr>";
                    exit();
                }

                $employeeId = $empRow['employee_id'];

                // Fetch attendance records based on employee_id
                $sql = "SELECT e.employee_name, a.date, a.sign_in, a.sign_out, a.working_hours
                        FROM attendance a
                        JOIN employee e ON a.employee_id = e.employee_id
                        WHERE a.employee_id = '$employeeId' 
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
                    echo "<tr><td colspan='5'>No attendance records found.</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <!-- PDF Button with Query String -->
    <?php if (isset($_POST['employee_name'], $_POST['from_date'], $_POST['to_date'])): ?>
        <a href="attendance_report_pdf.php?employee_name=<?= urlencode($_POST['employee_name']) ?>&from_date=<?= $_POST['from_date'] ?>&to_date=<?= $_POST['to_date'] ?>" target="_blank">
            <button class="btn">Generate PDF</button>
        </a>
    <?php endif; ?>
</div>
       </div>
    </section>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById("to_date").setAttribute("max", today);
    document.getElementById("from_date").setAttribute("max", today);
    document.getElementById("selected_date").setAttribute("max", today);
});

$(document).ready(function() {
    // Search on input event
    $('#search-query').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 0) { // Only search if input is not empty
            $.ajax({
                type: 'POST',
                url: 'search_employee.php',
                data: {
                    search_query: searchQuery
                },
                success: function(data) {
                    $('#search-results').html(data).show(); // Show suggestions
                }
            });
        } else {
            $('#search-results').hide(); // Hide dropdown if input is empty     
        }
    });

    // Select Employee Name when clicked
    $(document).on('click', '.search-item', function() {
        var selectedName = $(this).data('name');
        $('#search-query').val(selectedName);
        $('#search-results').hide(); // Hide dropdown after selection
    });

    // Hide search results if clicked outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#search-query, #search-results').length) {
            $('#search-results').hide();
        }
    });
});
</script>
<script>
$(document).ready(function() {
    $("#selected_date").change(function() {
        let selectedDate = $(this).val();

        if (selectedDate) {
            $.ajax({
                type: "POST",
                url: "check_holiday.php",
                data: { selected_date: selectedDate },
                success: function(response) {
                    $("#holiday-message").html(response);
                    updateButtonState(); // Call function to enable/disable button
                }
            });
        }
    });
});

</script>

</html>