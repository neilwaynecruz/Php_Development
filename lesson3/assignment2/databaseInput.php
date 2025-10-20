<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        table{
            margin: 50px auto;
            border-collapse: collapse;
            width: 50%;
        }
        
        th, td{
            border: 1px solid black;
            padding: 5px 5px;
            text-align: center;
        }

        form{
            margin-top: 5%;
            text-align: center;
        }

        input[type="text"], input[type="submit"]{
            margin: 5px auto;
            width: 20%;
            padding: 5px 5px;
        }
        input[type="text"]::placeholder{
            text-align: center;
        }
    </style>
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <label for="lName">Last Name: </label>
        <input type="text" name="lName" placeholder="Ex: Cruz"><br><br>


        <label for="fName">First Name: </label>
        <input type="text" name="fName" placeholder="Ex: Neil Wayne"><br><br>


        <label for="mName">Middle Name: </label>
        <input type="text" name="mName" placeholder="Ex: Tubillara"><br><br>
        

        <label for="course-section">Course Section: </label>
        <input type="text" name="course-section" placeholder="Ex: BSIT 3-2"><br><br>

        <input type="submit" name="submit" value="Submit">
        <input type="submit" name="reset" value="Reset"> <br><br>
    </form>

    <?php
    session_start();

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
        $courseSec =  htmlspecialchars($_POST["course-section"]);

        $insertQuery = "INSERT INTO studentinfo (lastname, firstname, middlename, course_section)
                     VALUES ('$lastName','$firstName','$middleName','$courseSec')";

        if(mysqli_query($dbConnection, $insertQuery)){
            $_SESSION["message"] = "<p style='text-align:center; color:green; font-weight:bold; font-size: 20px;'>Record inserted successfully!</p>";
        } else{
             $_SESSION["message"] = "<p style='text-align:center; color:red; font-weight:bold; font-size: 20px;'>Error: " . mysqli_error($dbConnection) . "</p";
        }

        header("Location: ". htmlspecialchars($_SERVER["PHP_SELF"]));
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
                    <th>Student Name</th>
                    <th>Course and Section</th>
                </tr>';
        while($row = mysqli_fetch_assoc($getRecord)){
            $fn = $row['firstname'];
            $ls = $row['lastname'];
            $mn = $row['middlename'];
            $cs = $row['course_section'];

            echo '
                <tr>
                    <td>' . $fn . " " . $mn . " " . $ls .'</td>
                    <td>' . $cs . '</td>
                </tr>';
        }

        echo '</table>';

    }

    ?>
</body>
</html>