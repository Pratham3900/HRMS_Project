<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';
// Check if employee_id is set
if (!isset($_GET['employee_id'])) {
  header("location: payroll.php");
  exit();
}

$employee_id = $_GET['employee_id'];
$present_days = $_GET['present_days'];
$total_hours = $_GET['total_hours'];
$salary = $_GET['salary'];


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form data
  $employee_id = $_POST['employee_id'];
  $present_days = $_POST['present_days'];
  $total_hours = $_POST['total_hours'];
  $salary = $_POST['salary'];
  $payment_mode = $_POST['payment_mode'];
  $month_year = $_POST['salary_month_year'];
  $payment_mode = $_POST['payment_mode'];
  $pay_date = $_POST['pay_date']; // Capture pay_date from form
  $cheque_number = $_POST['cheque_number'] ?? null;
  $razorpay_payment_id = $_POST['razorpay_payment_id'] ?? null;
  
  // Handle different payment modes
  if ($payment_mode == "cash") {
      $razorpay_payment_id = "Not Applicable";  // Store "Not Applicable" for cash
  }  elseif ($payment_mode == "DBT") {
    $razorpay_payment_id =  "Not Applicable";  // Store cheque number for cheque
}
  elseif ($payment_mode == "cheque") {
      $razorpay_payment_id = $cheque_number;  // Store cheque number for cheque
  }
  
  // Insert query
  $query = "INSERT INTO payroll (employee_id, present_days, total_hours, salary, payment_mode, month_year, pay_date,razorpay_payment_id) 
            VALUES ('$employee_id', '$present_days', '$total_hours', '$salary', '$payment_mode', '$month_year', '$pay_date', '$razorpay_payment_id')";

  // Execute query
  if (mysqli_query($con, $query)) {
      // Redirect to payroll2.php after successful insertion
      header("location: payroll.php");
      exit();
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
    <title>Generate Salary</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    /* Dashboard/Form Container */
    .genrate_salary {
        margin-top: 80px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        border-radius: 10px;
        max-width: 600px;

    }

    nav.open~.genrate_salary {
        margin-left: 200px;
    }

    form {
        background: #fff;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
    }

    hr {
        border: 2px solid #5f3a99;
        margin: 20px -20px;
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
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Radio button group styling */
    .radio-group {
        display: flex;
        gap: 20px;
        align-items: center;
        margin-top: 5px;
    }

    .radio-group label {
        font-weight: normal;
        margin-right: 10px;
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

    <section class="genrate_salary">
        <div>
            <form method="post" action="" id="salary-form">
                <h2>Salary Arrangement</h2>
                <hr>
                <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">
                <div class="form-group">
                    <label for="employee">Employee Id</label>
                    <input required type="text" id="employee" name="employee_id" value="<?php echo $employee_id; ?>"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="basic-salary">Basic Salary</label>
                    <input required type="text" id="basic-salary" name="salary" value="<?php echo $salary; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="working-hour">Working Hours</label>
                    <input required type="text" id="working-hour" name="total_hours" value="<?php echo $total_hours ?>"
                        readonly>
                </div>
                <div class="form-group">
                    <label for="Present-Days">Present Days (in a month)</label>
                    <input required type="text" id="Present-Days" name="present_days"
                        value="<?php echo $present_days; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="pay-date">Pay Date</label>
                    <input required type="date" id="pay-date" name="pay_date" placeholder="Enter pay date">
                </div>

                <!-- Radio Button Group for Salary Status -->
                <div class="form-group">
                    <label>Payment Mode</label>
                    <div class="radio-group">
                    <label>
                            <input type="radio" name="payment_mode" value="cash" checked onchange="toggleChequeInput()">
                            Cash
                        </label>
                        <label>
                            <input type="radio" name="payment_mode" value="DBT" onchange="toggleChequeInput()">
                            DBT
                        </label>
                        <label>
                            <input type="radio" name="payment_mode" value="cheque" onchange="toggleChequeInput()">
                            Cheque
                        </label>
                        <label>
                            <input type="radio" name="payment_mode" value="online" onchange="toggleChequeInput()">
                            Online
                        </label>
                    </div>
                </div>

                <!-- Hidden input for cheque number (only shown if "Cheque" is selected) -->
                <div class="form-group" id="cheque-number-group" style="display: none;">
                    <label for="cheque-number">Enter Cheque Number</label>
                    <input type="text" id="cheque-number" name="cheque_number" required>
                </div>

                <div class="form-group">
                    <label for="salary-month-year">Select Month & Year</label>
                    <select id="salary-month-year" name="salary_month_year" required>
                        <?php
      $currentYear = date('Y'); // Get current year
      $currentMonthNum = date('n'); // Get current month number (1-12)
      $months = [
        "January", "February", "March", "April", "May", "June", 
        "July", "August", "September", "October", "November", "December"
      ];

      for ($i = 0; $i < 12; $i++) { 
        $monthIndex = ($currentMonthNum + $i - 1) % 12; // Get correct month index
        $year = $currentYear + floor(($currentMonthNum + $i - 1) / 12); // Adjust year if needed
        $monthName = $months[$monthIndex]; // Get month name
        
        $selected = ($i == 0) ? "selected" : ""; // Select the current month by default
        echo "<option name='salary_month_year'  value='$monthName $year' $selected>$monthName $year</option>";
      }
    ?>
                    </select>
                </div>

        </div>
        <div class="form-group">
            <input type="submit" id="submit-btn" value="Submit">

        </div>
        </form>
        </div>
    </section>

</body>
<script>
function toggleChequeInput() {
    let paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;
    let chequeInputGroup = document.getElementById("cheque-number-group");
    let chequeInput = document.getElementById("cheque-number");

    if (paymentMode === "cheque") {
        chequeInputGroup.style.display = "block"; // Show input box
        chequeInput.setAttribute("required", "true"); // Add required attribute
    } else {
        chequeInputGroup.style.display = "none"; // Hide input box
        chequeInput.removeAttribute("required"); // Remove required attribute
    }
}

$(document).ready(function() {
    $("#submit-btn").click(function(e) {
        e.preventDefault(); // Prevent normal form submission

        let paymentMode = $("input[name='payment_mode']:checked").val();
        let payDate = $("#pay-date").val();
        let chequeNumber = $("#cheque-number").val();

        // Check if Pay Date is empty
        if (!payDate) {
            alert("Please select a Pay Date before submitting.");
            return;
        }

        // If payment mode is cheque, enforce cheque number validation
        if (paymentMode === "cheque" && chequeNumber.trim() === "") {
            alert("Please enter the cheque number.");
            return;
        }

        if (paymentMode === "online") {
            let salary = $("#basic-salary").val();
            let employeeId = $("#employee").val();

            $.ajax({
                type: "POST",
                url: "razorpay_payment.php",
                data: {
                    salary: salary * 100,
                    employeeId: employeeId
                },
                success: function(res) {
                    var order_id = JSON.parse(res).order_id;
                    var amount = JSON.parse(res).amount;
                    startPayment(order_id, amount);
                },
                error: function() {
                    alert("Payment initialization failed.");
                }
            });
        } else {
            $("#salary-form").submit(); // Submit form normally for Cash/Cheque
        }
    });
});

function startPayment(order_id, amount) {
    var options = {
        key: "rzp_test_3FUccOHkA69Y6O", // Replace with your Razorpay Key ID
        amount: amount,
        currency: "INR",
        name: "HR Payroll System",
        description: "Salary Payment",
        image: "https://cdn.razorpay.com/logos/GhRQcyean79PqE_medium.png",
        order_id: order_id,
        prefill: {
            name: "Pratham danawala",
            email: "prathamdanawala@gmail.com",
            contact: "9510993484"
        },
        notes: {
            address: "HR Payroll System"
        },
        theme: {
            "color": "#3399cc"
        },
        handler: function(response) {
            // Store the Razorpay payment ID in the hidden field
            $("#razorpay_payment_id").val(response.razorpay_payment_id);

            // Submit the form after successful payment
            $("#salary-form").submit();
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();

    rzp.on('payment.failed', function(response) {
        alert(response.error.reason);
    });
}
</script>

</html>