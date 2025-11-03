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
    <title>project</title>
    <style>
/* General Styles */
body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
        }

/* Dashboard Styles */
.project {
    margin-top: 60px;
    margin-left: 0;
    padding: 20px;
    transition: margin-left 0.4s ease;
}

nav.open ~ .project {
    margin-left: 200px;
}

/* Header */
.header {
    background-color: white;
            color:#6a1b9a ;
            padding: 1rem;
            font-size: 1.5rem;
            margin-top: -20px;
            margin-left: -20px;
            margin-right: -20px;
            margin-bottom: 15px;
            /* border:3px solid red; */
}
.sub-header{
   
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
hr{
 
 border:2px solid #5f3a99;
 margin-right:-20px;
 margin-left:-20px;
 margin-top:20px;
 margin-bottom:20px;
}
/* Buttons */
.btn {
    background-color:  #3333ff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
}

.btn:hover {
    background-color: 	 #3385ff;
    transform: scale(1.05);
}

/* Employee List */
.project-list h2 {

    font-size: 24px;
    margin-bottom: 15px;
}

.actions {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
 
    margin-bottom:30px;
}

.search-bar {
    padding: 8px;
    border: 2px solid  #3333ff;
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
    background-color:  #3333ff;
    color: white;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}



.btn.plan {
    background-color: #ff9800; /* Bright Orange */
}

.btn.plan:hover {
    background-color: #e68900; /* Darker Orange */
}

.btn.process {
    background-color: #1e88e5; /* Royal Blue */
}

.btn.process:hover {
    background-color: #1565c0; /* Darker Royal Blue */
}

.btn.complete {
    background-color: #4caf50; /* Fresh Green */
}

.btn.complete:hover {
    background-color: #388e3c; /* Darker Fresh Green */
}

.btn.edit {
    background-color: #673ab7; /* Deep Purple */
}

.btn.edit:hover {
    background-color: #512da8; /* Darker Purple */
}

.btn.delete {
    background-color: #f44336; /* Red */
}

.btn.delete:hover {
    background-color: #d32f2f; /* Darker Red */
}

td:nth-child(3) { /* Style for "Assigned Employees" Column */
    white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
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
    link.setAttribute("download", "project_data.csv");
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
        downloadLink.download = 'project_data.xls';

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
    <section class="project">
    <header class="header">
    <i class='bx bx-list-plus icon'></i>
    Project
    </header>
    <div class="sub-header">
            <a href="add_project.php"><button class="btn add-employee">Add project</button></a>
          
        </div>
    <div class="container">
       
        <div class="project-list">
            <h2>Project List</h2><hr>
            <div class="actions">
                    <div>
                    
                    <button class="btn" onclick="printTable()">Print</button>
                    <button class="btn" onclick="exportToCSV()">CSV</button>
                    <button class="btn" onclick="exportToExcel()">Excel</button>
                  
                    <a href="project_pdf.php" target="_blank">
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
                        <th>Project Id</th>
                        <th>Project title</th>
                        <th>Assigned Employees</th> <!-- New Column -->
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
              
                    <?php
                include '../connection.php';
                $sql = "SELECT p.project_id, p.project_title, p.status, p.start_date, p.end_date, 
                GROUP_CONCAT(e.employee_name SEPARATOR ', ') AS employees
         FROM project p
         LEFT JOIN project_employees pe ON p.project_id = pe.project_id
         LEFT JOIN employee e ON pe.employee_id = e.employee_id
         GROUP BY p.project_id
         ORDER BY p.start_date DESC";

                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  

                        <td> $row[project_id]</td>
                        <td> $row[project_title] </td>
                          <td>{$row['employees']}</td> <!-- Display Employee Names -->
                        <td> $row[status] </td>
                        <td> $row[start_date] </td>
                       <td> $row[end_date] </td>

                        <td>
                                 <a href='planned_project.php?project_id=" . $row['project_id'] . "' ><button class='btn plan'>planned</button></a>
                               <a href='in_process_project.php?project_id=" . $row['project_id'] . "'> <button class='btn process'>IN process</button></a>
                                 <a href='complte_project.php?project_id=" . $row['project_id'] . "' ><button class='btn complete'>Complte</button></a>
                               <a href='edit_project.php?project_id=" . $row['project_id'] . "' ><button class='btn edit'>Edit</button></a>
                               <a href='delete_project.php?project_id=" . $row['project_id'] . "'> <button class='btn delete'>Delete</button></a>
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