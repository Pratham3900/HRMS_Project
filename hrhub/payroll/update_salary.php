<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}



// Check if ID is set
if (!isset($_GET['employee_id'])) {
    header("Location: payroll2.php");
    exit();
}

$employee_id = $_GET['employee_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../connection.php';

    $employee_id = $_POST['employee_id'];
    $salary = $_POST['salary'];

    $query = "UPDATE employee SET salary = '$salary' WHERE employee_id = '$employee_id'";
   
    if (mysqli_query($con,$query )) {
        header("location: payroll2.php");
        exit();
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add salary</title>
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    /* Existing Dashboard and Form Styles */
    .add_salary {
      margin-top: 80px;
      margin-left: 0;
      padding: 20px;
      transition: margin-left 0.4s ease;
      /* border: 3px solid green; */
      border-radius: 10px;
      max-width: 600px;
    }

    nav.open~.add_salary {
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

    input[type="text"]{
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      transition: 0.3s;
    }

    input[type="text"]:focus{
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // Salary Validation
    $("#salary").keyup(function () {
        var salary = $(this).val();
        var regex = /^[0-9]+$/;

        if (!regex.test(salary)) {
            $("#salary_error").text("*Only numbers are allowed");
        } else if (salary < 1000) {
            $("#salary_error").text("*Salary cannot be less than 1000");
        } else if (salary > 300000) {
            $("#salary_error").text("*Salary cannot exceed 300000");
        } else {
            $("#salary_error").text("");
        }
    });


      // Form Submission Validation
      $("form").submit(function (e) {
        if ($("#salary_error").text()) {
            e.preventDefault(); // Prevent form submission if errors exist
            alert("Please correct the errors before submitting the form.");
        }
    });
});


</script>
</head>
<body>

  <?php 
    include '../nav.php';
  ?>
  <section class="add_salary">
    <div class="add_salary-container">
      <form method="post" action="">
        <h2>Add Salary</h2>
        <hr>
        <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
        <div class="form-group">
        <label>Salary:</label>
        <input type="text" id="salary" name="salary" placeholder="10000" required>

     <b id="salary_error" style="color: red;"></b>
        </div>
        <div class="form-group">
          <input type="submit" value="Submit">
        </div>
      </form>
    </div>
  </section>

</body>
</html>
