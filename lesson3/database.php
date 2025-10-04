<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My SQL Database</title>
</head>
<body>
    <?php
    $con = mysqli_connect("localhost","root","","dbstudents");

    if (!$con){
        die("No Connection: " . mysqli_connect_error());
    }

    
    ?>
    <form method="post" action="">
        Last Name: <input type="text" name="lname" required><br>
        First Name: <input type="text" name="fname" required><br>
        Middle Name: <input type="text" name="mname" required><br>
        Course Section: <input type="text" name="course" required><br>
        <input type="submit" name="btnSave" value="Submit">
    </form>
    <?php

    if (isset($_POST['btnSave'])) {
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $course = $_POST['course'];

        $sql = "INSERT INTO studentinfo (lastname, firstname, middlename, course_section) 
                VALUES ('$lname', '$fname', '$mname', '$course')";

        if (mysqli_query($con, $sql)) {
            echo "<p style='color:green;'>Record inserted successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . mysqli_error($con) . "</p>";
        }
    }

    mysqli_close($con);
    ?>
</body>
</html>
