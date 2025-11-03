<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

$searchQuery = isset($_POST['search_query']) ? $_POST['search_query'] : '';

$today = date('Y-m-d');

$query = "SELECT e.employee_name, a.date, a.sign_in 
          FROM employee e
          INNER JOIN attendance a 
          ON e.employee_id = a.employee_id 
          AND a.date = '$today'
          WHERE e.employee_name LIKE '$searchQuery%'";

$result = mysqli_query($con, $query) or die(mysqli_error($con));
$employees = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = [
            'name' => $row['employee_name'],
            'date' => $row['date'],
            'time' => $row['sign_in']
        ];
    }
}
    
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeName = $_POST['search-query'];
    $signOutTime = $_POST['sign-out'];
    $working_hours = $_POST['working-hour'];
    $date = $_POST['date'];

    // Fetch the employee ID based on the employee name
    $empQuery = "SELECT employee_id FROM employee WHERE employee_name = '$employeeName'";
    $empResult = mysqli_query($con, $empQuery);
    
    if ($empRow = mysqli_fetch_assoc($empResult)) {
        $employeeId = $empRow['employee_id'];

        // Update the sign-out time for today's attendance
        $updateQuery = "UPDATE attendance 
                        SET sign_out = '$signOutTime', working_hours = '$working_hours' 
                        WHERE employee_id = '$employeeId' 
                        AND date = '$date'";
        
        if (mysqli_query($con, $updateQuery)) {
              // Redirect to attendance.php after update
        header("Location: attendence.php");
        exit();
        }
    
}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Signout Attendance</title>


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
    .add_attendence {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_attendence {
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
    input[type="time"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="time"]:focus {
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

    <section class="add_attendence">
        <div class="attendance-container">
            <form method="post" action="">
                <h2>Attendance</h2>
                <hr>
                <div class="form-group">
                    <label for="employee">Employee</label>
                    <input type="text" id="search-query" name="search-query" placeholder="Search Employee Name">
                    <div id="search-results" class="suggestions"></div>

                </div>

                <div class="form-group">
                    <label for="date">Select Date:</label>
                    <input type="date" id="date" name="date" readonly>
                </div>

                <div class="form-group">
                    <label for="sign-in">Sign In Time</label>
                    <input type="time" id="sign-in" name="sign-in" readonly>
                </div>

                <div class="form-group">
                    <label for="sign-out">Sign Out Time</label>
                    <input type="time" id="sign-out" name="sign-out">
                </div>

                <div class="form-group">
                    <label for="working-hour">Working Hours</label>
                    <input type="text" id="working-hour" name="working-hour" placeholder="Enter Working Hours" readonly>
                </div>

                <div class="form-group">
                    <input type="submit">
                </div>
            </form>
        </div>
    </section>
    <script>
    $(document).ready(function() {
        // Search on input event
        $('#search-query').on('input', function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 0) { // Only search if input is not empty
                $.ajax({
                    type: 'POST',
                    url: 'search_employee_signout.php',
                    data: {
                        search_query: searchQuery
                    },
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
            var signInDate = $(this).data('date');
            var signInTime = $(this).data('time');

            $('#search-query').val(selectedName);
            $('#date').val(signInDate); // Auto-fill sign-in date
            $('#sign-in').val(signInTime); // Auto-fill sign-in time

            $('#search-results').hide(); // Hide dropdown after selection
        });

        // Calculate working hours when sign-out time is selected
        $('#sign-out').on('change', function() {
            calculateWorkingHours();
        });

        // Function to calculate working hours
        function calculateWorkingHours() {
            let signInTime = $('#sign-in').val();
            let signOutTime = $('#sign-out').val();

            if (signInTime && signOutTime) {
                let signIn = new Date("2000-01-01T" + signInTime);
                let signOut = new Date("2000-01-01T" + signOutTime);

                if (signOut > signIn) {
                    let timeDiff = (signOut - signIn) / (1000 * 60 * 60); // Convert milliseconds to hours
                    $('#working-hour').val(timeDiff.toFixed(2) + " hrs"); // Display with 2 decimal places
                } else {
                    alert("Sign-Out time must be later than Sign-In time!");
                    $('#sign-out').val(""); // Clear the incorrect value
                    $('#working-hour').val(""); // Clear duration field
                }
            }
        }

        // Hide search results if clicked outside
        $(document).click(function(e) {
            if (!$(e.target).closest('#search-query, #search-results').length) {
                $('#search-results').hide();
            }
        });
    });
    </script>

</body>


</html>