<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}

include 'connection.php'; // Ensure DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_SESSION['employee_id'];
    $reason = $_POST['reason'];
    $year = $_POST['year'];
    $months = isset($_POST['months']) ? $_POST['months'] : [];
    $status = "Pending"; // Default status

    if (!empty($months)) {
        // Convert months to 'YYYY-MM' format
        $formatted_months = array_map(function ($month) use ($year) {
            return date("Y-m", strtotime("$month 1, $year")); 
        }, $months);

        // Store the selected month names as a comma-separated string
        $months_string = implode(", ", $months); // Example: "January, February"

        // Check payroll records for selected months
        $payroll_ids = [];
        $missing_months = [];

        foreach ($formatted_months as $index => $month) {
            $query = "SELECT payroll_id FROM payroll 
                      WHERE employee_id = '$employee_id' 
                      AND DATE_FORMAT(DATE_SUB(pay_date, INTERVAL 1 MONTH), '%Y-%m') = '$month'";

            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $payroll_ids[] = $row['payroll_id'];
                }
            } else {
                $missing_months[] = $months[$index]; // Store original month names (e.g., "January")
            }
        }

        if (!empty($missing_months)) {
            $missing_months_str = implode(", ", $missing_months);
            echo "<script>alert('No salary received for: $missing_months_str');</script>";
        } else {
            if (!empty($payroll_ids)) {
                $payroll_ids_string = implode(",", $payroll_ids); // Convert to CSV format
                
                // Insert into payslip_requests table
                $sql = "INSERT INTO payslip_requests (employee_id, payroll_ids, year, months, reason, status) 
                        VALUES ('$employee_id', '$payroll_ids_string', '$year', '$months_string', '$reason', '$status')";

                if (mysqli_query($con, $sql)) {
                    echo "<script>alert('Payslip request submitted successfully!'); window.location.href='payroll.php';</script>";
                } else {
                    echo "<script>alert('Error submitting request: " . mysqli_error($con) . "');</script>";
                }
            } else {
                echo "<script>alert('No valid payroll records found!');</script>";
            }
        }
    } else {
        echo "<script>alert('Please select at least one month.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payroll</title>
    <style>
    /* General Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    .container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        width: 130%;
        margin: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .card {
        background: #fdfdfd;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    /* Table Styling */
    table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
        background: white;

    }

    .table {
        margin-top: 50px;
        background: #ffffff;
        box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);

    }

    th,
    td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #007bff;
        color: white;
    }

    /* Highlighted Fields */
    .month-year {
        color: #28a745;
        /* Green Color */
        font-weight: bold;
    }

    .pay-date {
        color: #ff5733;
        /* Reddish Color */
        font-weight: bold;
    }

    /* Button */
    button {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    button:hover {
        background: #0056b3;
    }

    /* Status Colors */
    .status-pending {

        color: #ffc107;

    }

    .status-approved {

        color: #28a745;

    }

    .status-rejected {
        color: #dc3545;

    }

    /* Form Styling */
    .form-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
        width: 50%;
        margin: auto;
        text-align: left;
        margin-top: 50px;
    }

    .form-container label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }

    .form-container input[type="text"],
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: inset 0px 1px 5px rgba(0, 0, 0, 0.1);
    }

    .form-container input:focus,
    select:focus {
        border-color: #007bff;
        outline: none;
    }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .checkbox-group input {
        margin-right: 5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        table {
            font-size: 12px;
        }

        th,
        td {
            padding: 8px;
        }

        button {
            font-size: 12px;
            padding: 6px 10px;
        }
    }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <section class="dashboard">
        <div class="container">

            <div class="card">
                <h3>Payslip History</h3>
                <table>
                    <tr>
                       <th>Month & Year</th>
                        <th>Pay Date</th>
                        <th>Payroll Id</th>
                        <th>Employee</th>
                        <th>Present Days (in a month)</th>
                        <th>Total Hours (in a month)</th>
                        <th>Basic Salary</th>
                        <th>Payment Mode</th>
                        <th>razorpay_payment_id</th>
                        <th>Department</th>
                        <th>Designation</th>




                    </tr>
                    <?php
include 'connection.php';
$employee_id = $_SESSION['employee_id']; // Fetch logged-in employee's ID

