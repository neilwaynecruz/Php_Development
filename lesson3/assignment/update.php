<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Statement</title>
</head>
<body>

    <form action="" method="POST">
        <label for="name">Search record by 'last name'</label><br><br>
        <input type="text" name="last-name" placeholder="Last Name">
        <input type="submit" name="search" value="search"> <br><br>
    </form>

<?php
session_start();
$connection = mysqli_connect("localhost","root","", "dbstudents");

if(!$connection){
    die("No connection: " . mysqli_connect_error());
}


if (isset($_SESSION['message'])) {
    echo "<p style='text-align:center; font-weight:bold;'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}


if(isset($_POST["search"])){
    searchRec();
}

function searchRec(){
    global $connection;
    $nameSearch = ucfirst(htmlspecialchars($_POST["last-name"]));
    $query = mysqli_query($connection,"SELECT * FROM studentinfo");
    $found = false;

    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            if(strcmp($row["lastname"], $nameSearch) == 0){
                $fn = $row["firstname"];
                $mn = $row["middlename"];
                $ln = $row["lastname"];
                $cs = $row["course_section"];

                echo "Update Record:<br><br>";
                echo '
                    <form method="post" action="">
                        <label>Last Name:</label> 
                        <input type="text" name="lname" value="' . $ln . '" required><br><br>

                        <label>First Name:</label> 
                        <input type="text" name="fname" value="' . $fn . '" required><br><br>

                        <label>Middle Name:</label> 
                        <input type="text" name="mname" value="' . $mn . '"><br><br>

                        <label>Course Section:</label> 
                        <input type="text" name="course" value="' . $cs . '" required><br><br>

                        <input type="hidden" name="origLastName" value="' . $ln . '" readonly>

                        <input type="submit" name="btnUpdate" value="Update"> <br><br><br>
                    </form>';
                $found = true;
                break;
            }
        }
    }
    if(!$found){
            echo "<p style='color:red; text-align:center;'>RECORD NOT FOUND!!</p>";
        }
}

if (isset($_POST['btnUpdate'])){
    updateRecords();
}

function updateRecords(){
    global $connection;

    $origLastName = $_POST['origLastName'];
    $newlname = ucwords($_POST['lname']);
    $newfname = ucwords($_POST['fname']);
    $newmname = ucwords($_POST['mname']);
    $newcourse = strtoupper($_POST['course']);

    $sql = "UPDATE studentinfo
            SET lastname =  '$newlname',
                firstname = '$newfname',
                middlename = '$newmname',
                course_section = '$newcourse'
            WHERE lastname = '$origLastName'";

    if (mysqli_query($connection, $sql)) {
        $_SESSION['message'] = "<span style='color:green;'>Record updated successfully!</span>";
    } else {
        $_SESSION['message'] = "<span style='color:red;'>Error: " . mysqli_error($connection) . "</span>";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
</body>
</html>
