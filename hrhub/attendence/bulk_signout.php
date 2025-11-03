<?php
session_start();
include '../connection.php';
$current_date = date('Y-m-d');

$sql = "SELECT e.employee_id, e.employee_name, a.sign_in 
        FROM employee e 
        LEFT JOIN attendance a 
        ON e.employee_id = a.employee_id 
        WHERE a.date = '$current_date' 
        AND a.sign_in IS NOT NULL 
        AND a.sign_out IS NULL";  // Exclude those who already signed out


$result = mysqli_query($con, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sign_out_time = $_POST['sign_out_time'];
    $attendance_date = $_POST['attendance_date'];
    $duration = $_POST['duration'];
    $employee_ids = $_POST['employee_ids']; // Array of selected employee IDs

    if (!empty($employee_ids)) {
        foreach ($employee_ids as $employee_id) {
            // Get the existing sign-in time and date
            $query = "SELECT sign_in, date FROM attendance WHERE employee_id = '$employee_id' AND date = '$attendance_date' AND sign_in IS NOT NULL";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
    
            if ($row) {
                $sign_in_time = $row['sign_in'];
                $sign_in_date = $row['date']; // Use the correct sign-in date
    
                // Calculate working hours correctly
                $sign_in_timestamp = strtotime("$sign_in_date $sign_in_time");
                $sign_out_timestamp = strtotime("$attendance_date $sign_out_time");
                $duration_seconds = $sign_out_timestamp - $sign_in_timestamp;
    
                if ($duration_seconds > 0) {
                    $hours = floor($duration_seconds / 3600);
                    $minutes = floor(($duration_seconds % 3600) / 60);
                    $working_hours = sprintf("%02d:%02d", $hours, $minutes);
                } else {
                    $working_hours = "00:00"; // Default if calculation fails
                }
    
                // Update sign-out time and working hours in the database
                $update_sql = "UPDATE attendance 
                               SET sign_out = '$sign_out_time', working_hours = '$working_hours' 
                               WHERE employee_id = '$employee_id' 
                               AND date = '$sign_in_date' 
                               AND sign_in IS NOT NULL";
                mysqli_query($con, $update_sql);
            }
        }
        // Redirect to attendance.php after update
        header("Location: attendence.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bulk Sign Out</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .add_attendence {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            margin: 80px auto;
            text-align: left;
        }

        h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 15px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="time"], input[type="date"], input[type="text"] {
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #3333ff;
            color: white;
        }

        .btn {
            background-color: #3333ff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #3385ff;
        }
    </style>
    <script>
       
        function searchEmployee() {
            let input = document.getElementById("searchBox").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let name = row.cells[1].innerText.toLowerCase();
                row.style.display = name.includes(input) ? "" : "none";
            });
        }

        function selectAll() {
            document.querySelectorAll('.checkbox').forEach(cb => cb.checked = true);
        }

        function unselectAll() {
            document.querySelectorAll('.checkbox').forEach(cb => cb.checked = false);
        }
    </script>
</head>

<body>
<?php include '../nav.php'; ?>
<section class="add_attendence">
    <h2>Bulk Sign Out</h2>
    <form method="POST">
        <label>Sign-out Time:</label>
        <input type="time" id="signOutTime" name="sign_out_time" required oninput="calculateDuration()">
         
  
        <label>Date:</label>
        <input type="date" name="attendance_date" value="<?= date('Y-m-d') ?>" readonly>

        <label>Search Employee:</label>
        <input type="text" id="searchBox" placeholder="Search employee..." onkeyup="searchEmployee()">
        
        <button type="button" class="btn" onclick="selectAll()">Select All</button>
        <button type="button" class="btn" onclick="unselectAll()">Unselect All</button>
         
        <table>
            <thead>
                <tr><th>Select</th><th>Employee Name</th><th>Sign In Time</th></tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr data-signin="<?= $row['sign_in'] ?>">
                        <td><input type="checkbox" name="employee_ids[]" value="<?= $row['employee_id'] ?>" class="checkbox"></td>
                        <td><?= $row['employee_name'] ?></td>
                        <td><?= $row['sign_in'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <button type="submit" class="btn">Submit Sign Out</button>
    </form>
</section>
</body>

</html>
