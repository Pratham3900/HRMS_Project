<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
include '../connection.php';

// Check if employee_id is set
if (!isset($_GET['employee_id'])) {
    header("location: employee.php");
    exit();
}

$employee_id = $_GET['employee_id'];

// Fetch employee details
$sql = "SELECT * FROM employee WHERE employee_id = '$employee_id'";
$result = mysqli_query($con, $sql);
$employee = mysqli_fetch_assoc($result);

if (!$employee) {
    header("location: employee.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name =  $_POST['employee_name'];
    $email =  $_POST['email'];
    $contact =  $_POST['contact'];
    $dob =  $_POST['dob'];
    $hire_date=$_POST['hire_date'];
    $nationality =  $_POST['nationality'];
    $marital_status =  $_POST['marital_status'];
    $address =  $_POST['address'];
    $gender =  $_POST['gender'];
    $department_id =  $_POST['department_id'];
    $designation_id =  $_POST['designation_id'];

    $update_sql = "UPDATE employee SET 
                    employee_name='$employee_name', 
                    email='$email', 
                    contact='$contact', 
                    dob='$dob', 
                    hire_date='$hire_date',
                    nationality='$nationality', 
                    marital_status='$marital_status', 
                    address='$address', 
                    gender='$gender', 
                    department_id='$department_id', 
                    designation_id='$designation_id' 
                  WHERE employee_id='$employee_id'";

    if (mysqli_query($con, $update_sql)) {
        header("location: employee.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    /* Existing Dashboard and Form Styles */
    .edit_employee {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border: 3px solid green; */
        border-radius: 10px;

        max-width: 600px;
    }

    nav.open~.edit_employee {
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
    <?php include '../nav.php'; ?>
    <section class="edit_employee">

        <div>
            <form method="POST">
                <h2>Edit Employee</h2>
                <hr>

                <div class="form-group">
                    <label for="employee">Employee Name</label>
                    <input type="text" id="employee" name="employee_name"
                        value="<?php echo $employee['employee_name']; ?>" placeholder="Enter employee name" readonly required>
                        <b id="name_error" style="color: red;"></b>
                    </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" readonly name="email" id="email" value="<?php echo $employee['email']; ?>" required>
                    <b class="email_error" style="color: red;"></b>
               
<b id="email_error" style="color: red;"></b>
                </div>

                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" maxlength="10" id="contact" name="contact" value="<?php echo $employee['contact']; ?>" required>
                    <b id="contact_error" style="color: red;"></b>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="<?php echo $employee['dob']; ?>" required>
                    <b id="dob_error" style="color: red;"></b>
                </div>
                <div class="form-group">
                    <label for="hire_date">Hire Date</label>
                    <input type="date" id="hire_date" name="hire_date" value="<?php echo $employee['hire_date']; ?>" required>
                    
                </div>

                <div class="form-group">

                    <label>Nationality</label>
                    <input type="text" name="nationality" value="<?php echo $employee['nationality']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Marital Status</label>
                    <select name="marital_status" required>
                        <option value="Single" <?php if ($employee['marital_status'] == 'Single') echo 'selected'; ?>>
                            Single
                        </option>
                        <option value="Married" <?php if ($employee['marital_status'] == 'Married') echo 'selected'; ?>>
                            Married
                        </option>
                        <option value="Divorced"
                            <?php if ($employee['marital_status'] == 'Divorced') echo 'selected'; ?>>
                            Divorced</option>
                        <option value="Widowed" <?php if ($employee['marital_status'] == 'Widowed') echo 'selected'; ?>>
                            Widowed
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo $employee['address']; ?>" required>
                </div>

                <div class="form-group">

                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male" <?php if ($employee['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($employee['gender'] == 'Female') echo 'selected'; ?>>Female
                        </option>
                        <option value="Other" <?php if ($employee['gender'] == 'Other') echo 'selected'; ?>>Other
                        </option>
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
                    <label>Designation</label>
                    <select name="designation_id" required>
                        <?php
                $des_sql = "SELECT * FROM designation";
                $des_result = mysqli_query($con, $des_sql);
                while ($row = mysqli_fetch_assoc($des_result)) {
                    echo "<option value='" . $row['designation_id'] . "'";
                    if ($employee['designation_id'] == $row['designation_id']) echo " selected";
                    echo ">" . $row['designation_name'] . "</option>";
                }
                ?>
                    </select>

                </div>

                <div class="form-group">
                    <input type="submit" value="Update Employee">
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
        if ($("#name_error").text() || $("#email_error").text() ||$(".email_error").text() || $("#contact_error").text() || $("#dob_error").text() || $("#salary_error").text()) {
            e.preventDefault(); // Prevent form submission if errors exist
            alert("Please correct the errors before submitting the form.");
        }
    });
});


</script>

</html>