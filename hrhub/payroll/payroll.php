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
    <title>payroll</title>

   
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Payroll Section */
    .payroll {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
    }

    nav.open~.payroll {
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
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Sub-header */
    .sub-header {
        margin-left: 40px;
    }

    /* Container */
    .container {
        background: white;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 140%;
        margin: auto;
        margin-top: 20px;
        border-radius: 10px;
    }

    /* Horizontal Line */
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

    /* Payroll List */
    .payroll-list h2 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    /* Actions Section */
    .actions {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    /* Search Bar */
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

    /* Ensure Date Column is Wide Enough */
    th:nth-child(10),
    td:nth-child(10) {
        min-width: 120px;
        white-space: nowrap;
    }

    /* Buttons in Table */
    td a {
        display: inline-block;
        margin-right: 10px;
    }

    /* Update Salary Button */
    .btn.update {
        background-color: #007bff;
    }

    .btn.update:hover {
        background-color: #0056b3;
    }

    /* Process Payroll Button */
    .btn.process {
        background-color: #28a745;
    }

    .btn.process:hover {
        background-color: #1e7e34;
    }

    /* Edit & Delete Buttons */
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

    /* Ensure Buttons Do Not Overflow */
    td {
        white-space: nowrap;
        text-align: center;
    }

    /* See Slip Button */
    .btn.slip {
        background-color: #ff9800;
    }

    .btn.slip:hover {
        background-color: #e68900;
    }


    /* Responsive Styling */
    @media (max-width: 768px) {
        .actions {
            flex-direction: column;
            gap: 5px;
        }

        .btn {
            width: 100%;
        }
    }

    </style>
    <script>
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
    <section class="payroll">
        <header class="header">
            <i class="fa-solid fa-hourglass-start"></i>
            payroll
        </header>
        <div class="sub-header">
               <a href="bulk_genrate_salary.php" onclick="return confirmPaymentMethod()"> 
    <button class="btn add-employee">Bulk Generate Salary</button>
</a>

<script>
    function confirmPaymentMethod() {
        // Display a confirmation dialog with "OK" and "Cancel"
        var result = confirm("Note: If you want to make payments online or via cheque, please select a single payment method. Payments cannot be processed for multiple employees at once.");
        
        // If the user clicks "OK", allow the navigation, else prevent it
        if (result) {
            return true;  // Allow navigation
        } else {
            return false;  // Prevent navigation
        }
    }
</script>



        </div>
        <div class="container">

            <div class="payroll-list">

                <h2>Payroll List</h2>
                <hr>
                <div class="actions">
                    <div>
                        
                      
                        
                        <a href="payroll_summary_print.php"  target="_blank"><button class="btn">Print</button></a>
<a href="payroll_summary_csv.php" > <button class="btn">CSV</button></a>


                        <a href="payroll_pdf.php" target="_blank">
    <button class="btn">PDF</button>
</a>
                    </div>

                    <div>
                    <input type="text" id="searchBar" placeholder="Search" class="search-bar" onkeyup="filterTable()">

                    </div>
                </div>

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
                            <th>Month & Year</th>
                            <th>Pay Date</th>
                            <th>razorpay_payment_id</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                include '../connection.php';
                $sql = "SELECT 
                e.employee_name,
                e.employee_id,
                COALESCE(e.salary, des.base_salary) AS salary,
                d.department_name,
                des.designation_name,
                COUNT(DISTINCT a.date) AS `Present Days`,
                SUM(a.working_hours) AS `Total Hours`,
                p.payroll_id,
                p.payment_mode,
                p.month_year,
                p.pay_date,
                p.razorpay_payment_id
            FROM employee e
            JOIN department d ON e.department_id = d.department_id
            JOIN designation des ON e.designation_id = des.designation_id
            LEFT JOIN attendance a ON e.employee_id = a.employee_id 
                AND DATE_FORMAT(a.date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')  
            LEFT JOIN payroll p ON e.employee_id = p.employee_id  
                AND p.month_year = DATE_FORMAT(CURDATE(), '%M %Y')  -- Keeps payroll for current month
            WHERE (p.payroll_id IS NOT NULL OR p.employee_id IS NULL)  
            GROUP BY 
                e.employee_id, e.employee_name, e.salary, 
                d.department_name, des.designation_name, 
                p.payroll_id, p.payment_mode, p.month_year, p.pay_date,p.razorpay_payment_id";
    
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  
                         
                       <td>{$row['payroll_id']}</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['department_name']}</td>
            <td>{$row['designation_name']} </td>
            <td>{$row['Present Days']} </td>
            <td>{$row['Total Hours']}</td>
            <td>{$row['salary']}</td>
            <td>{$row['payment_mode']}</td>
            <td>{$row['month_year']}</td>
            <td>{$row['pay_date']}</td>
            <td>{$row['razorpay_payment_id']}</td>
           <td>
                <a href='generate_salary.php?employee_id={$row['employee_id']}&present_days={$row['Present Days']}&total_hours={$row['Total Hours']}&salary={$row['salary']}'>
                    <button class='btn process'>Process Payroll</button>
                </a>
                <a href='update_salary.php?employee_id={$row['employee_id']}'>
                    <button class='btn update'>Update Salary</button>
                </a>
                 <a href='delete_payroll.php?payroll_id={$row['payroll_id']}'>
                    <button class='btn delete'>Delete Payroll</button>
                </a>
                </td>
           
        </tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</body>
<script>
    // <a href='payslip_report.php?payroll_id={$row['payroll_id']}'>
    //     <button class='btn slip'>See Slip</button>
    // </a>

document.addEventListener("DOMContentLoaded", function() {
    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
        let processButton = row.querySelector(".btn.process");
        let deleteButton = row.querySelector(".btn.delete");
        let razorpayPaymentId = row.cells[10].textContent.trim(); // Get Razorpay Payment ID

        if (razorpayPaymentId !== "") {
            // Payroll is processed, disable "Process Payroll" and enable "Delete Payroll"
            processButton.disabled = true;
            processButton.style.backgroundColor = "#ccc";
            processButton.style.cursor = "not-allowed";

            deleteButton.disabled = false;
            deleteButton.style.backgroundColor = "#f44336";
            deleteButton.style.cursor = "pointer";
        } else {
            // Payroll not processed, enable "Process Payroll" and disable "Delete Payroll"
            processButton.disabled = false;
            processButton.style.backgroundColor = "#28a745";
            processButton.style.cursor = "pointer";

            deleteButton.disabled = true;
            deleteButton.style.backgroundColor = "#ccc";
            deleteButton.style.cursor = "not-allowed";
        }

        processButton.addEventListener("click", function(event) {
            let presentDays = parseInt(row.cells[4].textContent.trim()) || 0;
            let totalHours = parseInt(row.cells[5].textContent.trim()) || 0;

            if (razorpayPaymentId !== "") {
                alert("Salary already paid for this employee.");
                event.preventDefault(); 
                return;
            }

            if (presentDays === 0 && totalHours === 0) {
                let confirmation = confirm("This employee was not present in the previous month. Do you still want to process payroll?");
                if (!confirmation) {
                    event.preventDefault();
                }
            }
        });
    });
});
</script>



</html>