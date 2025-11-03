<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Hub - HR Management System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Employee Hub</h2>
            <ul>
                <li><a href="#dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#profile"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="#attendance"><i class="fas fa-calendar-check"></i> Attendance</a></li>
                <li><a href="#leave"><i class="fas fa-plane"></i> Leave Management</a></li>
                <li><a href="#payroll"><i class="fas fa-money-bill"></i> Payroll</a></li>
                <li><a href="#projects"><i class="fas fa-tasks"></i> Projects</a></li>
                <li><a href="#notices"><i class="fas fa-bell"></i> Notices</a></li>
                <li><a href="#support"><i class="fas fa-life-ring"></i> Support</a></li>
                <li><a href="#logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header>
                <h1>Welcome to Employee Hub</h1>
            </header>
            <section id="dashboard" class="section">
                <h2>Dashboard</h2>
                <p>Overview of personal details, attendance, projects, and latest company notices.</p>
            </section>
            <section id="profile" class="section">
                <h2>Profile & Personal Information</h2>
                <p>Employee ID, Name, Photo, Designation, Contact Details, etc.</p>
            </section>
            <section id="attendance" class="section">
                <h2>Attendance Management</h2>
                <p>Daily attendance status, check-in/check-out time, and monthly reports.</p>
            </section>
            <section id="leave" class="section">
                <h2>Leave Management</h2>
                <p>Apply for leave, view leave balance, and holiday calendar.</p>
            </section>
            <section id="payroll" class="section">
                <h2>Payroll & Salary</h2>
                <p>Monthly payslip, salary breakdown, and tax information.</p>
            </section>
            <section id="projects" class="section">
                <h2>Projects & Task Management</h2>
                <p>Assigned projects, task status, and performance feedback.</p>
            </section>
            <section id="notices" class="section">
                <h2>Company Notices & Announcements</h2>
                <p>Latest HR announcements, policy updates, and upcoming events.</p>
            </section>
            <section id="support" class="section">
                <h2>Employee Requests & Support</h2>
                <p>Request for documents, HR support, and grievance reporting.</p>
            </section>
        </main>
    </div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    .container {
        display: flex;
    }
    .sidebar {
        width: 250px;
        background: #2c3e50;
        color: #ecf0f1;
        height: 100vh;
        padding: 20px;
        position: fixed;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .sidebar ul {
        list-style: none;
    }
    .sidebar ul li {
        padding: 15px;
    }
    .sidebar ul li a {
        color: #ecf0f1;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    .sidebar ul li a i {
        margin-right: 10px;
    }
    .sidebar ul li a:hover {
        background: #34495e;
        border-radius: 5px;
        padding: 10px;
    }
    .logout {
        color: #e74c3c !important;
    }
    .content {
        margin-left: 270px;
        padding: 20px;
        width: 100%;
    }
    header {
        background: #ecf0f1;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .section {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
</style>
