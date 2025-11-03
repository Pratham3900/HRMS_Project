<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';


if (isset($_POST['save'])) {
    $department_name = trim($_POST['department_name']); // Remove extra spaces
    $department_name= strtoupper($_POST['department_name']); // Convert input to uppercase
    // Check if the department already exists
    $check_sql = "SELECT * FROM department WHERE department_name = '$department_name'";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // If department already exists, store error message in session
        $_SESSION['message'] = "<div class='alert error'>⚠ Department already exists!</div>";
    } else {
        // Insert department if it does not exist
        $sql = "INSERT INTO department (department_name) VALUES ('$department_name')";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $_SESSION['message'] = "<div class='alert success'>✔ Department added successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>❌ Error adding department!</div>";
        }
    }

    header("Location: department.php"); // Redirect to avoid form resubmission
    exit();
}



// Edit department


if (isset($_POST['edit'])) {
  $department_id = $_POST['department_id'];
  $department_name = trim($_POST['department_name']); // Remove extra spaces
  $department_name= strtoupper($_POST['department_name']); // Convert input to uppercase
  // Check if the department already exists
  $check_sql = "SELECT * FROM department WHERE department_name = '$department_name'";
  $check_result = mysqli_query($con, $check_sql);

  if (mysqli_num_rows($check_result) > 0) {
      // If department already exists, store error message in session
      $_SESSION['message'] = "<div class='alert error'>⚠ Department already exists!</div>";
  } else {
      // Insert department if it does not exist
      $sql = "UPDATE department SET department_name = '$department_name' WHERE department_id = $department_id";
      $result = mysqli_query($con, $sql);

      if ($result) {
          $_SESSION['message'] = "<div class='alert success'>✔ Department update successfully!</div>";
      } else {
          $_SESSION['message'] = "<div class='alert error'>❌ Error adding department!</div>";
      }
  }

  header("Location: department.php"); // Redirect to avoid form resubmission
  exit();
}

