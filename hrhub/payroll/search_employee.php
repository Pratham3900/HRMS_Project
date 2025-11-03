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
// Fetch employees 
$sql = "SELECT employee_name 
        FROM employee 
        WHERE employee_name LIKE '$searchQuery%' 
        OR employee_name LIKE '%$searchQuery%' 
        ORDER BY 
        CASE 
            WHEN employee_name LIKE '$searchQuery%' THEN 1  -- Exact match comes first
            ELSE 2 
        END, employee_name ASC";  // Sort alphabetically after exact matches
     

  $result = mysqli_query($con, $sql) or die(mysqli_error($con));

  // Check if rows exist
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<p class='search-item' data-name='" . $row['employee_name'] . "'>" .$row['employee_name']. "</p>";
    }
  } else {
    echo "<p>No results found.</p>";
  }
?>
