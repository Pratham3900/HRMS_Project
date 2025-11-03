<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['search-query'];
    $sign_in_time = $_POST['sign_in'];
    $date = date('Y-m-d'); // Auto-fetch today's date

    // Get Employee ID based on Name
    $query = "SELECT employee_id FROM employee WHERE employee_name = '$employee_name'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $employee_id = $row['employee_id'];

       
            // Insert Sign-In Record
            $insert_query = "INSERT INTO attendance (employee_id, date, sign_in) 
                             VALUES ('$employee_id', '$date', '$sign_in_time')";
            mysqli_query($con, $insert_query);

            header("Location: attendence.php");
            exit();
       
}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add attendance</title>
    </title>
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
            <form id="search-form" method="post" action="">
                <h2>Attendance</h2>
                <hr>
                <div class="form-group">
                    <label for="employee">Employee</label>
                    <input type="text" id="search-query" name="search-query" placeholder="Search Employee Name">
                    <div id="search-results" class="suggestions"></div>
                    
                </div>

                <div class="form-group">
                    <label for="date">Today Date:</label>
                    <input type="date" id="date" name="date">
                </div>

                <div class="form-group">
                    <label for="sign-in">Sign In Time</label>
                    <input type="time" id="sign-in" name="sign_in">
                </div>

                <div class="form-group">
                    <input type="submit">
                </div>
            </form>
        </div>
    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto-fill today's date
        const dateInput = document.getElementById("date");
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        dateInput.value = today;
        dateInput.setAttribute("readonly", true); // Make it read-only

        // Auto-fill current time in "Sign In Time"
        const signInInput = document.getElementById("sign-in");

        function setCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            signInInput.value = `${hours}:${minutes}`; // Set current time
        }
        setCurrentTime(); // Set time on load

    });
    </script>


</body>
<script>
$(document).ready(function() {
    // Search on input event
    $('#search-query').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 0) { // Only search if input is not empty
            $.ajax({
                type: 'POST',
                url: 'search_employee_signin.php',
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

</body>
</html>