<?php
  $con = mysqli_connect("localhost", "root", "") or die(mysqli_error($con));
  mysqli_select_db($con, "hr") or die(mysqli_error($con));
?>
