<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $date = $_POST['date'];


  $sql = "INSERT INTO notice (title,description,date) 
          VALUES ('$title', '$description', '$date')";
    mysqli_query($con,  $sql);

    header("Location: notice.php");
    exit();
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Holiday</title>
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    /* Existing Dashboard and Form Styles */
    .add_holiday {
      margin-top: 80px;
      margin-left: 0;
      padding: 20px;
      transition: margin-left 0.4s ease;
      /* border: 3px solid green; */
      border-radius: 10px;
      max-width: 600px;
    }

    nav.open~.add_holiday {
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
    input[type="file"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="file"]:focus {
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
  <section class="add_holiday">
    <div class="add_holiday-container">
      <form method="post" action="">
        <h2>Notice</h2>
        <hr>
        <!-- New Notice Title Input -->
        <div class="form-group">
          <label for="notice_title">Notice Title</label>
          <input type="text" id="notice_title" name="title" placeholder="Enter notice title">
        </div>
        <div class="form-group">
          <label for="notice_description">Notice Description</label>
          <input type="text" id="notice_description" name="description" placeholder="Enter notice description">
        </div>
    
       
        <div class="form-group">
          <label for="Publish_date">Publish Date</label>
          <input type="date" id="Publish_date" name="date">
        </div>
        <div class="form-group">
          <input type="submit" value="Submit">
        </div>
      </form>
    </div>
  </section>

</body>
</html>
