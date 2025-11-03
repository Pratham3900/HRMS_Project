<?php
include 'connection.php';

$holidays = [];
$query = "SELECT start_date FROM holiday";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $holidays[] = $row['start_date'];
}

echo json_encode($holidays);
?>
