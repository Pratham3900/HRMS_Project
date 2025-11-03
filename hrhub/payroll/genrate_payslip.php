<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Generate Payslip</title>
  <style>
    /* General Styles */
body {
  font-family: Arial, sans-serif;
  background-color: #f5f8fa;
  margin: 0;
  padding: 0;
}

/* Section and Container */
.genrate_payslip {
  margin-top: 80px;
  padding: 20px;
  transition: margin-left 0.4s ease;
  border-radius: 10px;
}

nav.open ~ .genrate_payslip {
  margin-left: 200px;
}

.container {
  width: 100%;
  margin: auto;
  padding: 20px;
}

/* Card Styles */
.card {
  background: white;
  padding: 20px;
  margin-bottom: 15px;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card h2 {
  margin: 0;
  font-size: 25px;
  color: #333;
}

hr {
  border: 2px solid #5f3a99;
  margin: 20px -20px;
}

/* Input Group */
.input-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
  margin-top: 10px;
}

.input-group label {
  white-space: nowrap;
  font-weight: bold;
  color: #5f3a99;
  min-width: 100px;
}

.input-group input[type="text"],
.input-group input[type="date"]{
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #eef2f7;
  flex: 1;
}

/* Buttons */
.btn {
  background-color: #28a745;
  color: white;
  padding: 8px 15px;
  border: none;
  cursor: pointer;
  border-radius: 4px;
  margin-top: 5px;
}

.btn:hover {
  background-color: #218838;
}

/* Payroll Actions */
.actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.btn-group {
  display: flex;
  gap: 10px;
}

/* Search Bar */
.search-bar {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #eef2f7;
}

/* Table Container */
.table-container {
  background: white;
  padding: 15px;
  border-radius: 5px;
  
}

/* Table Styles */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f4f4f4;
}

/* Special Genrate Salary Button */
.btn.genrate {
  background-color: #f44336;
}

.btn.genrate:hover {
  background-color: #d32f2f;
}

  </style>
</head>
<body>
  <?php 
    include '../nav.php';
  ?>
  <section class="genrate_payslip">
    <div class="container">
      <!-- Input Card -->
      <div class="card">
      <h2>Monthly Payroll List</h2>
        <hr>
        <div class="input-group">
          <label>Designation</label>
          <input type="text" placeholder="Designation">
          <label>Month</label>
          <input type="date" placeholder="Month">
          <button class="btn">Submit</button>
        </div>
      </div>

      <!-- Payroll List Card -->
      <div class="card">
      
  
        <div class="actions">
          <div class="btn-group">
            <button class="btn">Copy</button>
            <button class="btn">CSV</button>
            <button class="btn">Excel</button>
            <button class="btn">PDF</button>
            <button class="btn">Print</button>
          </div>
          <div>
            <input type="text" placeholder="Search" class="search-bar">
          </div>
        </div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Employee</th>
                <th>PIN</th>
                <th>Salary</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Thom Anderson</td>
                <td>1</td>
                <td>10000</td>
                <td>
                <a href="genrate_salary.php"><button class="btn genrate">Generate Salary</button></a>
                </td>
              </tr>
              <tr>
                <td>Thom Anderson</td>
                <td>1</td>
                <td>10000</td>
                <td>
                  <a href="genrate_salary.php"><button class="btn genrate">Generate Salary</button></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
