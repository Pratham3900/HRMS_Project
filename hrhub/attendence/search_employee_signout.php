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
        
        $sql = "SELECT e.employee_id, e.employee_name, a.sign_in, a.date 
        FROM employee e 
        LEFT JOIN attendance a 
        ON e.employee_id = a.employee_id 
        WHERE a.date = '$today' 
        AND a.sign_in IS NOT NULL 
        AND a.sign_out IS NULL";  // Exclude those who already signed out

  $result = mysqli_query($con, $sql) or die(mysqli_error($con));

  // Check if rows exist
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p class='search-item' data-name='" . $row['employee_name'] . "' 
                                         data-date='" . $row['date'] . "' 
                                         data-time='" . $row['sign_in'] . "'>" 
                                         . $row['employee_name'] . 
             "</p>";
    }
} else {
    echo "<p>No results found.</p>";
}

?>