<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("Location: employee_login.php");
    exit();
}
include 'connection.php'; 
$designation_id = isset($_SESSION['designation_id']) ? $_SESSION['designation_id'] : null;

if ($designation_id !== null) {
    $des_query = "SELECT designation_name FROM designation WHERE designation_id = '$designation_id'";
    $des_result = mysqli_query($con, $des_query);

    if ($des_result && mysqli_num_rows($des_result) > 0) {
        $des_row = mysqli_fetch_assoc($des_result);
        $designation_name = $des_row['designation_name'];
    } else {
        $designation_name = "Not Assigned"; // Default value if not found
    }
} else {
    $designation_name = "Not Assigned"; // Default value if session variable is missing
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_account'])) {
    $employee_id = $_SESSION['employee_id'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $marital_status = $_POST['marital_status'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    // Validation
    if (empty($fullname) || empty($phone) || empty($dob) || empty($nationality) || empty($address) || empty($gender)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        // Update query
        $update_query = "UPDATE employee SET 
                            employee_name = '$fullname', 
                            contact = '$phone', 
                            dob = '$dob', 
                            nationality = '$nationality', 
                            marital_status = '$marital_status', 
                            address = '$address', 
                            gender = '$gender' 
                        WHERE employee_id = '$employee_id'";

        if (mysqli_query($con, $update_query)) {
            header("location: dashboard.php");
            exit; // Always add exit after header redirection
    }
}
}


$employee_id = $_SESSION['employee_id'];

// Fetch employee profile picture
$query = "SELECT profile_pic FROM employee WHERE employee_id = '$employee_id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

// If no profile picture, use a default one
$profile_pic = !empty($row['profile_pic']) ? 'uploads/' . $row['profile_pic'] : 'user.jpg';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    
    if (in_array($imageFileType, $allowed_types)) {
        // Move file to uploads folder
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Update the database
            $update_query = "UPDATE employee SET profile_pic = '$file_name' WHERE employee_id = '$employee_id'";
            mysqli_query($con, $update_query);

            // Refresh the page to show updated profile picture
            header("Location: profile.php");
            exit();
        } 
    } 
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .profile-pic-container {
        position: relative;
        display: inline-block;
    }

    .edit-icon {
        position: absolute;
        top: 90px;
        right: 7px;
        background: white;
        border-radius: 50%;
        padding: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }

    nav {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        background: white;
        /* Ensure visibility */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        /* Optional: Adds shadow */
    }

    .container {
        margin-top: 80px;
        /* Adjust based on navbar height */
    }
    </style>
</head>

<body>
    <?php 
    include 'nav.php';
    ?>
    <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4 ">Account settings</h4>
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0 text-center p-3">
                    <div class="profile-pic-container">
                        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="rounded-circle" width="120"
                            height="120">
                        <button class="btn btn-sm btn-primary edit-icon"
                            onclick="document.getElementById('profile-pic-input').click();">✏</button>
                        <form method="POST" enctype="multipart/form-data" style="display:inline;">
                            <input type="file" name="profile_pic" id="profile-pic-input" style="display: none;"
                                onchange="this.form.submit()">
                        </form>
                    </div>

                    <h5 class="mt-4"><?php echo $_SESSION['employee_name'] ?></h5>
                    <p class="text-muted"><?php echo $designation_name ?></p>
                    <div class="list-group list-group-flush account-settings-links mt-3">
                        <a class="list-group-item list-group-item-action active" data-toggle="list"
                            href="#account-general">Account Information</a>
                        <a class="list-group-item list-group-item-action" href="change_password.php">Change Password</a>


                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="account-general">
                            <hr class="border-light m-0">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label class="form-label">Employee ID</label>
                                        <input type="text" class="form-control mb-1" readonly
                                            value="<?php echo $_SESSION['employee_id'] ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">E-mail</label>
                                        <input type="text" class="form-control mb-1"
                                            value="<?php echo $_SESSION['employee_email'] ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Fullname</label>
                                        <input type="text" class="form-control mb-1" name="fullname"
                                            value="<?php echo $_SESSION['employee_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control mb-1" name="phone"
                                            value="<?php echo $_SESSION['contact']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Date Of Birth</label>
                                        <input type="date" class="form-control mb-1" name="dob"
                                            value="<?php echo $_SESSION['dob']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Nationality</label>
                                        <input type="text" class="form-control mb-1" name="nationality"
                                            value="<?php echo $_SESSION['nationality']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Marital Status</label>
                                        <select class="form-control mb-1" name="marital_status">
                                            <option value="Single"
                                                <?php echo ($_SESSION['marital_status'] == 'Single') ? 'selected' : ''; ?>>
                                                Single</option>
                                            <option value="Married"
                                                <?php echo ($_SESSION['marital_status'] == 'Married') ? 'selected' : ''; ?>>
                                                Married</option>
                                            <option value="Divorced"
                                                <?php echo ($_SESSION['marital_status'] == 'Divorced') ? 'selected' : ''; ?>>
                                                Divorced</option>
                                            <option value="Widowed"
                                                <?php echo ($_SESSION['marital_status'] == 'Widowed') ? 'selected' : ''; ?>>
                                                Widowed</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control mb-1"
                                            name="address"><?php echo $_SESSION['address']; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Gender</label><br>
                                        <input type="radio" id="male" name="gender" value="male"
                                            <?php echo ($_SESSION['gender'] == 'male') ? 'checked' : ''; ?>>
                                        <label for="male">Male</label>
                                        <input type="radio" id="female" name="gender" value="female"
                                            <?php echo ($_SESSION['gender'] == 'female') ? 'checked' : ''; ?>>
                                        <label for="female">Female</label>
                                        <input type="radio" id="other" name="gender" value="other"
                                            <?php echo ($_SESSION['gender'] == 'other') ? 'checked' : ''; ?>>
                                        <label for="other">Other</label>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" name="update_account" class="btn btn-primary">Save
                                            changes</button>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>