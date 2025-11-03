<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project = $_POST['project'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $employee_ids = isset($_POST['employee_id']) ? $_POST['employee_id'] : []; // Handle case when no checkbox is checked
  
  
    $sql = "INSERT INTO project (project_title,start_date,end_date) 
            VALUES ('$project', '$start_date', '$end_date')";
      mysqli_query($con,  $sql);
  
      // Get last inserted project ID
    $project_id = mysqli_insert_id($con);

    // Insert selected employees into `project_employees` table
    foreach ($employee_ids as $employee_id) {
        $sql_emp = "INSERT INTO project_employees (project_id, employee_id) VALUES ('$project_id', '$employee_id')";
        mysqli_query($con, $sql_emp);
    }
    
      header("Location: project.php");
      exit();
    
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    </title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .add_project {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_project {
        margin-left: 200px;
    }

    /* Attendance Form Styles */
    form {
        background: #fff;
        padding: 20px;

        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        /* border: 3px solid red; */

    }

    h2 {

        color: #333;
        margin-bottom: 20px;
    }

    hr {

        border: 2px solid #5f3a99;
        margin-right: -20px;
        margin-left: -20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #444;
    }

    input[type="text"],
    input[type="date"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Submit Button Styling */
    input[type="submit"] {
        width: 100%;
        padding: 10px;
        background: #007BFF;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    input[type="submit"]:hover {
        background: #0056b3;
    }
    </style>

</head>

<body>

    <?php include '../nav.php'; ?>

    <section class="add_project">

        <form method="post" action="">
            <h2>Project</h2>
            <hr>
            <div class="form-group">
                <label for="employee">Project</label>
                <input type="text" id="project" name="project" placeholder="Enter project name">
            </div>

 <!-- Employee Selection Checkboxes -->
<label for="employee_id">Assign to Employees</label>
<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
    <?php
    $emp_query = "SELECT e.employee_id, e.employee_name, d.designation_name 
                  FROM employee e
                  JOIN designation d ON e.designation_id = d.designation_id";
    $emp_result = mysqli_query($con, $emp_query);
    
    while ($emp = mysqli_fetch_assoc($emp_result)) {
        echo "<label>
                <input type='checkbox' name='employee_id[]' value='{$emp['employee_id']}'> 
                {$emp['employee_name']} - {$emp['designation_name']}
              </label><br>";
    }
    ?>
</div>
 
            <div class="form-group">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" name="start_date">
            </div>

            <div class="form-group">
                <label for="end-date">End Date:</label>
                <input type="date" id="End-date" name="end_date">
            </div>



            <div class="form-group">
                <input type="submit">
            </div>
        </form>
        </div>
    </section>



</body>


</html>