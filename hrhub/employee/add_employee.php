<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
function sendEmail($to_email, $employee_name, $password, $salary, $department_name,$designation_name) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'hrmsbyprathamdanawala@gmail.com'; // Your Gmail address
        $mail->Password = 'hqxc hdhg bdpm mtnc';  // Your email app password (Use App Password, Not Normal Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender & Recipient
        $mail->setFrom('hrmsbyprathamdanawala@gmail.com', 'HR Department');
        $mail->addAddress($to_email, $employee_name);

        // Email Content
        // Email Content
$mail->isHTML(true);
$mail->Subject = 'Welcome to HRMS - Employee Login Credentials';
$mail->Body    = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f7fa;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .header {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: white;
      text-align: center;
      padding: 25px 10px;
    }
    .header h2 {
      margin: 0;
      font-size: 24px;
      letter-spacing: 1px;
    }
    .content {
      padding: 25px;
      color: #333333;
    }
    .content h3 {
      color: #2575fc;
      margin-top: 0;
    }
    .details, .credentials {
      background: #f9f9f9;
      padding: 15px;
      border-radius: 8px;
      margin: 15px 0;
    }
    .details li, .credentials li {
      margin: 8px 0;
      font-size: 15px;
    }
    .credentials li b {
      color: #6a11cb;
    }
    .footer {
      background: #2575fc;
      color: white;
      text-align: center;
      padding: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>
      <h2>Welcome to HRMS 🚀</h2>
    </div>
    <div class='content'>
      <h3>Hello, $employee_name 👋</h3>
      <p>We are excited to have you onboard! Here are your details:</p>
      
      <div class='details'>
        <ul>
          <li><b>Department:</b> $department_name</li>
          <li><b>Designation:</b> $designation_name</li>
          <li><b>Salary (Per Month):</b> ₹$salary</li>
        </ul>
      </div>
      
      <p>Your login credentials are as follows:</p>
      
      <div class='credentials'>
        <ul>
          <li><b>Username:</b> $to_email</li>
          <li><b>Password:</b> $password</li>
        </ul>
      </div>
      
      <p style='color:#ff5722;font-weight:bold;'>⚠️ Please change your password after your first login for security reasons.</p>
      
      <p>We wish you great success ahead!</p>
      <p>Best Regards,<br><b>HR Team</b></p>
    </div>
    <div class='footer'>
      © ".date('Y')." HRMS | All Rights Reserved
    </div>
  </div>
</body>
</html>
";

        // Send Email
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name =  $_POST['employee_name'];
    $email =  $_POST['email'];
    $contact =  $_POST['contact'];
    $dob =  $_POST['dob'];
    $hire_date =  $_POST['hire_date'];
    $nationality =  $_POST['nationality'];
    $marital_status =  $_POST['marital_status'];
    $address =  $_POST['address'];
    $gender =  $_POST['gender'];
    $department_id =  $_POST['department_id'];
    $designation_id =  $_POST['designation_id'];
    $password = "Employee@123";

    // Fetch department and designation names
    $dept_query = "SELECT department_name FROM department WHERE department_id = '$department_id'";
    $dept_result = mysqli_query($con, $dept_query);
    $dept_row = mysqli_fetch_assoc($dept_result);
    $department_name = $dept_row['department_name'];

 // Fetch designation name and base salary
$des_query = "SELECT designation_name, base_salary FROM designation WHERE designation_id = '$designation_id'";
$des_result = mysqli_query($con, $des_query);
$des_row = mysqli_fetch_assoc($des_result);
$designation_name = $des_row['designation_name'];
$salary = $des_row['base_salary'];   // <-- salary fetched here
    // Insert new employee
    $sql = "INSERT INTO employee (employee_name, email, contact, dob, nationality, marital_status, address, gender, department_id, designation_id, password, hire_date) 
            VALUES ('$employee_name', '$email', '$contact', '$dob', '$nationality', '$marital_status', '$address', '$gender', '$department_id', '$designation_id', '$password', '$hire_date')";

    if (mysqli_query($con, $sql)) {
        $employee_id = mysqli_insert_id($con); // Get the newly inserted employee ID

        // Fetch all leave types to initialize leave balance
        $leave_query = "SELECT leave_type_id, number_of_days FROM leave_type";
        $leave_result = mysqli_query($con, $leave_query);

        while ($leave = mysqli_fetch_assoc($leave_result)) {
            $leave_type_id = $leave['leave_type_id'];
            $total_days = $leave['number_of_days'];

            // Insert into employee_leave_balance
            $insert_balance = "INSERT INTO employee_leave_balance (employee_id, leave_type_id, used_leaves, remaining_leaves) 
                               VALUES ('$employee_id', '$leave_type_id', 0, '$total_days')";
            mysqli_query($con, $insert_balance);
        }

        // Send Email to Employee
        sendEmail($email, $employee_name, $password, $salary, $department_name, $designation_name);

        // Redirect after successful addition
        header("location: employee.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
        

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
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
    .add_employee {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.add_employee {
        margin-left: 200px;
    }


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
    input[type="email"],
    select,
    input[type="textarea"],
    input[type="date"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    select:focus,
    input[type="date"]:focus,
    input[type="textarea"]:focus {
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
    <?php 
    include '../nav.php';
    ?>

    <section class="add_employee">

        <div>
            <form method="POST">
                <h2>Employees</h2>
                <hr>

                <div class="form-group">
                    <label for="employee">Employee Name</label>
                    <input type="text" id="employee" name="employee_name" placeholder="Enter employee name" required>
                    <b id="name_error" style="color: red;"></b>


                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required>
                    <b class="email_error" style="color: red;"></b>
               
<b id="email_error" style="color: red;"></b>
                </div>

                <div class="form-group">
                    <label for="contact">Contact</label>
                    <input type="text" maxlength="10" id="contact" name="contact" placeholder="Enter contact number" required>
                    <b id="contact_error" style="color: red;"></b>
                </div>

                <div class="form-group">
                    <label for="dob">Date Of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                    <b id="dob_error" style="color: red;"></b>
                </div>

                <div class="form-group">
                    <label for="hire_date">Hire Date</label>
                    <input type="date" id="hire_date" name="hire_date" required>
                    
                </div>


                <div class="form-group">
                    <label for="nationality">Nationality</label>
                    <input type="text" id="nationality" name="nationality" placeholder="Enter Nationality" required>
                </div>

                <div class="form-group">
                    <label for="marital_status">Marital Status</label>
                    <select id="marital_status" name="marital_status" required>
                        <option>Single</option>
                        <option>Married</option>
                        <option>Divorced</option>
                        <option>Widowed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter Address" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department_id">Select Department</label>
                    <select id="department_id" name="department_id" required>
                        <?php
            $sql = "SELECT * FROM department";
            $result = mysqli_query($con, $sql);
            while ($num = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $num['department_id'] . "'>" . $num['department_name'] . "</option>";
            }
            ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="designation_id">Select Designation</label>
                    <select id="designation_id" name="designation_id" required>
                        <?php
            $sql = "SELECT * FROM designation";
            $result = mysqli_query($con, $sql);
            while ($num = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $num['designation_id'] . "'>" . $num['designation_name'] . "</option>";
            }
            ?>
                    </select>
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Add Employee">
                </div>
            </form>

        </div>
    </section>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#email").keyup(function() {  // Use the correct ID
        var email = $("#email").val();  // Get the email value
        console.log(email);  

        $.ajax({
            type: "POST",
            url: "email_check.php",  // Ensure this file exists in the same directory
            data: {
                'check_emailbtn': 1,
                'email': email
            },
            success: function(response) {
                console.log(response);
                $(".email_error").text(response);  // Correctly display the response
            }
        });
    });
});
$(document).ready(function () {
    // Function for name validation (only characters and spaces allowed)
    function validateName() {
        var value = $("#employee").val();
        var regex = /^[a-zA-Z ]*$/;
        if (!regex.test(value)) {
            $("#name_error").text("*Please enter characters only");
            return false;
        } else {
            $("#name_error").text("");
            return true;
        }
    }

    // Function for contact validation (only digits, exactly 10 digits)
    function validateContact() {
        var value = $("#contact").val();
        var regex = /^[0-9]*$/;
        if (!regex.test(value)) {
            $("#contact_error").text("*Only digits are allowed");
            return false;
        } else if (value.length !== 10) {
            $("#contact_error").text("*Please enter a 10-digit phone number");
            return false;
        } else {
            $("#contact_error").text("");
            return true;
        }
    }

    // Function for email validation
    function validateEmail() {
        var email = $("#email").val();
        var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (!emailRegex.test(email)) {
            $("#email_error").text("*Please enter a valid email address");
            return false;
        } else {
            $("#email_error").text("");
            return true;
        }
    }
     // Date of Birth Validation (Must be at least 18 years old)
     $("#dob").change(function () {
        var dob = new Date($(this).val());
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        if (age < 18) {
            $("#dob_error").text("*Employee must be at least 18 years old");
        } else {
            $("#dob_error").text("");
        }
    });

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



    // Attach validation functions to input fields
    $("#employee").on("input", validateName);
    $("#contact").on("input", validateContact);
    $("#email").on("input", validateEmail);

      // Form Submission Validation
      $("form").submit(function (e) {
        if ($("#name_error").text() || $("#email_error").text()  ||$(".email_error").text() || $("#contact_error").text() || $("#dob_error").text() || $("#salary_error").text()) {
            e.preventDefault(); // Prevent form submission if errors exist
            alert("Please correct the errors before submitting the form.");
        }
    });
});


</script>

</html>