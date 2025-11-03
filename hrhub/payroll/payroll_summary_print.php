<?php
include '../connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Payment Summary</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        .unpaid { background-color: #ff4c4c; color: white; }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>

<h2 style="text-align: center;">Payroll Payment Summary</h2>

<h3>Paid Employees</h3>
<table>
    <tr>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Salary</th>
        <th>Razorpay ID</th>
        <th>Payment Mode</th>
    </tr>
    <?php
    $sql_paid = "
        SELECT e.employee_name, d.department_name, ds.designation_name, e.salary, p.razorpay_payment_id, p.payment_mode 
        FROM payroll p 
        JOIN employee e ON p.employee_id = e.employee_id 
        JOIN department d ON e.department_id = d.department_id
        JOIN designation ds ON e.designation_id = ds.designation_id
        WHERE p.razorpay_payment_id IS NOT NULL
    ";
    $result_paid = mysqli_query($con, $sql_paid);
    while ($row = mysqli_fetch_assoc($result_paid)) {
        echo "<tr>
                <td>{$row['employee_name']}</td>
                <td>{$row['department_name']}</td>
                <td>{$row['designation_name']}</td>
                <td>{$row['salary']}</td>
                <td>{$row['razorpay_payment_id']}</td>
                <td>{$row['payment_mode']}</td>
              </tr>";
    }
    ?>
</table>

<h3>Unpaid Employees</h3>
<table class="unpaid">
    <tr>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Salary</th>
    </tr>
    <?php
    $sql_unpaid = "
        SELECT e.employee_name, d.department_name, ds.designation_name, e.salary 
        FROM employee e
        JOIN department d ON e.department_id = d.department_id
        JOIN designation ds ON e.designation_id = ds.designation_id
        WHERE NOT EXISTS 
        (SELECT 1 FROM payroll p WHERE p.employee_id = e.employee_id AND p.razorpay_payment_id IS NOT NULL)
    ";
    $result_unpaid = mysqli_query($con, $sql_unpaid);
    while ($row = mysqli_fetch_assoc($result_unpaid)) {
        echo "<tr>
                <td>{$row['employee_name']}</td>
                <td>{$row['department_name']}</td>
                <td>{$row['designation_name']}</td>
                <td>{$row['salary']}</td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
