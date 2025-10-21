<?php

session_start();

$connection = mysqli_connect("localhost", "root", "", "dbstudents");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

if (isset($_POST["btnUpdate"])) {
    updateRecords();
}

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record in Database</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 17px;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 40px 15px;
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
            padding-right: 63px;
        }

        form p {
            margin-top: 0;
            text-align: center;
            font-weight: 600;
        }

        label {
            font-size: 15px;
            color: #555;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            margin-top: 14px;
        }

        input[type="number"],
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

        input[type="number"]:focus,
        input[type="text"]:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 6px rgba(74,144,226,0.3);
            outline: none;
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

        p {
            text-align: center;
            font-size: 17px;
            margin-top: 20px;
        }

        b {
            color: #4a90e2;
        }

        span {
            font-size: 13px;
            color: grey;
            display: inline-block;
            margin-top: 2px;
        }

        input[type="number"][readonly] {
            background-color: #f5f5f5;
            border-color: #ddd;
        }

       
        form {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }

       
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

    
        .message {
            text-align: center;
            font-weight: 600;
            margin: 15px auto;
            width: 100%;
            max-width: 520px;
        }
    </style>
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <label for="stud-id">Search student by <b>"Student ID"</b></label>
        <input type="number" name="stud-id" placeholder="Ex: 23" required step="1">
        <input type="submit" name="search" value="Search">
    </form>

</body>
</html>

<?php

if (isset($_SESSION['message'])) {
    echo "<div class='message'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
}

// Searching the record
if (isset($_POST["search"])) {
    searchRec();
}


function searchRec() {
    global $connection;
    $isFound = false;
    $studIdSearch = (int) $_POST["stud-id"];

    $selectQuerySearch = mysqli_query($connection, "SELECT * FROM studentinfo");

    if (mysqli_num_rows($selectQuerySearch) > 0) {
        while ($row = mysqli_fetch_assoc($selectQuerySearch)) {
            if ((int)$row['sid'] === $studIdSearch) {
                $stud_id = (int)$row["sid"];
                $fn = htmlspecialchars($row["firstname"]);
                $mn = htmlspecialchars($row["middlename"]);
                $ln = htmlspecialchars($row["lastname"]);
                $cs = htmlspecialchars($row["course_section"]);

                echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='POST'>
                        <p>Update Student Record:</p>

                        <label>Student Id:</label>
                        <input type='number' name='sid' value='" . $stud_id . "' readonly step='1'>
                        <span>read only</span>

                        <label>Last Name:</label>
                        <input type='text' name='lname' value='" . $ln . "' required>

                        <label>First Name:</label>
                        <input type='text' name='fname' value='" . $fn . "' required>

                        <label>Middle Name:</label>
                        <input type='text' name='mname' value='" . $mn . "'>

                        <label>Course and Section:</label>
                        <input type='text' name='course' value='" . $cs . "' required>

                        <input type='submit' name='btnUpdate' value='Update'>
                    </form>";

                $isFound = true;
                break;
            }
        }
    }

    if (!$isFound) {
        echo "<p style='color:red; text-align:center; font-weight:bold;'>Student record not found!</p>";
    }
}

function updateRecords() {
    global $connection;

    $studentId = (int) $_POST["sid"];
    $newLastName = ucwords(trim($_POST["lname"]));
    $newFirstName = ucwords(trim($_POST["fname"]));
    $newMiddleName = ucwords(trim($_POST["mname"]));
    $newCourseSection = ucwords(trim($_POST["course"]));

    $queryUpdateRec = "UPDATE studentinfo
                       SET lastname = '$newLastName',
                           firstname = '$newFirstName',
                           middlename = '$newMiddleName',
                           course_section = '$newCourseSection'
                        WHERE sid = $studentId";

    if (mysqli_query($connection, $queryUpdateRec)) {
        $_SESSION["message"] = "<p style='color:green;'>Record updated successfully!</p>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION["message"] = "<p style='color:red;'>Error: " . htmlspecialchars(mysqli_error($connection)) . "</p>";
    }
}

ob_end_flush(); 
?>