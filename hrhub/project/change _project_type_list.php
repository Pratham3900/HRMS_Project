<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>change_project_status_list</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
    }

    /* Employee Styles */
    .change_project_status {
        margin-top: 60px;
        margin-left: 0;
        padding: 20px;
        transition: margin-left 0.4s ease;
        /* border:3px solid red; */
    }

    nav.open~.change_project_status {
        margin-left: 200px;
    }

    /* Header */
    header {
            background-color: white;
            color:#6a1b9a ;
            padding: 1rem;
            font-size: 1.5rem;
            margin-top:-20px;
            margin-left: -20px;
            margin-right: -20px;
            margin-bottom: 15px;
        }

    .sub-header {

        margin-left: 40px;
    }

    /* Container */
    .container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 95%;
        margin: auto;
        margin-top: 20px;
        /* border:3px solid red; */
    }

    hr {

        border: 2px solid #5f3a99;
        margin-right: -20px;
        margin-left: -20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    /* Buttons */
    .btn {
        background-color: #3333ff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease-in-out;
    }

    .btn:hover {
        background-color: #3385ff;
        transform: scale(1.05);
    }

    /* Employee List */
    .change_project_status_list h2 {

        font-size: 24px;
        margin-bottom: 15px;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;

        margin-bottom: 30px;
    }

    .search-bar {
        padding: 8px;
        border: 2px solid #3333ff;
        border-radius: 5px;
        outline: none;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    thead {
        background-color: #3333ff;
        color: white;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }



    /* Edit and Delete Buttons */
    .btn.edit {
        background-color: #4caf50;
    }

    .btn.edit:hover {
        background-color: #388e3c;
    }

    .btn.delete {
        background-color: #f44336;
    }

    .btn.delete:hover {
        background-color: #d32f2f;
    }
    </style>
</head>

<body>
    <?php 
    include '../nav.php';
    ?>
    <section class="change_project_status">
        <header class="header">

            Project Status type
        </header>
        <div class="sub-header">
            <a href="change_project_status.php"><button class="btn add-employee">Change Project Status Type</button></a>

        </div>
        <div class="container">

            <div class="change_project_status_list">
                <h2>change project status List</h2>
                <hr>
                <div class="actions">
                    <div>
                        <button class="btn">Copy</button>
                        <button class="btn">CSV</button>
                        <button class="btn">Excel</button>
                        <button class="btn">PDF</button>
                        <button class="btn">Print</button>
                    </div>

                    <div>
                        <input type="text" placeholder="Search" class="search-bar"/>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>change project status Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Example rows, you can add more as needed -->
                        <tr>

                            <td>1</td>
                            <td>running</td>
                            <td><button class="btn edit">Edit</button>
                                <button class="btn delete">delete</button>
                            </td>

                        </tr>
                        <tr>
                            <td>1</td>
                            <td>running</td>
                            <td><button class="btn edit">Edit</button>
                                <button class="btn delete">delete</button>
                            </td>

                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</body>

</html>