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
    <title>Pay Slip</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Dashboard Styles */
    .pay_slip {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
    }

    nav.open~.pay_slip {
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
    <section class="pay_slip">
        <header class="header">
        <i class="fa-solid fa-hourglass-start"></i>
            Pay Slip
        </header>
        <!-- <div class="sub-header">
            <a href="apply_leave.php"><button class="btn add-employee">Leave application</button></a>

        </div> -->
        <div class="container">

            <div class="leave-list">
                <h2>Leave List</h2>
                <hr>
                <div class="actions">
 

                    <div>
                    <input type="text" id="searchBar" placeholder="Search" class="search-bar" onkeyup="filterTable()">

                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                        <th>Request_Id</th>
                    <th>Employee ID</th>
                    <th>Payroll Ids</th>
                    <th>Description</th>
                    <th>Year</th>
                    <th>Months</th>
                    <th>Reason for Rejection</th>
                    <th>request_date</th>
                    <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $sql = "SELECT * FROM payslip_requests";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  

                       <td>{$row['request_id']}</td>
                       <td>{$row['employee_id']}</td>
                        <td>{$row['payroll_ids']}</td> 
                        <td>{$row['reason']}</td>
                       <td>{$row['year']}</td>
                       <td>{$row['months']}</td>
                      <td>{$row['reason_of_rejection']}</td>
                      <td>{$row['request_date']}</td>
                      <td>{$row['status']}</td>
                        

                        <td>
    
                                <a href='pending_request_status.php?request_id=" . $row['request_id'] . "'> <button class='btn reject'>Pending</button></a>
                                 <a href='approve_request_status.php?request_id=" . $row['request_id'] . "'><button class='btn approve'>Approve</button></a>
                                <a href='edit_request_status.php?request_id=" . $row['request_id'] . "' ><button class='btn delete'>Reject</button></a>
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