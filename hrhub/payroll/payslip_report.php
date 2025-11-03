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
    <title>Payslip report</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f8fa;
        margin: 0;
        padding: 0;
    }

    /* Section and Container */
    .payslip_report {
        margin-top: 80px;
        padding: 20px;
        transition: margin-left 0.4s ease;
        border-radius: 10px;
    }

    nav.open~.payslip_report {
        margin-left: 200px;
    }

    .container {
        width: 100%;
        margin: auto;
        padding: 20px;
    }

    /* Card Styles */
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
        margin: 20px -20px;
    }

    /* Input Group */
    .input-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }

    .input-group label {
        white-space: nowrap;
        font-weight: bold;
        color: #5f3a99;
        min-width: 100px;
    }

    .input-group input[type="text"],
    .input-group select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #eef2f7;
        flex: 1;
    }

    /* Buttons */
    .btn {
        background-color: #28a745;
        color: white;
        padding: 8px 15px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        margin-top: 5px;
    }

    .btn:hover {
        background-color: #218838;
    }

    /* Payroll Actions */
    .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .btn-group {
        display: flex;
        gap: 10px;
    }

    /* Search Bar */
    .search-bar {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #eef2f7;
    }

    /* Table Container */
    .table-container {
        background: white;
        padding: 15px;
        border-radius: 5px;

    }

    /* Table Styles */
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

    /* Special Genrate Salary Button */
    .btn.genrate {
        background-color: #f44336;
    }

    .btn.genrate:hover {
        background-color: #d32f2f;
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
        margin-left: 120px;
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
    <section class="payslip_report">
        <div class="container">
            <!-- Input Card -->
            <div class="card">
                <h2>Monthly Payroll List</h2>
                <hr>
                <form method="POST">
                    <div class="input-group">

                        <label>Employee</label>
                        <input type="text" id="search-query" name="employee_name" placeholder="Search Employee Name"
                            required>
                        <div id="search-results" class="suggestions"></div>

                        <label for="salary-month-year">Select Month & Year</label>
                        <select id="salary-month-year" name="salary_month_year" required>
                            <?php
    $financialYearStart = 2024; // Define financial year start
    $financialYearEnd = $financialYearStart + 1; // Next year for March

    $months = [
        "April", "May", "June", "July", "August", "September", 
        "October", "November", "December", "January", "February", "March"
    ];

    foreach ($months as $index => $month) {
        $year = ($index < 9) ? $financialYearStart : $financialYearEnd; // April-Dec (2024), Jan-Mar (2025)
        $selected = ($index == 0) ? "selected" : ""; // Default selection: April
        echo "<option value='$month $year' $selected>$month $year</option>";
    }
    ?>
                        </select>

                        <button class="btn" type="submit" id="fetch-payroll">Submit</button>

                </form>
            </div>
        </div>

        <div class="card table-container">
            <h2>Full attendance</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payroll Id</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Present Days (in a month)</th>
                        <th>Total Hours (in a month)</th>
                        <th>Basic Salary</th>
                        <th>Payment Mode</th>
                       
                        <th>Pay Date</th>
                        <th>razorpay_payment_id</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['employee_name'];
    $selected_month_year =$_POST['salary_month_year'];
    
   // Convert selected month-year into payroll month (next month)
   $date = DateTime::createFromFormat('F Y', $selected_month_year);
   $date->modify('+1 month');  // Move to the next month to get payroll month-year
   $payroll_month_year = $date->format('F Y'); // Get payroll month-year

   $query = "SELECT 
               p.payroll_id, 
               e.employee_name, 
               d.department_name, 
               des.designation_name, 
               p.present_days, 
               p.total_hours, 
               p.salary, 
               p.payment_mode, 
               p.pay_date, 
               p.razorpay_payment_id
           FROM payroll p
           JOIN employee e ON p.employee_id = e.employee_id
           JOIN department d ON e.department_id = d.department_id
           JOIN designation des ON e.designation_id = des.designation_id
           WHERE e.employee_name = '$employee_name' 
           AND p.month_year = '$payroll_month_year'";  // Fetch salary given in next month

    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$row['payroll_id']}</td>
                <td>{$row['employee_name']}</td>
                <td>{$row['department_name']}</td>
                <td>{$row['designation_name']}</td>
                <td>{$row['present_days']}</td>
                <td>{$row['total_hours']}</td>
                <td>{$row['salary']}</td>
                <td>{$row['payment_mode']}</td>
            
                <td>{$row['pay_date']}</td>
                <td>{$row['razorpay_payment_id']}</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='11'>No salary records found.</td></tr>";
    }
}
?>

                </tbody>
            </table>
        </div>

        <!-- Payroll List Card -->
        <div class="card">


            <div class="actions">
                <div class="btn-group">

                <form action="monthly_payroll_pdf.php" method="POST" target="_blank">
    <input type="hidden" name="employee_name" value="<?php echo $_POST['employee_name'] ?? ''; ?>">
    <input type="hidden" name="salary_month_year" value="<?php echo $_POST['salary_month_year'] ?? ''; ?>">
    <button class="btn">Generate PDF</button>
</form>

                  
                </div>

            </div>
    </section>
</body>
<script>
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

</html>