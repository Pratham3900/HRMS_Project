<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header("location: ../hr_login.php");
    exit();
}

include '../connection.php';

// Add Designation
if (isset($_POST['save'])) {
    $designation_name = strtoupper($_POST['designation_name']);
    $department_id = $_POST['department_id']; 
    $base_salary = $_POST['basic_salary']; // Get base salary input

    // Check if the designation already exists in the department
    $check_sql = "SELECT * FROM designation WHERE designation_name = '$designation_name' AND department_id = '$department_id'";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = "<div class='alert error'>⚠ This designation already exists in the selected department!</div>";
    } else {
        // Insert new designation with base salary
        $sql = "INSERT INTO designation (designation_name, department_id, base_salary) VALUES ('$designation_name', '$department_id', '$base_salary')";
        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "<div class='alert success'>✔ Designation added successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>❌ Error adding designation!</div>";
        }
    }

    header("Location: designation.php");
    exit();
}

// Edit Designation
if (isset($_POST['edit'])) {
    $designation_id = $_POST['designation_id'];
    $designation_name = strtoupper($_POST['designation_name']);
    $department_id = $_POST['department_id']; 
    $base_salary = $_POST['basic_salary']; // Get base salary input

    // Check if the updated designation already exists in the same department
    $check_sql = "SELECT * FROM designation WHERE designation_name = '$designation_name' AND department_id = '$department_id' AND designation_id != '$designation_id'";
    $check_result = mysqli_query($con, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = "<div class='alert error'>⚠ This designation name already exists in this department!</div>";
    } else {
        // Update the designation with base salary
        $sql = "UPDATE designation SET designation_name = '$designation_name', department_id = '$department_id', base_salary = '$base_salary' WHERE designation_id = $designation_id";
        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "<div class='alert success'>✔ Designation updated successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>❌ Error updating designation!</div>";
        }
    }

    header("Location: designation.php");
    exit();
}

// Delete Designation
if (isset($_GET['delete'])) {
    $designation_id = $_GET['delete'];

    // Check if the designation is assigned to employees
    $check_sql = "SELECT COUNT(*) AS total FROM employee WHERE designation_id = '$designation_id'";
    $check_result = mysqli_query($con, $check_sql);
    $row = mysqli_fetch_assoc($check_result);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "<div class='alert error'>⚠ Cannot delete! This designation is assigned to employees.</div>";
    } else {
        $sql = "DELETE FROM designation WHERE designation_id = $designation_id";
        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "<div class='alert success'>✔ Designation deleted successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>❌ Error deleting designation!</div>";
        }
    }

    header("Location: designation.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designation Management</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    .container {
        padding: 20px;

    }

    header {
        background-color: white;
        color: #6a1b9a;
        padding: 1rem;
        font-size: 1.5rem;
        margin-top: -20px;
        margin-left: -20px;
        margin-right: -20px;
    }

    .content {
        display: flex;
        gap: 20px;
    }

    .add-designation,
    .designation-list {
        background: white;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        flex: 1;
    }

    h2 {
        margin-top: 0;
    }

    hr {
        border: 2px solid #5f3a99;
        margin: 20px -20px;
    }

    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 20px;
    }

    form input,
    form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
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

    .designation-list table {
        width: 100%;
        border-collapse: collapse;
    }

    .designation-list th,
    .designation-list td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }

    /* Dashboard Styles */
    .designation {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
    }

    nav.open~.designation {
        margin-left: 200px;
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

    <section class="designation">
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
            Designation
        </header>
        <div class="container">
            <div class="content">
                <!-- Add Designation Form -->
                <div class="add-designation">
                    <h2>Add/Edit Designation</h2>
                    <hr>
                    <form method="POST">
                        <input type="hidden" name="designation_id" id="designation_id" value="" />
                        <label for="designationName">Designation Name</label>
                        <input type="text" id="designationName" name="designation_name"
                            placeholder="Enter Designation Name" required />
                            <label for="designationName">Basic Salary</label>
                        <input type="text" id="basicsalary" name="basic_salary"
                            placeholder="Enter basic Name" required />
                        <label for="department_id">Select Department:</label>
                        <select id="department_id" name="department_id" required>
                            <?php
                                // Fetch all departments
                                $sql = "SELECT * FROM department";
                                $result = mysqli_query($con, $sql);

                                
                                while ($num = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $num['department_id'] . "'>" . $num['department_name'] . "</option>";
                                }
                            ?>
                        </select>
                        <div class="buttons">
                            <button class="btn save" name="save" id="saveBtn">Save</button>
                            <button class="btn save" name="edit" id="editBtn" style="display:none;">Update</button>
                            <button class="btn cancel" type="button" onclick="clearForm()">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Designation List -->
                <div class="designation-list">
                    <h2>Designation List</h2>
                    <hr>
                    <table>
                        <thead>
                            <tr>
                                <th>Designation id</th>
                                <th>Designation Name</th>
                                <th>Basic Salary</th>
                                <th>Department Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Fetch designation data
                                $sql = "SELECT designation.designation_id, designation.designation_name, designation.base_salary, department.department_name
                                        FROM designation
                                        JOIN department ON designation.department_id = department.department_id";
                                $result = mysqli_query($con, $sql);

                                while ($num = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                    <td>$num[designation_id]</td>
                                    <td>$num[designation_name]</td>
                                    <td>$num[base_salary]</td>
                                    <td>$num[department_name]</td>
                                    <td>
                                        <button class='btn save' onclick='editDesignation(" . $num['designation_id'] . ", \"" . $num['designation_name'] . "\", \"" . $num['base_salary'] . "\", \"" . $num['department_name'] . "\")'>Edit</button>
                                        <a href='?delete=" . $num['designation_id'] . "' class='btn cancel'>Delete</a>
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
    // Edit designation
    function editDesignation(designation_id, designationName, baseSalary, departmentName) {
    document.getElementById('designation_id').value = designation_id;
    document.getElementById('designationName').value = designationName;
    document.getElementById('basicsalary').value = baseSalary; // Set base salary

    var departmentSelect = document.getElementById('department_id');
    for (var i = 0; i < departmentSelect.options.length; i++) {
        if (departmentSelect.options[i].text == departmentName) {
            departmentSelect.selectedIndex = i;
            break;
        }
    }

    // Show "Update" button, hide "Save" button
    document.getElementById('saveBtn').style.display = 'none';
    document.getElementById('editBtn').style.display = 'inline-block';
}


    // Clear form function
    function clearForm() {
        document.getElementById('designation_id').value = '';
        document.getElementById('designationName').value = '';
        // Reset department select
        document.getElementById('department_id').selectedIndex = 0;

        // Show "Save" button, hide "Update" button
        document.getElementById('saveBtn').style.display = 'inline-block';
        document.getElementById('editBtn').style.display = 'none';
    }
    </script>

</body>

</html>