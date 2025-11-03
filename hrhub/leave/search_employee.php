<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}
  // Connect to database
  include '../connection.php';

  // Get search query from AJAX request
  $searchQuery = $_POST['search_query'];

  // Get today's date
$today = date('Y-m-d');

// SQL query to get employees who have NOT signed in today
$query ="SELECT employee_id, employee_name FROM employee;
";

  $result = mysqli_query($con, $query) or die(mysqli_error($con));

  // Check if rows exist
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<p class='search-item' data-name='" . $row['employee_name'] . "'>" .$row['employee_name']. "</p>";
    }
  } else {
    echo "<p>No results found.</p>";
  }
?>
