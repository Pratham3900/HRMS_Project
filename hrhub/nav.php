<?php
// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {  
    header("location: hr_login.php");  
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navigation bar</title>

    <!-- for icon  -->
    <link rel="stylesheet" href=../css/all.min.css />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/nav.css">
</head>

<body>
    <!-- nav -->
    <nav>
        <!-- Left group: menu icon and logo -->
        <div class="left-group">
            <i class="fa-solid fa-bars menu-icon"></i>
            <span class="logo-name">HRHub</span>
        </div>

        <!-- Right group: user icon (or other right icon) -->
        <div class="right-group">
            <i class="fa-solid fa-user menu-icon-2"></i>
        </div>

        <div class="sidebar">
            <p class="name-of-user"><?php echo $_SESSION['email'] ?></p>
            <div class="sidebar-content">
                <ul class="lists">

                    <li class="list">
                        <a href="/HR PROJECT SEM 6/hrhub/dashboard.php" class="nav-link">
                            <i class="fa-solid fa-house icon"></i>
                            <span class="link"> Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class="fa-regular fa-building icon"></i>
                            <span class="link"> Organaztion</span>
                            <i class='bx bx-chevron-right icon'></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/organazation/department.php">Department</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/organazation/designation.php">Designation</a></li>

                        </ul>
                    </li>

                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class='bx bx-group icon'></i>
                            <span class="link">Employees</span>
                            <i class='bx bx-chevron-right icon'></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/employee/employee.php">Employees</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/employee/add_employee.php">Add Employee</a></li>
                            
                        </ul>
                    </li>

                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-clipboard-list icon"></i>
                            <span class="link"> Attendance</span>
                            <i class='bx bx-chevron-right icon'></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/attendence/attendence.php">Attendance List</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/attendence/one_attendence.php">Add one Attendance</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/attendence/bulk_attendance.php">Add bulk Attendance</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/attendence/attendence_report.php">Attendance Report</a></li>
                        </ul>
                    </li>

                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-user-large-slash icon"></i>
                            <span class="link"> Leave</span>
                            <i class='bx bx-chevron-right icon'></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/leave/holiday.php">Holiday</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/leave/leave_type.php">Leave Type</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/leave/leave.php">Leave Application</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/leave/leave_report.php">Report</a></li>
                        </ul>
                    </li>

                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class='bx bx-list-plus icon'></i>
                            <span class="link">Projects</span>
                            <i class='bx bx-chevron-right icon'></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/project/project.php">Projects</a></li>
                            <li><a href="/HR PROJECT SEM 6/hrhub/project/add_project.php">Add Projects</a></li>
                           
                        </ul>
                    </li>

                    <li class="list">
                        <a href="#" class="nav-link">
                            <i class='bx bxs-bank icon'></i>
                            <span class="link">Payroll</span>
                            <i class='bx bx-chevron-right icon'></i>

                        </a>
                        <ul class="submenu">
                            <li><a href="/HR PROJECT SEM 6/hrhub/payroll/payroll.php">Payroll List</a></li>
                           <li><a href="/HR PROJECT SEM 6/hrhub/payroll/payslip_report.php">Payslip Report</a></li>
                           <li><a href="/HR PROJECT SEM 6/hrhub/payroll/payroll_request.php">Payslip Request</a></li>
                        
                           
                        </ul>
                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/hrhub/notice/notice.php" class="nav-link">
                            <i class="fa-solid fa-clipboard"></i>
                            <span class="link">Notice</span>
                        </a>
                    </li>
                </ul>
                <div class="bottom-content">
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/hrhub/change_password.php" class="nav-link">
                        <i class="fa-solid fa-key"></i>
                            <span class="link">Change Password</span>

                        </a>
                    </li>
                    <li class="list">
                        <a href="/HR PROJECT SEM 6/hrhub/logout.php" class="nav-link">
                            <i class='bx bx-log-out icon'></i>
                            <span class="link"> Logout</span>
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </nav>
    <!-- <section class="dashboard">
    
</section> -->


</body>

<script>
const navbar = document.querySelector("nav");
const menubtn = document.querySelector(".menu-icon"); // Select the menu icon

// Toggle sidebar when menu icon is clicked
menubtn.onclick = () => {
    navbar.classList.toggle("open"); // Open and close the sidebar by toggling the "open" class
};

// Fix: Handle links for navigation properly
document.querySelectorAll('.list .nav-link').forEach(link => {
    link.addEventListener('click', event => {
        const submenu = link.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
            event.preventDefault(); // Prevent navigation if submenu exists
            const isActive = submenu.classList.contains('active');

            // Close all other submenus
            document.querySelectorAll('.submenu.active').forEach(openMenu => {
                openMenu.classList.remove('active');
                const chevron = openMenu.previousElementSibling.querySelector(
                    '.bx-chevron-right');
                if (chevron) chevron.classList.remove('rotate');
            });

            // Toggle current submenu
            if (!isActive) {
                submenu.classList.add('active');
                const chevron = link.querySelector('.bx-chevron-right');
                if (chevron) chevron.classList.add('rotate');
            }
        }
    });
});
</script>

</html>