<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['search-query'];
    $leave_type_id = $_POST['leaveType'];
    $apply_date = $_POST['apply_date'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];

    // Fetch employee ID based on name
    $emp_query = "SELECT employee_id FROM employee WHERE employee_name = '$employee_name'";
    $emp_result = mysqli_query($con, $emp_query);
    $emp_row = mysqli_fetch_assoc($emp_result);
    $employee_id = $emp_row['employee_id'] ?? null;

    // Fetch leave type name based on ID
    $leave_query = "SELECT leave_type_name FROM leave_type WHERE leave_type_id = '$leave_type_id'";
    $leave_result = mysqli_query($con, $leave_query);
    $leave_row = mysqli_fetch_assoc($leave_result);
    $leave_type_name = $leave_row['leave_type_name'] ?? null;

    // Insert into leave table
    if ($employee_id && $leave_type_name) {
        $insert_query = "INSERT INTO `leave`(employee_id, leave_type_id,apply_date, start_date, end_date, duration) 
                         VALUES ('$employee_id','$leave_type_id','$apply_date', '$start_date', '$end_date', '$duration')";
        
        mysqli_query($con, $insert_query);
        header("Location: leave.php");
      exit();

    } 
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <style>
 /* General Styles */
 body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }


    /* Existing Dashboard and Form Styles */
    .apply_leave {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.apply_leave {
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
    input[type="date"],select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,select {
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
    
/* Dropdown Styling */
.suggestions {
    border: 1px solid #ccc;
    background: white;
    position: absolute;
    width: 33%;
    max-height: 150px;
    max-width: 600px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

.search-item {
    padding: 10px;
    cursor: pointer;
    
}

.search-item:hover {
    background: #007BFF;
    color: white;
}
    </style>

</head>

<body>

    <?php include '../nav.php'; ?>

    <section class="apply_leave">
        <form method="post" action="">
            <h2>Apply Leave</h2>
            <hr>
            <div class="form-group">
                <label for="employee">Employee</label>
                <input type="text" id="search-query" name="search-query" placeholder="Search Employee Name">
                <div id="search-results" class="suggestions"></div>
            </div>

            <div class="form-group">
                <label for="leaveType">Leave Type:</label>
                <select id="leaveType" name="leaveType" required>
                            <?php
                                // Fetch all departments
                                $sql = "SELECT * FROM leave_type";
                                $result = mysqli_query($con, $sql);
                                while ($num = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $num['leave_type_id'] . "'>" . $num['leave_type_name'] . "</option>";
                                }
                            ?>
                        </select>
            </div>

            <div class="form-group">
            <label for="apply-date">Apply Date:</label>
            <input type="date" id="apply-date" name="apply_date" required>
        </div>
        <div class="form-group">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" name="end_date" required>
        </div>

           
            <div class="form-group">
                            <label for="duration">Duration</label>
                            <input type="text" id="duration" name="duration" placeholder="Duration" readonly>
                        </div>

            <div class="form-group">
                <input type="submit">
            </div>
        </form>
     
    </section>

 
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("start-date").addEventListener("change", updateDateInfo);
    document.getElementById("end-date").addEventListener("change", updateDateInfo);
});

async function updateDateInfo() {
    let startDate = document.getElementById("start-date").value;
    let endDate = document.getElementById("end-date").value;

    if (!startDate || !endDate) return;

    let sDate = new Date(startDate);
    let eDate = new Date(endDate);
    
    if (eDate < sDate) {
        document.getElementById("duration").value = "";
        return;
    }

    try {
        let response = await fetch("get_holidays.php");
        
        if (!response.ok) {
            throw new Error("Network error: " + response.status);
        }

        let text = await response.text();
        console.log("Raw response:", text);  // Debug response

        let holidays = JSON.parse(text);
        console.log("Parsed holidays:", holidays);  // Debug parsed JSON

        let duration = 0;
        let currentDate = new Date(sDate);

        while (currentDate <= eDate) {
            let dayOfWeek = currentDate.getDay(); 
            let formattedDate = currentDate.toISOString().split('T')[0];

            if (dayOfWeek !== 0 && dayOfWeek !== 6 && !holidays.includes(formattedDate)) {
                duration++;
            }

            currentDate.setDate(currentDate.getDate() + 1);
        }

        document.getElementById("duration").value = duration;
    } catch (error) {
        console.error("Error fetching holidays:", error);
    }
}

</script>
<script>
$(document).ready(function() {
    // Search on input event
    $('#search-query').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 0) { // Only search if input is not empty
            $.ajax({
                type: 'POST',
                url: 'search_employee.php',
                data: { search_query: searchQuery },
                success: function(data) {
                    $('#search-results').html(data).show(); // Show suggestions
                }
            });
        } else {
            $('#search-results').hide(); // Hide dropdown if input is empty     
        }
    });

    // Select Employee Name when clicked
    $(document).on('click', '.search-item', function() {
        var selectedName = $(this).data('name');
        $('#search-query').val(selectedName);
        $('#search-results').hide(); // Hide dropdown after selection
    });

    // Hide search results if clicked outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#search-query, #search-results').length) {
            $('#search-results').hide();
        }
    });
});
</script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date().toISOString().split('T')[0];

            // Apply Date: Must be today or earlier
            document.getElementById("apply-date").setAttribute("max", today);

            // Start Date: Must be today or later
            document.getElementById("start-date").setAttribute("min", today);

            // End Date: Must be today or later
            document.getElementById("end-date").setAttribute("min", today);

            // Ensure End Date is after or equal to Start Date
             // Ensure End Date starts at least one day after Start Date
    document.getElementById("start-date").addEventListener("change", function () {
        let startDate = new Date(this.value);
        if (!isNaN(startDate.getTime())) {
            let nextDay = new Date(startDate);
            nextDay.setDate(nextDay.getDate() + 1);
            let formattedNextDay = nextDay.toISOString().split('T')[0];

            document.getElementById("end-date").setAttribute("min", formattedNextDay);
        }
            });
        });
    </script>

</body>
</html>
