<?php
session_start();
if (!isset($_SESSION['employee_loggedin']) || $_SESSION['employee_loggedin'] != true) {
    header("location: employee_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Notices & Announcements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            text-align: left;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .notice {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .notice h3 {
            margin: 0;
            color: #007bff;
        }
        .notice p {
            margin: 5px 0;
            color: #555;
        }
        .date {
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<section class="dashboard">
    <div class="container">
        <h2>Company Notices & Announcements</h2>
        <?php
                include 'connection.php';
                    $sql = "select * from notice ORDER BY date desc";
                    $result = mysqli_query($con, $sql);
                    
                    while ($row = mysqli_fetch_assoc($result)) {

                        echo " <div class='notice'> 

                         <h3>$row[title]</h3>
                        <p>$row[description]</p>
                         <p class='date'>Published on:$row[date] </p>
                       
                      
                       </div> ";
                    }
                    ?>
        
    </div>
    </section>
</body>
</html>
