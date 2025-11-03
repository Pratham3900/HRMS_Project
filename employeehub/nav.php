<?php
include 'connection.php'; 
$employee_id = $_SESSION['employee_id'];

// Fetch employee profile picture
$query = "SELECT profile_pic FROM employee WHERE employee_id = '$employee_id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

// If no profile picture, use a default one
$profile_pic = !empty($row['profile_pic']) ? 'uploads/' . $row['profile_pic'] : 'user.jpg';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navigation bar</title>

    <!-- for icon  -->
    <link rel="stylesheet" href=css/all.min.css />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/nav1.css">
   <style>
    img.rounded-circle {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease-in-out;
}

   </style>

</head>

<body>
    <!-- nav -->
    <nav>
        <!-- Left group: menu icon and logo -->
        <div class="left-group">
            <i class="fa-solid fa-bars menu-icon"></i>
            <span class="logo-name">EmployeHub</span>
        </div>

        <!-- Right group: user icon (or other right icon) -->
        <div class="right-group">
            <i class="fa-solid fa-user menu-icon-2"></i>
        </div>

        <div class="sidebar">
            <div class="sidebar-content">
                <!-- Profile/Header Section -->
                <div class="sidebar-header">
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="rounded-circle" width="120"
                height="120">
                    <h3 class="user-name"><?php echo $_SESSION['employee_name'] ?></h3>
                </div>
                
                <ul class="lists">
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/profile.php" class="nav-link">
                            <i class="fa-solid fa-user icon"></i>
                            <span class="link"> Profile</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/dashboard.php" class="nav-link">
                            <i class="fa-solid fa-house icon"></i>
                            <span class="link"> Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/attendence.php" class="nav-link">
                            <i class="fa-solid fa-clipboard-list icon"></i>
                            <span class="link"> Attendance</span>

                        </a>

                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/leave.php" class="nav-link">
                            <i class="fa-solid fa-user-large-slash icon"></i>
                            <span class="link"> Leave</span>

                        </a>

                    </li>
                    <!-- <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/project.php" class="nav-link">
                            <i class='bx bx-list-plus icon'></i>
                            <span class="link">Projects</span>

                        </a>

                    </li> -->
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/payroll.php" class="nav-link">
                            <i class='bx bxs-bank icon'></i>
                            <span class="link">Payroll</span>

                        </a>

                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/notice.php" class="nav-link">
                            <i class="fa-solid fa-clipboard icon"></i>
                            <span class="link">Notice</span>
                        </a>
                    </li>

                    <!-- <li class="list">
                        <a href="/HR PROJECT SEM 6/employeehub/change_password.php" class="nav-link">
                        <i class="fa-solid fa-key icon"></i>
                            <span class="link">Edit Password</span>
                        </a>
                    </li> -->
                </ul>

                <div class="bottom-content">
                    <ul class="lists">
                        <li class="list">
                            <a href="logout.php" class="nav-link">
                                <i class='bx bx-log-out'></i>
                                <span class="link"> Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>


    </nav>
    <!-- <section class="dashboard">
        dchkjscb
    </section> -->


</body>

<script>
const navbar = document.querySelector("nav");
const menubtn = document.querySelector(".menu-icon");

menubtn.onclick = () => {
    navbar.classList.toggle("open");
};
</script>

</html>