<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}
include 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['emp_name'];
    $leave_type_id = $_POST['leaveType'];
    $apply_date = $_POST['apply_date'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];

    // Fetch employee ID based on name
    $emp_query = "SELECT employee_id FROM employee WHERE employee_name = '$employee_name'";
    $emp_result = mysqli_query($con, $emp_query);
    $emp_row = mysqli_fetch_assoc($emp_result);
    $employee_id = $emp_row['employee_id'] ?? null;

    // Fetch leave type name based on ID
    $leave_query = "SELECT leave_type_name FROM leave_type WHERE leave_type_id = '$leave_type_id'";
    $leave_result = mysqli_query($con, $leave_query);
    $leave_row = mysqli_fetch_assoc($leave_result);
    $leave_type_name = $leave_row['leave_type_name'] ?? null;

    // Insert into leave table
    
    $check_leave_query = "SELECT remaining_leaves FROM employee_leave_balance 
    WHERE employee_id = '$employee_id' 
    AND leave_type_id = '$leave_type_id'";
$check_leave_result = mysqli_query($con, $check_leave_query);
$leave_data = mysqli_fetch_assoc($check_leave_result);
$remaining_leaves = $leave_data['remaining_leaves'] ?? 0;

if ($employee_id && $leave_type_name && $remaining_leaves > 0) {

        $insert_query = "INSERT INTO `leave`(employee_id, leave_type_id,apply_date, start_date, end_date, duration) 
                         VALUES ('$employee_id','$leave_type_id','$apply_date', '$start_date', '$end_date', '$duration')";
        
        mysqli_query($con, $insert_query);
        header("Location: leave.php");
      exit();

    } 
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    .container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .card {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        flex: 1;
        min-width: 300px;
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
        margin-top: 20px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        word-wrap: break-word;
    }

    th {
        background: #007bff;
        color: white;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
    }

    .form-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    label {
        font-weight: bold;
        width: 40%;
        text-align: left;
    }

    input,
    select,
    textarea {
        width: 55%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <section class="dashboard">
    
        <div class="container">
        <div class="row">
    <div class="card">
        <h3>Leave Balance</h3>
        <table>
            <tr>
                <th>Leave Type</th>
                <th>Used Days</th>
                <th>Remaining Days</th>
            </tr>
            <?php
            $employee_id = $_SESSION['employee_id']; // Get employee ID from session

            $balance_query = "SELECT lt.leave_type_name, elb.used_leaves, elb.remaining_leaves 
                              FROM employee_leave_balance elb
                              JOIN leave_type lt ON elb.leave_type_id = lt.leave_type_id
                              WHERE elb.employee_id = '$employee_id'";

            $balance_result = mysqli_query($con, $balance_query);

            if (mysqli_num_rows($balance_result) > 0) {
                while ($row = mysqli_fetch_assoc($balance_result)) {
                    echo "<tr>
                            <td>{$row['leave_type_name']}</td>
                            <td>{$row['used_leaves']}</td>
                            <td>{$row['remaining_leaves']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No leave balance found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

            <div>
                <form method="GET">
                    <select name="month" id="monthSelect" class="search-bar" onchange="this.form.submit()">
                        <option value="">Select Month</option>
                        <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = date("F", mktime(0, 0, 0, $m, 1)); // Get full month name
                    $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? "selected" : "";
                    echo "<option value='$m' $selected>$monthName</option>";
                }

                
                ?>
                    </select>
                </form>
            </div>
            <!-- Holiday Calendar at the top -->
            <div class="card">
                <h3>Holiday Calendar</h3>
                <table>
                    <tr>
                        <th>Holiday Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>

                    </tr>
                    <?php
                 $monthFilter = isset($_GET['month']) && $_GET['month'] !== "" ? $_GET['month'] : null;
                 $yearFilter = date('Y'); // Default to the current year
                 
                 if ($monthFilter) {
                     $sql = "SELECT * FROM holiday 
                             WHERE MONTH(start_date) = $monthFilter 
                             AND YEAR(start_date) = $yearFilter 
                             ORDER BY start_date ASC";
                 } else {
                     $sql = "SELECT * FROM holiday 
                             WHERE end_date >= CURDATE() 
                             ORDER BY start_date ASC";
                 }
                $result = mysqli_query($con, $sql);
               if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                    <td> $row[holiday_name]</td>
                         <td> $row[start_date]</td>
                        <td> $row[end_date]</td>
                  
                    </tr>";
                }
            }else {
                echo "<tr><td colspan='3'>No holiday found</td></tr>";
            }

                    ?>


                </table>
            </div>

            <!-- Apply for Leave and Leave History in one row -->
            <div class="row">
                <div class="card">
                    <h3>Apply for Leave</h3>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="emp_name">Employee Name</label>
                            <input type="text" id="emp_name" name="emp_name" readonly
                                value="<?php echo $_SESSION['employee_name'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="apply-date">Apply Date:</label>
                            <input type="date" id="apply-date" name="apply_date" required>
                        </div>
                        <div class="form-group">
                            <label for="start-date">Start Date:</label>
                            <input type="date" id="start-date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end-date">End Date:</label>
                            <input type="date" id="end-date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration</label>
                            <input type="text" id="duration" name="duration" placeholder="Duration" readonly>
                        </div>

                        <div class="form-group">
                            <label for="leave_type">Leave Type</label>

                            <select id="leaveType" name="leaveType" required>
    <?php
    $employee_id = $_SESSION['employee_id']; // Get the logged-in employee ID
    $leave_query = "SELECT lt.leave_type_id, lt.leave_type_name, elb.remaining_leaves
                    FROM leave_type lt
                    LEFT JOIN employee_leave_balance elb ON lt.leave_type_id = elb.leave_type_id 
                    AND elb.employee_id = '$employee_id'";

    $result = mysqli_query($con, $leave_query);

    while ($row = mysqli_fetch_assoc($result)) {
        $leave_id = $row['leave_type_id'];
        $leave_name = $row['leave_type_name'];
        $remaining = $row['remaining_leaves'] ?? 0; // Default to 0 if NULL

        $disabled = ($remaining == 0) ? "disabled" : "";
        echo "<option value='$leave_id' $disabled>$leave_name ($remaining days left)</option>";
    }
    ?>
</select>

                        </div>

                        <button type="submit">Submit Request</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Leave History</h3>
                    <table>
                        <tr>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                        <?php
        $employee_id = $_SESSION['employee_id']; // Get employee ID from session

        $history_query = "SELECT l.apply_date, 
               lt.leave_type_name, 
               COALESCE(l.leave_status, 'Pending') AS leave_status 
        FROM `leave` l
        JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id
        WHERE l.employee_id = '$employee_id'
        ORDER BY l.apply_date DESC";
        $history_result = mysqli_query($con, $history_query);

        if (mysqli_num_rows($history_result) > 0) {
            while ($row = mysqli_fetch_assoc($history_result)) {
                $statusColor = ($row['leave_status'] == 'Approved') ? 'green' : (($row['leave_status'] == 'Rejected') ? 'red' : 'orange');
                echo "<tr>
                        <td>{$row['apply_date']}</td>
                       
                        <td>{$row['leave_type_name']}</td>
                        <td style='color: $statusColor;'>{$row['leave_status']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No leave records found</td></tr>";
        }
        ?>

                    </table>
                </div>
            </div>

            <!-- Download Leave Report at the bottom -->
            <div class="card">
                <h3>Download Leave Report</h3>
                <form action="leave_report.php" method="POST">
                    <div class="form-group">
                        <label for="from_date">From</label>
                        <input type="date" id="from_date" name="from_date" required>
                    </div>
                    <div class="form-group">
                        <label for="to_date">To</label>
                        <input type="date" id="to_date" name="to_date" required>
                    </div>
                    <button type="submit">Generate PDF Report</button>
                </form>
            </div>

        </div>
    </section>
</body>
<script>
document.getElementById("start-date").addEventListener("change", updateDateInfo);
document.getElementById("end-date").addEventListener("change", updateDateInfo);

async function updateDateInfo() {
    let startDate = document.getElementById("start-date").value;
    let endDate = document.getElementById("end-date").value;

    if (startDate && endDate) {
        let sDate = new Date(startDate);
        let eDate = new Date(endDate);

        if (eDate < sDate) {
            document.getElementById("duration").value = "";
            return;
        }

        // Fetch holiday list from the server
        let response = await fetch("get_holidays.php");
        let holidays = await response.json();

        let duration = 0;

        while (sDate <= eDate) {
            let dayOfWeek = sDate.getDay(); // 0 = Sunday, 6 = Saturday
            let formattedDate = sDate.toISOString().split('T')[0];

            if (dayOfWeek !== 0 && dayOfWeek !== 6 && !holidays.includes(formattedDate)) {
                duration++;
            }
            sDate.setDate(sDate.getDate() + 1);
        }

        document.getElementById("duration").value = duration;
    }
}

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let today = new Date().toISOString().split('T')[0];

    // Apply Date: Must be today only
    let applyDateInput = document.getElementById("apply-date");
    applyDateInput.setAttribute("min", today);
    applyDateInput.setAttribute("max", today);
    applyDateInput.value = today;
    applyDateInput.readOnly = true; // Optional: Prevent manual changes

    // Start Date: Must be today or later
    document.getElementById("start-date").setAttribute("min", today);

    // End Date: Must be today or later
    document.getElementById("end-date").setAttribute("min", today);

    // Ensure End Date starts at least one day after Start Date
    document.getElementById("start-date").addEventListener("change", function () {
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


</html>