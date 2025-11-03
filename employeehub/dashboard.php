<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}
include 'connection.php';

$employee_id = $_SESSION['employee_id'];
$today = date('Y-m-d'); // Get today's date

// Query to check if the employee has signed in today
$sql = "SELECT sign_in FROM attendance WHERE employee_id = '$employee_id' AND date = '$today'";
$result = mysqli_query($con, $sql);
$attendance_status = (mysqli_num_rows($result) > 0) ? "Present" : "Absent";

$month = date('Y-m'); // Get current month in YYYY-MM format

// Count Present Days
$sql_present = "SELECT COUNT(*) as present_days FROM attendance WHERE employee_id = '$employee_id' AND date LIKE '$month%'";
$result_present = mysqli_query($con, $sql_present);
$row_present = mysqli_fetch_assoc($result_present);
$present_days = $row_present['present_days'];

// Count Total Working Days (Assuming company follows Mon-Fri schedule)
$start_date = date('Y-m-01'); // First day of the month
$end_date = date('Y-m-t'); // Last day of the month
$working_days = 0;

for ($date = strtotime($start_date); $date <= strtotime($end_date); $date += 86400) {
    if (date('N', $date) <= 5) { // Monday to Friday (Weekdays)
        $working_days++;
    }
}

// Calculate Absent Days
$absent_days = $working_days - $present_days;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['employee_description'])) {
    $employee_description = $_POST['employee_description'];
    
    if (!empty($employee_description)) {
        $sql = "INSERT INTO employee_todo (employee_description) VALUES ('$employee_description')";
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
    <title>Employee Dashboard</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 0;
        
    }

    header {
    background-color: white;
    color: #6a1b9a;
    padding: 1rem;
    font-size: 1.5rem;
    margin-top: -20px;
    margin-left: -20px;
    margin-right: -20px;
}
    /* Dashboard Container */
    .container {
        width: 90%;
        max-width: 1000px;
        margin: 20px auto;
    }

    /* Marquee Notice */
    .marquee {
        background: #673ab7;
        color: white;
        padding: 10px;
        font-size: 1.1rem;
      
        text-align: center;
        font-weight: bold;
    }

    /* Latest Notice Section */
    .section {
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        margin: 20px 0;
        width: 100%;
     
        justify-content: center;
    }

    .section h2 {
        text-align: center;
        color: #673ab7;
        border-bottom: 2px solid #d1c4e9;
        padding-bottom: 5px;
        font-size: 1.4rem;
    }

    .notice ul {
        list-style: none;
        padding: 0;
    }

    .notice li {
        background: #f5f5f5;
        padding: 15px;
        margin-bottom: 10px;
       
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .notice p {
        font-size: 1rem;
        color: #333;
        text-align: center;
    }

    .notice .date {
        font-size: 0.9rem;
        color: #777;
        text-align: center;
    }

    /* To-Do List */
    .todo-card {
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        width: 100%;
        
    }

    .todo-card h2 {
        text-align: center;
        color: #673ab7;
        border-bottom: 2px solid #d1c4e9;
        padding-bottom: 5px;
        font-size: 1.4rem;
    }

    .todo ul {
        list-style: none;
        padding: 0;
    }

    .todo li {
        background: #e8f5e9;
        padding: 10px;
        margin-bottom: 10px;
        
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Task Description */
    .todo li .task-text {
        color: green;
        font-weight: bold;
    }

    /* Delete Button */
    .todo li .delete {
        background: red;
        color: white;
        border: none;
        padding: 8px 12px;
        
        cursor: pointer;
        transition: 0.3s;
    }

    .todo li .delete:hover {
        background: darkred;
    }

    /* Attendance Section */
    .attendance {
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        width: 100%;
       
    }

    .attendance h2 {
        text-align: center;
        color: #673ab7;
        border-bottom: 2px solid #d1c4e9;
        padding-bottom: 5px;
        font-size: 1.4rem;
    }

    .attendance p {
        font-size: 1rem;
        text-align: center;
        font-weight: bold;
    }

    .attendance p:nth-child(2) {
        color: green;
    }

    .attendance p:nth-child(3) {
        color: red;
    }

    /* Assigned Projects */
    .projects {
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        width: 100%;
       
    }

    .projects h2 {
        text-align: center;
        color: #673ab7;
        border-bottom: 2px solid #d1c4e9;
        padding-bottom: 5px;
        font-size: 1.4rem;
    }

    .projects ul {
        list-style: none;
        padding: 0;
    }

    .projects li {
        background: #f1f8e9;
        padding: 10px;
        margin-bottom: 10px;
        
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        font-weight: bold;
    }

    .new-task {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    margin-top: 20px;
}

.new-task input[type="text"] {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    font-size: 1rem;
   
}

.new-task button {
    padding: 10px 15px;
    background-color: #1a73e8;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.new-task button:hover {
    background-color: #1558b0;
}
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <section class="dashboard">
        <header>
            <i class="fa-solid fa-house icon"></i>
            Dashboard

        </header>
        <div class="container">
            <div class="marquee">
                <?php
             
                    $sql = "select * from notice ORDER BY date desc limit 1";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo"<marquee>$row[description],Published on:$row[date]</marquee>";
                    }
                    ?>

            </div>

            <div class="section notice">
                <h2>Latest Notice</h2>

                <?php
               
                    $sql = "select * from notice ORDER BY date desc limit 3";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo"<ul><li><p>$row[description]</p></li> <p class='date'>Published on:$row[date] </p><br></ul>";
                    }
                    ?>
            </div>
            <div class="todo-project">
                <!-- Updated To-Do List -->
                <div class="todo-card">
                    <h2>Todo List</h2>
                   
                    <form method="post" action="">
                    <div class="new-task">
                    <input type="text" name="employee_description" id="new-task-input" placeholder="Enter a new task">
                    <button type="submit">Add Task</button>
                </div>
                    </form>

                    <div class="todo">
                        <ul id="incomplete-tasks">
                            <?php
                         $sql = "SELECT * FROM employee_todo";
                         $result = mysqli_query($con, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<li class='todo-item'>
                      
                         <span class='task-text'>$row[employee_description]</span>
                         <a href='delete_task.php?employee_todo_id=" . $row['employee_todo_id'] . "'>
                            <button class='btn delete'>Delete</button>
                         </a>
                           </li>";
                            }
                          ?>
                        </ul>
                    </div>
                </div>


                <div class="section attendance">
                    <h2>Attendance</h2>
                    <p>Days Present: <?php echo $present_days; ?></p>
                    <p>Days Absent: <?php echo $absent_days; ?></p>

                </div>
<?php
                $employee_id = $_SESSION['employee_id']; // Assuming employee is logged in

$sql = "SELECT p.project_title, p.end_date 
        FROM project_employees pe
        JOIN project p ON pe.project_id = p.project_id
        WHERE pe.employee_id = '$employee_id'";

$result = mysqli_query($con, $sql);
?>

<div class="section projects">
    <h2>Assigned Projects</h2>
    <ul>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>{$row['project_title']} - Deadline: {$row['end_date']}</li>";
        }
        ?>
    </ul>
</div>
            </div>
    </section>
</body>
<script>
function updateTaskCount() {
    const count = document.getElementById('incomplete-tasks').childElementCount;
    document.getElementById('task-count').innerText = `You have ${count} pending task(s).`;
}
</script>

</html>