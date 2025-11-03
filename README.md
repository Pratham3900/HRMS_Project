# 🧠 Human Resource Management System (HRMS)

A full-featured HR Management System built with **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**.  
This system helps automate HR processes such as employee management, attendance tracking, payroll, and leave management.

---

## 🚀 Features

✅ Employee Registration & Login  
✅ HR Dashboard (Admin Panel)  
✅ Attendance Management  
✅ Payroll Management with Razorpay Integration  
✅ Leave Application & Approval System  
✅ Real-Time Data Management  
✅ Responsive and Premium UI Design  

---

## 🗃️ Tech Stack

| Component | Technology |
|------------|-------------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP (WAMP Server) |
| Database | MySQL |
| Payment Gateway | Razorpay |
| Authentication | Session-based |

---

## ⚙️ Installation Steps

1. **Clone this repository**
   ```bash
   git clone https://github.com/Pratham3900/HRMS_Project.git
   
2. Move the folder to your WAMP www directory:

C:\wamp64\www\HRMS_Project

3. Start WAMP Server and ensure both Apache and MySQL are running.

4. Create the database:

Open http://localhost/phpmyadmin

Create a new database named hrms_db

Import the SQL file:

database/hrms_db.sql


5. Update connection details in config.php (if required):

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hrms_db";


Run the project:

6. http://localhost/HRMS_Project/

📁 Folder Structure
HRMS_Project/
│
├── hrhub/               # HR/Admin Panel
├── emphub/              # Employee Panel
├── attendancehub/       # Attendance Module
├── database/            # MySQL dump (.sql file)
├── assets/              # CSS, JS, Images
├── index.php            # Main entry point
└── README.md            # Project Documentation

Developed By

Pratham Danawala
📧 Email: prathamdanawala@gmail.com
🎓 MCA Student | HRMS Project Developer
