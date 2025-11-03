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
    <title>leave_type</title>
    <style>
/* General Styles */
body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
        }

/* Dashboard Styles */
.leave_type {
    margin-top: 60px;
    margin-left: 0;
    padding: 20px;
    transition: margin-left 0.4s ease;
}

nav.open ~ .leave_type{
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
.leave_type-list h2 {

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

    </style>
</head>
<body>
<?php 
    include '../nav.php';
    ?>
    <section class="leave_type">
    <header class="header">
    <i class="fa-solid fa-user-large-slash icon"></i>
    Leave
    </header>
    <div class="sub-header">
            <a href="add_leave_type.php"><button class="btn add-employee">Add Leave type</button></a>

        </div>
    <div class="container">
       
        <div class="leave_type-list">
            <h2>Leave type List</h2><hr>
           
          
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Leave Type</th>
                       <th>Number of Days</th>
                
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                $sql = "SELECT * FROM leave_type";

                $result = mysqli_query($con, $sql);
               if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td> $row[leave_type_id]</td>
                        <td> $row[leave_type_name]</td>
                        <td>{$row['number_of_days']} days</td>
                      
                        <td>
                         <a href='edit_leave_type.php?leave_type_id=" . $row['leave_type_id'] . "' class='btn edit'>Edit</a>
                         <a href='delete_leave_type.php?leave_type_id=" . $row['leave_type_id'] . "' class='btn delete'>Delete</a>
                                        
                        </td>
                    </tr>";
                }
            }else {
                echo "<tr><td colspan='14'>No leave type found</td></tr>";
            }

                    ?>
                </tbody>
            </table>
        </div>
    </div>

    </section> 
</body>
</html>