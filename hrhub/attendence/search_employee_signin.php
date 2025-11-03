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
// SQL query to get employees who have NOT signed in today and are NOT on leave
$query = "SELECT e.employee_name 
FROM employee e
LEFT JOIN attendance a 
    ON e.employee_id = a.employee_id 
    AND a.date = '$today'
LEFT JOIN `leave` l 
    ON e.employee_id = l.employee_id 
    AND ('$today' BETWEEN l.start_date AND l.end_date)  
    AND NOT (l.end_date = '$today')  -- Allow return on the last leave day
WHERE e.employee_name LIKE '$searchQuery%' 
AND a.employee_id IS NULL 
AND (l.employee_id IS NULL OR l.end_date = '$today'); -- Include employees returning on last day
";  // Exclude employees on leave

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
