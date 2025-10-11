<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My SQL Database</title>
    <style>
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 60%;
        }
        th, td {
            border: 1px solid #555;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        input[type="text"] {
            margin: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <?php
    // Database connection
    $con = mysqli_connect("localhost", "root", "", "dbstudents");

    if (!$con) {
        die("No Connection: " . mysqli_connect_error());
    }

    // Handle Reset Table button
    if (isset($_POST["btnReset"])) {
        mysqli_query($con, "TRUNCATE TABLE studentinfo");
        echo "<p style='color:red; text-align:center;'>All records have been deleted and ID reset to 1.</p>";
        // Redirect to clear POST data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Handle Save button
    if (isset($_POST['btnSave'])) {
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $course = $_POST['course'];

        $sql = "INSERT INTO studentinfo (lastname, firstname, middlename, course_section) 
                VALUES ('$lname', '$fname', '$mname', '$course')";

        if (mysqli_query($con, $sql)) {
            echo "<p style='color:green; text-align:center;'>Record inserted successfully!</p>";
            // Redirect to clear POST data (prevents re-insert on refresh)
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<p style='color:red; text-align:center;'>Error: " . mysqli_error($con) . "</p>";
        }
    }
    ?>

    <!-- Form -->
    <form method="post" action="">
        <label>Last Name:</label> 
        <input type="text" name="lname" required><br><br>
        <label>First Name:</label> 
        <input type="text" name="fname" required><br><br>
        <label>Middle Name:</label> 
        <input type="text" name="mname" required><br><br>
        <label>Course Section:</label> 
        <input type="text" name="course" required><br><br>
        <input type="submit" name="btnSave" value="Submit">
        <input type="submit" name="btnReset" value="Reset Table">
    </form>

    <?php
    // Fetch and display table
    $q = mysqli_query($con, "SELECT * FROM studentinfo");

    if (mysqli_num_rows($q) > 0) {
        echo "<table>
                <tr>
                    <th>Name</th>
                    <th>Course / Section</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($q)) {
            $fn = $row["firstname"];
            $mn = $row["middlename"];
            $ln = $row["lastname"];
            $cs = $row["course_section"];

            echo "<tr>
                    <td>{$fn} {$mn} {$ln}</td>
                    <td>{$cs}</td>
                </tr>";
        }
        echo "</table>";
    }

    mysqli_close($con);
    ?>
</body>
</html>
