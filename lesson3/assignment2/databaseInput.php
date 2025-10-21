<?php
    ob_start();
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database</title>

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 17px;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 40px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 35px 40px;
            width: 100%;
            max-width: 520px;
            margin: 40px auto;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            animation: fadeIn 0.4s ease-in-out;
            padding-right: 63px;
        }

        label {
            font-size: 15px;
            color: #555;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            margin-top: 14px;
        }

        input[type="text"] {
            display: block;
            width: 100%;
            padding: 11px 13px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 2px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 6px rgba(74,144,226,0.3);
            outline: none;
        }

        input[type="text"]::placeholder {
            text-align: left;
            color: #888;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 18px;
            transition: background 0.3s ease, transform 0.2s ease;
            width: 100%;
            margin-left: 13px;
        }

        input[type="submit"]:hover {
            background-color: #357ABD;
            transform: translateY(-1px);
        }

        input[name="reset"] {
            background-color: #ddd;
            color: #333;
        }

        input[name="reset"]:hover {
            background-color: #ccc;
        }

        
        table {
            margin: 40px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            animation: fadeIn 0.5s ease-in-out;
        }

        th {
            background-color: #4a90e2;
            color: #fff;
            padding: 14px 10px;
            font-size: 16px;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 12px 10px;
            font-size: 15px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9fafc;
        }

        tr:hover {
            background-color: #eef4ff;
        }

        p {
            text-align: center;
            margin-top: 20px;
            font-size: 17px;
        }

        .message {
            text-align: center;
            font-weight: 600;
            margin: 15px auto;
            width: 100%;
            max-width: 520px;
        }

        
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(8px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <label for="lName">Last Name: </label>
        <input type="text" name="lName" placeholder="Ex: Cruz" required>

        <label for="fName">First Name: </label>
        <input type="text" name="fName" placeholder="Ex: Neil Wayne" required>

        <label for="mName">Middle Name: </label>
        <input type="text" name="mName" placeholder="Ex: Tubillara">

        <label for="course-section">Course Section: </label>
        <input type="text" name="course-section" placeholder="Ex: BSIT 3-2" required>

        <input type="submit" name="submit" value="Submit">
        <input type="submit" name="reset" value="Reset">
    </form>

    <?php

    $dbConnection = mysqli_connect("localhost", "root", "", "dbstudents");

    if (!$dbConnection) {
        die("Error Connection: " . mysqli_connect_error());
    }

    if(isset($_POST["reset"])){
        mysqli_query($dbConnection, "TRUNCATE TABLE studentinfo");
        $_SESSION["message"] = "<p style='color:red; text-align:center; font-weight:bold;'>All records have been deleted and ID reset to 1.</p>";
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    if(isset($_POST["submit"])){
        $lastName = htmlspecialchars($_POST["lName"]);
        $firstName = htmlspecialchars($_POST["fName"]);
        $middleName = htmlspecialchars($_POST["mName"]);
        $courseSec = htmlspecialchars($_POST["course-section"]);

        $insertQuery = "INSERT INTO studentinfo (lastname, firstname, middlename, course_section)
                        VALUES ('$lastName','$firstName','$middleName','$courseSec')";

        if(mysqli_query($dbConnection, $insertQuery)){
            $_SESSION["message"] = "<p style='text-align:center; color:green; font-weight:bold; font-size: 20px;'>Record inserted successfully!</p>";
        } else{
            $_SESSION["message"] = "<p style='text-align:center; color:red; font-weight:bold; font-size: 20px;'>Error: " . mysqli_error($dbConnection) . "</p>";
        }

        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    if(isset($_SESSION["message"])){
        echo $_SESSION["message"];
        unset($_SESSION["message"]);
    }

    $selectQuery = "SELECT * FROM studentinfo";
    $getRecord = mysqli_query($dbConnection, $selectQuery);

    if(mysqli_num_rows($getRecord) > 0){
        echo ' 
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Course and Section</th>
                </tr>';
        while($row = mysqli_fetch_assoc($getRecord)){
            $sID = $row['sid'];
            $fn = $row['firstname'];
            $ls = $row['lastname'];
            $mn = $row['middlename'];
            $cs = $row['course_section'];

            echo '
                <tr>
                    <td>' . $sID .'</td>
                    <td>' . $fn . ' ' . $mn . ' ' . $ls .'</td>
                    <td>' . $cs . '</td>
                </tr>';
        }

        echo '</table>';
    }
    ob_end_flush();
    ?>
</body>
</html>