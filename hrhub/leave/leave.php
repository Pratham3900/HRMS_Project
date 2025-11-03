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
    <title>leave</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Dashboard Styles */
    .leave {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
    }

    nav.open~.leave {
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
    .leave-list h2 {

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
    .btn.approve {
        background-color: #4caf50;
    }

    .btn.approve:hover {
        background-color: #388e3c;
    }

    .btn.delete {
        background-color: #f44336;
    }

    .btn.delete:hover {
        background-color: #d32f2f;
    }

    .btn.edit {
        background-color: purple;
    }

    .btn.edit:hover {
        background-color: light purple;
    }

    /* Reject Button */
    .btn.reject {
        background-color: #ffcc00;
        /* Yellow Color */
        color: black;
    }

    .btn.reject:hover {
        background-color: #e6b800;
        /* Darker Yellow */
    }
    </style>
    
<script>
    function printTable() {
        let printWindow = window.open('', '', 'width=900,height=600');
        printWindow.document.write('<html><head><title>Print Employee Data</title>');
        printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: left; } th { background-color: #3333ff; color: white; }</style></head><body>');
        printWindow.document.write(document.querySelector("table").outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

    function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll("table tr");

    for (let i = 0; i < rows.length; i++) {
        let cols = rows[i].querySelectorAll("td, th");
        let row = [];

        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.trim();

            // Ensure the date format is preserved
            if (text.match(/^\d{4}-\d{2}-\d{2}$/)) {
                text = `="${text}"`; // Ensures Excel does not convert date format
            }

            row.push(`"${text.replace(/"/g, '""')}"`); // Handle commas and quotes properly
        }
        csv.push(row.join(","));
    }

    let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "leave_data.csv");
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
        downloadLink.download = 'leave_data.xls';

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
    <section class="leave">
        <header class="header">
            <i class="fa-solid fa-user-large-slash icon"></i>
            Leave
        </header>
        <div class="sub-header">
            <a href="apply_leave.php"><button class="btn add-employee">Leave application</button></a>

        </div>
        <div class="container">

            <div class="leave-list">
                <h2>Leave List</h2>
                <hr>
                <div class="actions">
                <div>
                    
                    <button class="btn" onclick="printTable()">Print</button>
                    <button class="btn" onclick="exportToCSV()">CSV</button>
                    <button class="btn" onclick="exportToExcel()">Excel</button>
                  
                    <a href="leave_data_pdf.php" target="_blank">
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
                            <th>Leave Id</th>
                            <th>Employee Name</th>

                            <th>Leave Type</th>
                            <th>Apply Date</th>
                            <th>Start date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Leave Status</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $sql = "SELECT l.leave_id, e.employee_name , lt.leave_type_name, l.apply_date, l.start_date, 
                            l.end_date, l.duration, l.leave_status 
                            FROM `leave` l
                            JOIN employee e ON l.employee_id = e.employee_id
                            JOIN leave_type lt ON l.leave_type_id = lt.leave_type_id";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  

                        <td> $row[leave_id]</td>
                        <td> $row[employee_name] </td>
                        <td> $row[leave_type_name] </td>
                        <td> $row[apply_date] </td>
                        <td> $row[start_date] </td>
                        <td> $row[end_date] </td>
                        <td> $row[duration] </td>
                        <td> $row[leave_status] </td>
                        

                        <td>
                              
                              <a href='approve_leave_status.php?leave_id=" . $row['leave_id'] . "'><button class='btn approve'>Approve</button></a>
                                <a href='reject_leave_status.php?leave_id=" . $row['leave_id'] . "'><button class='btn reject'>Reject</button></a>
                                <a href='delete_leave_status.php?leave_id=" . $row['leave_id'] . "'> <button class='btn delete'>Delete</button></a>
                               
                              </td>
                      </tr>  ";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</body>

</html>