<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Current date
$current_date = date('Y-m-d');

// Fetch employees who have not yet signed in today
$sql = "SELECT e.employee_id, e.employee_name, 
               COALESCE(e.salary, des.base_salary) AS salary, 
               COUNT(DISTINCT a.date) AS `Present Days`, 
               SUM(a.working_hours) AS `Total Hours`
        FROM employee e 
        LEFT JOIN attendance a 
            ON e.employee_id = a.employee_id 
            AND DATE_FORMAT(a.date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
        LEFT JOIN payroll p 
            ON e.employee_id = p.employee_id 
            AND DATE_FORMAT(p.month_year, '%M %Y') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%M %Y')
        JOIN designation des ON e.designation_id = des.designation_id
        WHERE p.payroll_id IS NULL
        GROUP BY e.employee_id, e.employee_name, salary;";

$result = mysqli_query($con, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $pay_date = isset($_POST['pay_date']) ? $_POST['pay_date'] : null;
    $payment_mode = isset($_POST['payment_mode']) ? $_POST['payment_mode'] : null;
    $month_year = isset($_POST['month_year']) ? $_POST['month_year'] : null;
    $payment_mode="Direct Benifit Transfer(DBT)";
    $razorpay_payment_id = "Not Applicable"; 

    // Insert data for each selected employee
    if (isset($_POST['employee_ids']) && is_array($_POST['employee_ids'])) {
        foreach ($_POST['employee_ids'] as $employee_id) {
            // You need to fetch employee details based on employee_id, such as present days, total hours, and salary
           
                  $sql = "   SELECT  e.salary, COUNT(DISTINCT a.date) AS `Present Days`, SUM(a.working_hours) AS `Total Hours`
        FROM employee e 
        LEFT JOIN attendance a ON e.employee_id = a.employee_id AND DATE_FORMAT(a.date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m') 
        WHERE e.employee_id = '$employee_id'
        GROUP BY  e.salary";

            $result = mysqli_query($con, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                $salary = $row['salary'];
                $present_days = $row['Present Days'];
                $total_hours = $row['Total Hours'];

                // Now insert into the payroll table
                $query = "INSERT INTO payroll (employee_id, present_days,total_hours, salary, payment_mode, month_year, pay_date, razorpay_payment_id) 
                          VALUES ('$employee_id', '$present_days', '$total_hours', '$salary', '$payment_mode', '$month_year', '$pay_date', '$razorpay_payment_id')";

                // Execute query
                if (!mysqli_query($con, $query)) {
                    echo "Error: " . mysqli_error($con);
                }
            }
        }

        // Redirect after inserting payroll data for all selected employees
        header("location: payroll.php");
        exit();
    } else {
        echo "Please select at least one employee.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bulk Payroll Generation</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin-top:100px;
    }

    h2 {
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    select {
        padding: 8px;
        width: 100%;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .btn {
        background-color: #3333ff;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        margin: 5px;
    }

    .btn:hover {
        background-color: #3385ff;
    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #3333ff;
        color: white;
    }

    td input[type="checkbox"] {
        transform: scale(1.2);
    }

    .total-salary {
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
    }
    /* Style for the note in red */
    .note {
            color: red;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    
    <script>
// Function to disable checkboxes for rows with blank "Present Days" or "Total Hours"
function updateCheckboxes() {
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let checkbox = row.querySelector('input[type="checkbox"]');
        let presentDays = row.cells[4].innerText.trim(); // Adjust index if needed
        let workingHours = row.cells[5].innerText.trim(); // Adjust index if needed

        // Disable checkbox if fields are empty
        if (presentDays === '' || workingHours === '' || presentDays === 'N/A' || workingHours === 'N/A') {
            checkbox.dataset.invalid = "true"; // Mark as invalid (but not disable)
        } else {
            checkbox.dataset.invalid = "false"; // Mark as valid
        }
    });
}

// Function to select all only if present days & working hours are not empty
function selectAll() {
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let checkbox = row.querySelector('input[type="checkbox"]');

        // Only check if the row is valid (i.e., not marked as invalid)
        if (checkbox.dataset.invalid === "false") {
            checkbox.checked = true;
        }
    });

    calculateTotalSalary();
}

// Deselect all (for both valid & invalid rows)
function deselectAll() {
    let checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    calculateTotalSalary();
}

// Function to calculate total salary of selected employees
function calculateTotalSalary() {
    let totalSalary = 0;
    let totalEmployees = 0;
    let checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');

    checkboxes.forEach(cb => {
        let row = cb.closest("tr");
        let salary = parseFloat(row.getAttribute("data-salary"));
        totalSalary += salary;
        totalEmployees++;
    });

    document.getElementById("totalSalary").textContent = `Total Salary: ${totalSalary}`;
    document.getElementById("totalEmployees").textContent = `Total Employees: ${totalEmployees}`;
}

// Run updateCheckboxes() when the page loads
window.onload = updateCheckboxes;


    </script>

</head>

<body>
<?php 
    include '../nav.php';
    ?>
    <div class="container">
         <!-- Red Note at the top -->
         <div class="note">
    Note: If you want to make payments online or via cheque, please select a single payment method. Payments cannot be processed for multiple employees at once.
</div>

        <h2>Bulk Payroll Generation</h2>
        <form method="POST" onsubmit="return validateForm()">
            <!-- Pay Date -->
            <div class="form-group">
                <label>Pay Date:</label>
                <input type="date" name="pay_date" required>
            </div>

            <!-- Payment Mode -->
            <div class="form-group">
    <label>Payment Mode</label>
    <div class="radio-group">
        <label>
            <input type="radio" name="payment_mode" value="cash" checked onchange="toggleChequeInput()">
            DBT
        </label>

        
    </div>
</div>




            <!-- Month and Year -->
            <div class="form-group">
                    <label for="salary-month-year">Select Month & Year</label>
                    <select id="salary-month-year" name="month_year" required>
                        <?php
      $currentYear = date('Y'); // Get current year
      $currentMonthNum = date('n'); // Get current month number (1-12)
      $months = [
        "January", "February", "March", "April", "May", "June", 
        "July", "August", "September", "October", "November", "December"
      ];

      for ($i = 0; $i < 12; $i++) { 
        $monthIndex = ($currentMonthNum + $i - 1) % 12; // Get correct month index
        $year = $currentYear + floor(($currentMonthNum + $i - 1) / 12); // Adjust year if needed
        $monthName = $months[$monthIndex]; // Get month name
        
        $selected = ($i == 0) ? "selected" : ""; // Select the current month by default
        echo "<option name='salary_month_year'  value='$monthName $year' $selected>$monthName $year</option>";
      }
    ?>
                    </select>
                </div>


            <!-- Employee Selection -->
            <div class="form-group">
                <label>Search Absent Employee:</label>
                <input type="text" id="searchBox" placeholder="Search Absent employee..." onkeyup="searchEmployee()">

            </div>
            <!-- Select/Deselect All Buttons -->
            <div class="form-group">
                <button type="button" class="btn" onclick="selectAll()">Select All</button>
                <button type="button" class="btn" onclick="deselectAll()">Deselect All</button>
            </div>
            <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Employee_id</th>
                <th>Employee Name</th>
                <th>Basic Salary</th>
                <th>Working Hours</th>
                <th>Present Days</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
            echo "<tr data-salary='{$row['salary']}'>
                    <td><input type='checkbox' name='employee_ids[]' value='{$row['employee_id']}' onclick='calculateTotalSalary()'></td>
                    <td>" . (isset($row['employee_id']) ? $row['employee_id'] : 'N/A') . "</td>
                    <td>" . (isset($row['employee_name']) ? $row['employee_name'] : 'N/A') . "</td>
                    <td>" . (isset($row['salary']) ? $row['salary'] : 'N/A') . "</td>
                    <td>" . (isset($row['Present Days']) ? $row['Present Days'] : 'N/A') . "</td>
                    <td>" . (isset($row['Total Hours']) ? $row['Total Hours'] : 'N/A') . "</td>
                </tr>";
            } ?>
        </tbody>
    </table>
            <div class="total-salary" id="totalSalary">Total Salary: 0</div>
            <div id="totalEmployees" class="total-salary">Total Employees: 0</div>

            <button type="submit" class="btn">Generate Payroll</button>
        </form>
    </div>
</body>

</html>