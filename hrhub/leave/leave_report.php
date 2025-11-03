<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>leave report</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f8fa;
        margin: 0;
        padding: 0;
    }

    .leave_report {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        border-radius: 10px;
    }

    nav.open~.leave_report {
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
        width: 200PX;
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
    </style>
</head>

<body>
    <?php 
    include '../nav.php';
    ?>
    <section class="leave_report ">
        <div class="container">
            <div class="card">
                <h2>Leave Report</h2>
                <hr>
                <form id="leaveForm" method="POST">
                    <div class="input-group">
                        <label>From</label>
                        <input type="date" name="from_date" id="from_date" required>
                        <label>To</label>
                        <input type="date" name="to_date" id="to_date" required>
                        <button class="btn">Submit</button>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <h2>Full report</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Leave type</th>
                            <th>Apply date</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                          include '../connection.php';
                          if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $fromDate = $_POST['from_date'];
                            $toDate = $_POST['to_date'];
                        
                            // Fetch leave records for all employees within the selected date range
                            $sql = "SELECT 
                            e.employee_name, 
                            lt.leave_type_name, 
                            l.apply_date, 
                            l.start_date, 
                            l.end_date, 
                            l.duration
                        FROM `leave` l
                        JOIN employee e ON l.employee_id = e.employee_id
                        JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id
                        WHERE l.apply_date BETWEEN '$fromDate' AND '$toDate'";
                            $result = mysqli_query($con, $sql);
                        
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td>{$row['employee_name']}</td>
                                            <td>{$row['leave_type_name']}</td>
                                           <td style='color: red; font-weight: bold;'>{$row['apply_date']}</td>
                                            <td>{$row['start_date']}</td>
                                            <td>{$row['end_date']}</td>
                                            <td>{$row['duration']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No leave records found within this period.</td></tr>";
                            }
                        }
                        
?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById("to_date").setAttribute("max", today);
    document.getElementById("from_date").setAttribute("max", today);
});
</script>

</html>