<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

$current_date = date('Y-m-d'); // Get today's date
$sql = "SELECT e.employee_id, e.employee_name, 
       a.attendance_id, a.date, a.sign_in, a.sign_out, a.working_hours 
FROM attendance a
INNER JOIN employee e ON e.employee_id = a.employee_id
LEFT JOIN `leave` l ON e.employee_id = l.employee_id 
    AND '$current_date' BETWEEN l.start_date AND l.end_date
    AND NOT (l.end_date = '$current_date')  -- Allow return on the last leave day
WHERE a.date = '$current_date' 
AND (l.employee_id IS NULL OR l.end_date = '$current_date')  -- Include employees returning on last day
ORDER BY e.employee_name;
";

$result = mysqli_query($con, $sql);

// Query to get absent employees (Employees without attendance today and not on leave)
// Check if attendance has been recorded for today
$attendance_check_sql = "SELECT COUNT(*) as total FROM attendance WHERE date = '$current_date'";
$attendance_check_result = mysqli_query($con, $attendance_check_sql);
$attendance_check_row = mysqli_fetch_assoc($attendance_check_result);
$attendance_completed = $attendance_check_row['total'] > 0;
// If attendance is completed, fetch absent employees
if ($attendance_completed) {
    $absent_sql = "
    SELECT e.employee_id, e.employee_name, 
           CASE 
               WHEN l.employee_id IS NOT NULL THEN 'On Leave'
               ELSE 'Absent' 
           END AS status
    FROM employee e
    LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date = '$current_date'
    LEFT JOIN `leave` l ON e.employee_id = l.employee_id 
        AND '$current_date' BETWEEN l.start_date AND DATE_SUB(l.end_date, INTERVAL 1 DAY)
    WHERE a.employee_id IS NULL 
    ORDER BY status DESC, e.employee_name";



    $absent_result = mysqli_query($con, $absent_sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Dashboard Styles */
    .attendence {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
    }

    nav.open~.attendence {
        margin-left: 200px;
    }

    /* Header */
    .header {
        background-color: white;
        color: #6a1b9a;
        padding: 1rem;
        font-size: 1.5rem;
        margin-top: -20px;
        margin-left: -20px;
        margin-right: -20px;
        margin-bottom: 15px;
        /* border:3px solid red; */
    }

    .sub-header {

        margin-left: 40px;
    }

    /* Container */
    .container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 95%;
        margin: auto;
        margin-top: 20px;
        /* border:3px solid red; */
    }

    hr {

        border: 2px solid #5f3a99;
        margin-right: -20px;
        margin-left: -20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    /* Buttons */
    .btn {
        background-color: #3333ff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease-in-out;
    }

    .btn:hover {
        background-color: #3385ff;
        transform: scale(1.05);
    }

    /* Employee List */
    .employee-list h2 {

        font-size: 24px;
        margin-bottom: 15px;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;

        margin-bottom: 30px;
    }

    .search-bar {
        padding: 8px;
        border: 2px solid #3333ff;
        border-radius: 5px;
        outline: none;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    thead {
        background-color: #3333ff;
        color: white;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }



    /* Edit and Delete Buttons */
    .btn.edit {
        background-color: #4caf50;
    }

    .btn.edit:hover {
        background-color: #388e3c;
    }

    .btn.delete {
        background-color: #f44336;
    }

    .btn.delete:hover {
        background-color: #d32f2f;
    }

/* Absent Employees Box */
.absent-box {
    background: #ffebee; /* Light red background */
    border: 2px solid #e57373; /* Slightly darker red border */
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    margin-bottom: 20px;
    max-width: 95%;
    margin-left: auto;
    margin-right: auto;
}

/* Absent Employees Table */
.absent-box h2 {
    color: #d32f2f; /* Dark red text */
    text-align: center;
}

.absent-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.absent-table thead {
    background-color: #e57373; /* Red header */
    color: white;
}

.absent-table th,
.absent-table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.absent-table tr:nth-child(even) {
    background-color: #fce4ec; /* Slight pinkish background for alternate rows */
}

    </style>
    
<script>
    function printTable() {
    let table = document.querySelector(".employee-list table"); // Get only the attendance table
    if (!table) return alert("No attendance data available.");

    let printWindow = window.open('', '', 'width=900,height=600');
    printWindow.document.write('<html><head><title>Print Attendance</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #3333ff; color: white; }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(table.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

function exportToCSV() {
    let table = document.querySelector(".employee-list table"); 
    if (!table) return alert("No attendance data available.");
    
    let csv = [];
    let rows = table.querySelectorAll("tr");

    for (let row of rows) {
        let cols = row.querySelectorAll("td, th");
        let csvRow = [];
        for (let col of cols) {
            let text = col.innerText.trim();
            text = text.replace(/"/g, '""'); // Escape quotes
            if (text.match(/^\d{4}-\d{2}-\d{2}$/)) {
                text = `="${text}"`; // Ensure Excel treats it as a date
            }
            csvRow.push(`"${text}"`);
        }
        csv.push(csvRow.join(","));
    }

    let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.href = encodedUri;
    link.download = "attendance_data.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel() {
    let table = document.querySelector(".employee-list table"); 
    if (!table) return alert("No attendance data available.");

    let html = table.outerHTML.replace(/ /g, '%20');
    let excelFile = "data:application/vnd.ms-excel," + html;
    
    let downloadLink = document.createElement("a");
    downloadLink.href = excelFile;
    downloadLink.download = "attendance_data.xls";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
function filterTable() {
    let input = document.getElementById("searchBar").value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName("td");
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j]) {
                let text = cells[j].innerText.toLowerCase();
                if (text.includes(input)) {
                    found = true;
                    break;
                }
            }
        }

        if (found) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}
</script>
</head>

<body>
    <?php 
    include '../nav.php';
    ?>
    <section class="attendence">
        <header class="header">

            Attendance List
        </header>
        <div class="sub-header">
            <a href="one_attendence.php"><button class="btn add-employee">Add Attendance</button></a>
            <a href="bulk_attendance.php"><button class="btn add-bullk-attendance">Bullk attendance</button></a>
            <a href="attendence_report.php"><button class="btn attendance-report">Attendance Report</button></a>
        </div>
        <div class="container">

            <div class="employee-list">
                <h2>Employee List</h2>
                <hr>
                <div class="actions">
                    <div>
                    
                    <button class="btn" onclick="printTable()">Print</button>
                    <button class="btn" onclick="exportToCSV()">CSV</button>
                    <button class="btn" onclick="exportToExcel()">Excel</button>
                  
                    <a href="export_pdf.php" target="_blank">
    <button class="btn">PDF</button>
</a>

                    </div>

                    <div>
                    <input type="text" id="searchBar" placeholder="Search" class="search-bar" onkeyup="filterTable()">

                    </div>
                </div>
                <div class="absent-box">
    <h2>Absent Employees</h2>
    <?php if (!$attendance_completed): ?>
                <p style="color: #d32f2f; font-weight: bold;">Today's attendance is not completed yet.</p>
            <?php elseif (mysqli_num_rows($absent_result) > 0): ?>
                <table class="absent-table">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($absent_result)): ?>
                            <tr>
                                <td><?= $row['employee_id'] ?></td>
                                <td><?= $row['employee_name'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: green; font-weight: bold;">No employees are absent today.</p>
            <?php endif; ?>
</div>

   

                <table>
                    <thead>
                        <tr>
                            <th>Attendance ID</th>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Sign In</th>
                            <th>Sign Out</th>
                            <th>Working Hour</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td> $row[attendance_id]</td>
                                        <td>$row[employee_name]</td>
                                        <td>$row[date]</td>
                                        <td>$row[sign_in]</td>
                                        <td>$row[sign_out]</td>
                                        <td>$row[working_hours]</td>
                                        
                                        <td>
                                            <a href='edit_attendence.php?attendance_id=" . $row['attendance_id'] . "' class='btn edit'>Edit</a>
                                            <a href='delete_attendance.php?attendance_id=" . $row['attendance_id'] . "' class='btn delete'>Delete</a>
                                        
                                            </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='14'>No employees found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>

</html>