<?php
include '../connection.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

// Fetch employee data
$sql = "SELECT 
    e.employee_id, e.employee_name, e.email, e.contact, e.dob, e.hire_date, 
    DATE_ADD(e.dob, INTERVAL 60 YEAR) AS retirement_date,  
    des.base_salary,
    e.salary,
    COALESCE(e.salary, des.base_salary) AS final_salary,
    COALESCE(e.salary, des.base_salary) - des.base_salary AS additional_salary,
    e.nationality, e.marital_status, e.address, e.gender,
    d.department_name, des.designation_name
FROM employee e
LEFT JOIN department d ON e.department_id = d.department_id
LEFT JOIN designation des ON e.designation_id = des.designation_id;

";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Employee Styles */
    .employee {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border:3px solid red; */
    }

    nav.open~.employee {
        margin-left: 200px;
    }

    /* Header */
    header {
            background-color: white;
            color:#6a1b9a ;
            padding: 1rem;
            font-size: 1.5rem;
            margin-top:-20px;
            margin-left: -20px;
            margin-right: -20px;
            margin-bottom: 15px;
        }

    .sub-header {

        margin-left: 40px;
    }

    /* Container */
    .container {
        background: white;
        padding: 20px;
  
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 150%;
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
td a {
    display: inline-block;
    margin-right: 10px;  /* Space between Edit and Delete buttons */
}

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

/* Ensure the action buttons do not overflow */
td {
    white-space: nowrap; /* Prevents wrapping of content */
    text-align: center; /* Center align the buttons in the cell */
}
 </style>
</head>
<script>
    function openSalaryModal(employeeId, currentSalary) {
    document.getElementById("employee_id").value = employeeId;
    document.getElementById("new_salary").value = currentSalary;
    document.getElementById("salaryModal").style.display = "block";
}

function closeSalaryModal() {
    document.getElementById("salaryModal").style.display = "none";
}

    function printTable() {
        let printWindow = window.open('', '', 'width=900,height=600');
        printWindow.document.write('<html><head><title>Print Employee Data</title>');
        printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: left; } th { background-color: #3333ff; color: white; }</style></head><body>');
        printWindow.document.write(document.querySelector("table").outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

      // Function to export table data to CSV
      function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll("table tr");

    for (let i = 0; i < rows.length; i++) {
        let cols = rows[i].querySelectorAll("td, th");
        let row = [];

        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.trim();

            // Preserve date format
            if (text.match(/^\d{4}-\d{2}-\d{2}$/)) {
                text = `="${text}"`; // Prevents Excel from auto-formatting dates
            }

            // Preserve contact number format
            if (text.match(/^\d{10,}$/)) { 
                text = `="${text}"`; // Forces Excel to treat as text, preventing scientific notation
            }

            row.push(`"${text.replace(/"/g, '""')}"`); // Handle commas and quotes properly
        }
        csv.push(row.join(","));
    }

    let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "employee_data.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


    // Function to export table data to Excel
    function exportToExcel() {
        let table = document.querySelector("table");
        let html = table.outerHTML.replace(/ /g, '%20');

        let downloadLink = document.createElement("a");
        downloadLink.href = 'data:application/vnd.ms-excel,' + html;
        downloadLink.download = 'employee_data.xls';

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

<body>
    <?php include '../nav.php'; ?>
    <section class="employee">
        <header class="header">
            <i class='bx bx-group icon'></i>
            Employee
        </header>
        <div class="sub-header">
            <a href="add_employee.php"><button class="btn add-employee">Add Employee</button></a>
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
                        <a href="generate_employee_pdf.php" target="_blank"><button class="btn">PDF</button></a>

                        <input type="text" id="searchBar" placeholder="Search" class="search-bar" onkeyup="filterTable()">

                    </div>

                  
                </div>

                <table>
                    <thead>
                        <tr>
                        <th>Action</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Date Of Birth</th>                       
                            <th>Basic Salary</th>
<th>Additional</th>
<th>Final Salary</th>

                            <th>Hire Date</th>
                            <th>Retirement Date</th>
                            <th>Nationality</th>
                            <th>Marital Status</th>
                            <th>Address</th>
                            <th>Gender</th>
                            <th>Department</th>
                            <th>Designation</th>
                           
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                <td>
                                    <a href='edit_employee.php?employee_id=" . $row['employee_id'] . "' class='btn edit'>Edit</a>
                                    <a href='delete_employee.php?employee_id=" . $row['employee_id'] . "' class='btn delete'>Delete</a>
                                    <button class='btn update' onclick=\"openSalaryModal('" . $row['employee_id'] . "', '" . $row['final_salary'] . "')\">Update Salary</button>
                                </td>
                                <td> $row[employee_id]</td>
                                <td>$row[employee_name]</td>
                                <td>$row[email]</td>
                                <td>$row[contact]</td>
                                <td>$row[dob]</td>   
                            <td>{$row['base_salary']}</td>
<td>{$row['additional_salary']}</td>
<td>{$row['final_salary']}</td>

                                <td>{$row['hire_date']}</td>
                                 <td>{$row['retirement_date']}</td>
                                <td>$row[nationality]</td>
                                <td>$row[marital_status]</td>
                                <td>$row[address]</td>
                                <td>$row[gender]</td>
                                <td>$row[department_name]</td>
                                <td>$row[designation_name]</td>
                                
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
    <!-- Salary Update Modal -->
<div id="salaryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSalaryModal()">&times;</span>
        <h2>Update Salary</h2>
        <form action="update_salary.php" method="post">
            <input type="hidden" id="employee_id" name="employee_id">
            <label for="new_salary">New Salary:</label>
            <input type="number" id="new_salary" name="new_salary" required>
            <button type="submit" class="btn save">Save</button>
        </form>
    </div>
</div>

<!-- Modal CSS -->
<style>
/* Modal Background */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(5px);
}

/* Modal Content */
.modal-content {
    background: #ffffff; /* White background */
    color: #333;
    padding: 20px;
    margin: 10% auto;
    width: 40%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    text-align: center;
    animation: fadeIn 0.3s ease-in-out;
}

/* Close Button */
.close {
    float: right;
    font-size: 22px;
    font-weight: bold;
    cursor: pointer;
    color: #555;
    transition: 0.3s;
}

.close:hover {
    color: #e74c3c;
}

/* Input Fields */
.modal-content label {
    font-size: 18px;
    font-weight: bold;
    display: block;
    margin: 10px 0;
    color: #444;
}

.modal-content input[type="number"] {
    width: 90%;
    padding: 12px;
    font-size: 16px;
    border: 2px solid #5f3a99; /* Premium Purple Border */
    border-radius: 8px;
    outline: none;
    transition: 0.3s;
}

.modal-content input[type="number"]:focus {
    border-color: #3333ff;
    box-shadow: 0 0 6px rgba(51, 51, 255, 0.5);
}

/* Save Button */
.btn.save {
    background: #5f3a99; /* Premium Purple */
    color: white;
    font-size: 18px;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    margin-top:10px;
}

.btn.save:hover {
    background: #3333ff;
}

/* Fade-in Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

</body>

</html>
