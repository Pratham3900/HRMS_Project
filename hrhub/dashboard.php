<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: hr_login.php");
    exit();
}
include 'connection.php';



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['description'])) {
  $description = $_POST['description'];
  
  if (!empty($description)) {
      $sql = "INSERT INTO hr_todo (description) VALUES ('$description')";
      mysqli_query($con, $sql);
  }

  // Redirect to prevent resubmission
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <?php 
    include 'nav.php';
    ?>
    <section class="dashboard">
        <header>
            <i class="fa-solid fa-house icon"></i>
            Dashboard
        </header>
        <div class="container">
            <div class="cards">
                <div class="card">

                    <?php
                       $employeesql="SELECT COUNT(*) AS total_employees FROM employee";
                       $result=mysqli_query($con, $employeesql);
                       $row = mysqli_fetch_assoc($result);
                       echo " <h2>{$row['total_employees']}</h2>";
                    ?>
                    <p>Employees</p>
                    <a href="employee/employee.php">View Details</a>
                </div>
                <div class="card">
                    <?php
                       $leavesql="SELECT COUNT(*) AS total_leaves FROM `leave`";
                       $result=mysqli_query($con, $leavesql);
                       $row = mysqli_fetch_assoc($result);
                       echo " <h2>{$row['total_leaves']}</h2>";
                    ?>
                    <p>Leaves</p>
                    <a href="leave/leave.php">View Details</a>
                </div>
                <div class="card">
                    <?php
                       $projectsql="SELECT COUNT(*) AS total_projects FROM `project`";
                       $result=mysqli_query($con, $projectsql);
                       $row = mysqli_fetch_assoc($result);
                       echo " <h2>{$row['total_projects']}</h2>";
                    ?>
                    <p>Projects</p>
                    <a href="project/project.php">View Details</a>
                </div>
                <!-- Total Departments (New Card) -->
                <div class="card">
                    <?php
           $departmentsql="SELECT COUNT(*) AS total_departments FROM department";
           $result=mysqli_query($con, $departmentsql);
           $row = mysqli_fetch_assoc($result);
           echo " <h2>{$row['total_departments']}</h2>";
        ?>
                    <p>Departments</p>
                    <a href="organazation/department.php">View Details</a>
                </div>
            </div>

            <div class="cards" style="margin-top: 20px;">
                <div class="card" style="background-color: #4caf50; color: white;">
                    <?php
                       $paidEmpSql="SELECT COUNT(*) AS paid_employees
FROM payroll
WHERE MONTH(pay_date) = MONTH(CURDATE())
  AND YEAR(pay_date) = YEAR(CURDATE());
";
                       $result=mysqli_query($con, $paidEmpSql);
                       $row = mysqli_fetch_assoc($result);
                          echo " <h2 style='color: white;'>{$row['paid_employees']}</h2>";
                    ?>
                    <p style="color: white;">Employees Who Got Salary</p>
                </div>
                <div class="card" style="background-color: #1a73e8; color: white;">
                    <?php
                       $leavesql="SELECT COUNT(*) AS total_leaves FROM `leave` WHERE leave_status IS NULL;";
                       $result=mysqli_query($con, $leavesql);
                       $row = mysqli_fetch_assoc($result);
                       echo " <h2 style='color: white;'>{$row['total_leaves']}</h2>";
                    ?>
                    <p style="color: white;">Pending Leave Applications</p>
                </div>
                <div class="card" style="background-color: #d32f2f; color: white;">
                    <?php
                       $projectsql="SELECT COUNT(*) AS total_projects FROM `project` where status='Planned';";
                       $result=mysqli_query($con, $projectsql);
                       $row = mysqli_fetch_assoc($result);
                       echo " <h2 style='color: white;'>{$row['total_projects']}</h2>";
                    ?>
                    <p style="color: white;">Upcoming Project</p>
                </div>
                <!-- New Card: Total Attendance Today -->
                <div class="card" style="background-color: #ff9800; color: white;">
                    <?php
           $attendanceSql="SELECT COUNT(*) AS total_attendance FROM attendance WHERE date = CURDATE()";
           $result=mysqli_query($con, $attendanceSql);
           $row = mysqli_fetch_assoc($result);
           echo " <h2 style='color: white;'>{$row['total_attendance']}</h2>";
        ?>
                    <p style="color: white;">Total Attendance Today</p>
                </div>
            </div>
            <div class="todo-project">
                <!-- Updated To-Do List -->
                <div class="todo-card">
                    <h2>Todo List</h2>
                    <form method="post" action="">
                        <div class="new-task">
                            <input type="text" name="description" id="new-task-input" placeholder="Enter a new task">
                            <button type="submit">Add Task</button>
                        </div>
                    </form>

                    <div class="todo">
                        <ul id="incomplete-tasks">
                            <?php
                         $sql = "SELECT * FROM hr_todo";
                         $result = mysqli_query($con, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<li class='todo-item'>
                      
                         <span class='task-text'>$row[description]</span>
                         <a href='delete_task.php?todo_id=" . $row['todo_id'] . "'>
                            <button class='btn delete'>Delete</button>
                         </a>
                           </li>";
                            }
                          ?>
                        </ul>
                    </div>
                </div>

                <div class="project-running">
                    <h2>Running Projects</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'connection.php';
                             $sql = "SELECT project_title, start_date, end_date 
                              FROM project
                             WHERE status = 'In Process';
                              ";

                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  
                        <td> $row[project_title] </td>
                        <td> $row[start_date] </td>
                       <td> $row[end_date] </td>

                        
                      </tr>  ";
                    }
                    ?>
                        </tbody>
                    </table>
                </div>

            </div>


            <div class="info-tables">
                <!-- Notice Table -->
                <div class="notice-table ">
                    <h2>Notice</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                include 'connection.php';
                    $sql = "select * from notice ORDER BY date desc";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo "<tr>  
                        <td> $row[title] </td>
                        <td> $row[description] </td>
                        <td> $row[date] </td>
                      </tr>  ";
                    }
                    ?>

                        </tbody>
                    </table>
                </div>
                <!-- Holiday Table -->
                <div class="holiday-table">
                    <h2>Holiday</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                $sql = "SELECT * FROM holiday WHERE end_date >= CURDATE() ORDER BY start_date ASC";

                $result = mysqli_query($con, $sql);
               if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                
                        <td> $row[holiday_name]</td>
                        <td> $row[start_date]</td>
                       
                    </tr>";
                }
              }

                    ?>
                        </tbody>
                    </table>
                </div>
            </div>


    </section>


</body>

</html>