$sql = "SELECT 
            p.payroll_id,
            p.employee_id,
            p.present_days,
            p.total_hours,
            p.salary,
            p.payment_mode,
            p.pay_date,
            p.razorpay_payment_id,
            d.department_name,
            des.designation_name,
            e.employee_name,
            -- Subtract 1 month from pay_date and format as 'Month Year'
            DATE_FORMAT(DATE_SUB(p.pay_date, INTERVAL 1 MONTH), '%M %Y') AS correct_month_year
        FROM payroll p
        JOIN employee e ON p.employee_id = e.employee_id
        JOIN department d ON e.department_id = d.department_id
        JOIN designation des ON e.designation_id = des.designation_id
        WHERE p.employee_id = '$employee_id'
        ORDER BY p.pay_date";

        $result = mysqli_query($con, $sql);


                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                          
                              <td class='month-year'>{$row['correct_month_year']}</td>
                              <td class='pay-date'>{$row['pay_date']}</td>
                                    <td>{$row['payroll_id']}</td>
                                    <td>{$row['employee_name']}</td>
                                    <td>{$row['present_days']}</td>
                                    <td>{$row['total_hours']}</td>
                                    <td>{$row['salary']}</td>
                                    <td>{$row['payment_mode']}</td>
                                    
                                    
                                    <td>{$row['razorpay_payment_id']}</td>
                                    <td>{$row['department_name']}</td>
                                    <td>{$row['designation_name']}</td>
                                   
                                   
                                </tr>";
                        }
            
                       
                
                    ?>

                </table>
            </div>
        </div>
        <div class="table">
            <h3>Request History</h3>
            <table>



                <tr>
                    <th>Request_Id</th>
                    <th>Employee ID</th>
                    <th>Payroll Ids</th>
                    <th>Description</th>
                    <th>Year</th>
                    <th>Months</th>
                    <th>Reason for Rejection</th>
                    <th>Status</th>
                    <th>Download</th>
                </tr>
                <?php

$employee_id = $_SESSION['employee_id']; // Logged-in employee

$sql = "SELECT 
    pr.request_id, 
    pr.months, 
    pr.year, 
    pr.status,
    pr.employee_id,
    pr.reason,
    pr.reason_of_rejection,
    pr.payroll_ids  -- Directly fetching stored payroll IDs
FROM payslip_requests pr
WHERE pr.employee_id = '$employee_id'
ORDER BY pr.request_id DESC;
";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $status_class = strtolower($row['status']) == 'approved' ? 'status-approved' : 
                    (strtolower($row['status']) == 'rejected' ? 'status-rejected' : 'status-pending');

                    echo "<tr>
                    <td>{$row['request_id']}</td>
                    <td>{$row['employee_id']}</td>
                    <td>{$row['payroll_ids']}</td>
                    <td>{$row['reason']}</td>
                    <td>{$row['year']}</td>
                    <td>{$row['months']}</td>
                    <td>{$row['reason_of_rejection']}</td>
                    <td class='$status_class'>{$row['status']}</td>
                    <td>";
            
                    if (strtolower($row['status']) == 'approved') { 
                        echo "<a href='generate_pdf.php?payroll_ids=" . $row['payroll_ids'] . "&request_id=" . $row['request_id'] . "' target='_blank'>
                                <button>Download Payslip</button>
                              </a>";
                    } else { 
                        echo "<button disabled style='background: gray; cursor: not-allowed;'>Pending Approval</button>";
                    } 
                    
            
}
?>


            </table>
        </div>


        <div class="form-container">
            <h3>Payslip Request</h3>
            <form action="" method="POST">
                <label>Employee ID:</label>
                <input type="text" name="employee_id" value="<?php echo $_SESSION['employee_id']; ?>" readonly><br><br>

                <label>Reason for Payslip Request:</label>
                <input type="text" name="reason" required><br><br>

                <label>Select Year:</label>
                <select name="year" required>
                    <?php
    $currentYear = date("Y"); // Gets the current year dynamically
    for ($year = $currentYear; $year >= 2020; $year--) { 
        echo "<option value='$year'>$year</option>";
    }
    ?>
                </select>

                <br><br>
                <label>Select Months:</label><br>
                <?php
                $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                foreach ($months as $month) {
                    echo "<input type='checkbox' name='months[]' value='$month'> $month ";
                }
                ?>
                <br><br>
                <!-- Hidden status field (Default: Pending) -->
                <input type="hidden" name="status" value="Pending">
                <button type="submit">Request Payslip</button>
            </form>
        </div>

    </section>
</body>

</html>