// Delete department
if (isset($_GET['delete'])) {
  $department_id = $_GET['delete'];

  // Check if the department is used in child tables
  $check_child_sql = "
      SELECT ( 
          (SELECT COUNT(*) FROM employee WHERE department_id = $department_id) + 
          (SELECT COUNT(*) FROM designation WHERE department_id = $department_id) 
      ) AS total_references";
  
  $check_result = mysqli_query($con, $check_child_sql);
  $row = mysqli_fetch_assoc($check_result);

  if ($row['total_references'] > 0) {
      // If department is referenced in child tables, prevent deletion
      $_SESSION['message'] = "<div class='alert error'>⚠ Cannot delete! Department is assigned to employees or designation.</div>";
  } else {
      // Proceed with deletion if not used in child tables
      $sql = "DELETE FROM department WHERE department_id = $department_id";
      $result = mysqli_query($con, $sql);

      if ($result) {
          $_SESSION['message'] = "<div class='alert success'>✔ Department deleted successfully!</div>";
      } else {
          $_SESSION['message'] = "<div class='alert error'>❌ Error deleting department!</div>";
      }
  }

  // Redirect to avoid form resubmission
  header("Location: department.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <style>
    body {
              font-family: Arial, sans-serif;
              background-color: #f4f6f8;
          }

  .container {
    /* border:3px solid red; */
    padding: 20px;
  }

  header {
              background-color: white;
              color:#6a1b9a ;
              padding: 1rem;
              font-size: 1.5rem;
              margin-top:-20px;
              margin-left: -20px;
              margin-right: -20px;
          }

  .content {
    display: flex;
    gap: 20px;
    /* border:3px solid red; */
  }

  .add-department,
  .department-list {
    background: white;
  
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    flex: 1;
    
  }

  h2 {
    margin-top: 0;
    /* border:3px solid red; */
  }
  hr{
  
  border:2px solid #5f3a99;
  margin-right:-20px;
  margin-left:-20px;
  margin-top:20px;
  margin-bottom:20px;
  }

  form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    font-size:20px;
    /* border:3px solid red; */
  }

  form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;


    /* border:3px solid red; */
  }

  .buttons {
    display: flex;
    gap: 10px;
  }

  .btn {
    padding: 10px 20px;
    border: none;

    font-size: 16px;
    cursor: pointer;
  }

  .btn.save {
    background-color: #28a745;
    color: white;
  }

  .btn.cancel {
    background-color: #dc3545;
    color: white;
  }

  .department-list table {
    width: 100%;
    border-collapse: collapse;
  }

  .department-list th,
  .department-list td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ccc;
  }


          /* Dashboard Styles */
          .department {
      margin-top: 60px; /* To stay below navbar */
      margin-left: 0; /* Default margin when sidebar is closed */
      padding: 20px;
      transition: margin-left 0.4s ease;
  }

  nav.open ~ .department {
      margin-left: 200px; /* Adjust to sidebar width */
  }

  .alert {
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        display: inline-block;
        width: 100%;
        max-width: 400px;
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        top: 20px;
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        transition: opacity 0.5s ease-out, top 0.5s ease-out;
    }
    .success { background: #4CAF50; color: white; }
    .error { background: #f44336; color: white; }
  </style>
  <script>
    // Automatically hide the message after 5 seconds with fade-out effect
    setTimeout(function() {
        var msgBox = document.getElementById('message-box');
        if (msgBox) {
            msgBox.style.opacity = '0';
            msgBox.style.top = '-50px'; // Moves the message up
            setTimeout(function() { msgBox.style.display = 'none'; }, 300);
        }
    }, 3000);
</script>
</head>

<body>
<?php include '../nav.php'; ?>
<section class="department">
  <!-- Display the message -->
<?php if (isset($_SESSION['message'])): ?>
    <div id="message-box">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']); // Remove message after displaying
        ?>
    </div>
<?php endif; ?> 
    <header class="header">
        Department
    </header>
    <div class="container">
      
        <div class="content">
          
            <div class="add-department">
                <h2>Add/Edit Department</h2><hr>
                <form method="POST">
                    <input type="hidden" name="department_id" id="department_id" value="" />
                    <label for="departmentName">Department Name</label>
                    <input type="text" id="departmentName" name="department_name" placeholder="Enter Department Name" required />
                    <div class="buttons">
                        <button class="btn save" name="save" <?php echo isset($editMode) ? 'style="display:none"' : ''; ?>>Save</button>
                        <button class="btn save" name="edit" <?php echo !isset($editMode) ? 'style="display:none"' : ''; ?>>Update</button>
                        <button class="btn cancel" type="button" onclick="clearForm()">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="department-list">
                <h2>Department List</h2><hr>
                <table>
                    <thead>
                        <tr> 
                        <th>Department_id</th>
                            <th>Department Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Query to fetch department data
                    $sql = "SELECT * FROM department";
                    $result = mysqli_query($con, $sql);

                    while ($num = mysqli_fetch_assoc($result)) {
                      echo "<tr>
                       <td>$num[department_id]</td>
                              <td>$num[department_name]</td>
                              <td>
                                  <button class='btn save' onclick='editDepartment(" . $num['department_id'] . ", \"" . $num['department_name'] . "\")'>Edit</button>
                                  <a href='?delete=" . $num['department_id'] . "' class='btn cancel'>Delete</a>
                              </td>
                            </tr>";
                  }
                  
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    // Edit department function
    function editDepartment(department_id, departmentName) {
        document.getElementById('department_id').value = department_id;
        document.getElementById('departmentName').value = departmentName;
        // Show "Update" button, hide "Save" button
        document.querySelector('button[name="save"]').style.display = 'none';
        document.querySelector('button[name="edit"]').style.display = 'inline-block';
    }

    // Clear form function
    function clearForm() {
        document.getElementById('department_id').value = '';
        document.getElementById('departmentName').value = '';
        // Show "Save" button, hide "Update" button
        document.querySelector('button[name="save"]').style.display = 'inline-block';
        document.querySelector('button[name="edit"]').style.display = 'none';
    }
</script>

</body>
</html>

