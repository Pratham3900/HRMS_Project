<?php
session_start();
include '../connection.php';
$current_date = date('Y-m-d');

// Fetch employees who have NOT signed in and are NOT on leave (including multi-day leaves)
$sql = "SELECT e.employee_id, e.employee_name 
FROM employee e 
LEFT JOIN attendance a 
    ON e.employee_id = a.employee_id 
    AND a.date = '$current_date'
LEFT JOIN `leave` l 
    ON e.employee_id = l.employee_id 
    AND ('$current_date' BETWEEN l.start_date AND l.end_date)  
    AND NOT (l.end_date = '$current_date')  -- Allow return on the last day of leave
WHERE a.sign_in IS NULL 
AND (l.employee_id IS NULL OR l.end_date = '$current_date'); -- Include employees returning on last day
"; 

$result = mysqli_query($con, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sign_in_time = $_POST['sign_in_time'];
    $attendance_date = $_POST['attendance_date'];
    $employee_ids = $_POST['employee_ids']; // Array of selected employee IDs

    if (!empty($employee_ids)) {
        foreach ($employee_ids as $employee_id) {
            // Check if record already exists
            $check_sql = "SELECT * FROM attendance WHERE employee_id = '$employee_id' AND date = '$attendance_date'";
            $check_result = mysqli_query($con, $check_sql);

            if (mysqli_num_rows($check_result) == 0) {
                // Insert new attendance record
                $insert_sql = "INSERT INTO attendance (employee_id, date, sign_in) 
                               VALUES ('$employee_id', '$attendance_date', '$sign_in_time')";
                mysqli_query($con, $insert_sql);
            }
        }
        header("Location: attendence.php");
        exit();
    } else {
        echo "<script>alert('No employees selected!'); window.location.href='bulk_signin.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Sign In</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .add_attendence {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 80px auto;
            text-align: left;
        }

        /* Heading */
        h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 15px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        /* Input Fields */
        input[type="time"], input[type="date"], input[type="text"] {
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
        }

        /* Table Styling */
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

        /* Checkbox Styling */
        input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
        }

        /* Buttons */
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

        /* Button Container */
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
    <script>
        // Select all checkboxes
        function selectAll() {
            document.querySelectorAll('.checkbox').forEach(cb => cb.checked = true);
        }

        // Unselect all checkboxes
        function unselectAll() {
            document.querySelectorAll('.checkbox').forEach(cb => cb.checked = false);
        }

        // Search employee names
        function searchEmployee() {
            let input = document.getElementById("searchBox").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let name = row.cells[1].innerText.toLowerCase();
                row.style.display = name.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>
<?php include '../nav.php'; ?>
<section class="add_attendence">
    <h2>Bulk Sign In</h2>
    <form  method="POST">
        <label>Sign-in Time:</label>
        <input type="time" name="sign_in_time" required>

        <label>Date:</label>
        <input type="date" name="attendance_date" value="<?= $current_date ?>" readonly>

        <label>Search Absent Employee:</label>
        <input type="text" id="searchBox" placeholder="Search Absent employee..." onkeyup="searchEmployee()">

        <div class="btn-container">
            <button type="button" class="btn" onclick="selectAll()">Select All</button>
            <button type="button" class="btn" onclick="unselectAll()">Unselect All</button>
        </div>

        <table>
            <thead>
                <tr><th>Select</th><th>Employee Name</th></tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><input type="checkbox" name="employee_ids[]" value="<?= $row['employee_id'] ?>" class="checkbox"></td>
                        <td><?= $row['employee_name'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <button type="submit" class="btn">Submit Sign In</button>
    </form>
</section>
</body>
</html>
