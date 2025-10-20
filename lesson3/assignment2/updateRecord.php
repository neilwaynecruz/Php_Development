<?php
// Start session before any HTML output
session_start();

// Database connection
$connection = mysqli_connect("localhost", "root", "", "dbstudents");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

// Handle update before any HTML is sent (prevents header errors)
if (isset($_POST["btnUpdate"])) {
    updateRecords();
}

// Buffer output to safely show search results after HTML
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record in Database</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20px;
        }

        input[type="number"], input[type="submit"],input[type="text"]{
            padding: 5px 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <label for="stud-id">Search student by <b>"Student ID"</b></label> <br><br>
        <input type="number" name="stud-id" placeholder="Ex: 23" required step="1">
        <input type="submit" name="search" value="Search"> <br><br>
    </form>

</body>
</html>

<?php
// Display session message (✅ safe and renders HTML)
if (isset($_SESSION['message'])) {
    echo "<p style='text-align:center; font-weight:bold;'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}

// Searching the record
if (isset($_POST["search"])) {
    searchRec();
}

// ---------- FUNCTIONS ----------
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

                echo "Update Student Record: <br><br>";
                echo '
                    <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST">
                        <label>Student Id: </label>
                        <input type="number" name="sid" value="' . $stud_id . '" readonly step="1"> 
                        <span style="color:grey;">read only</span> <br><br>

                        <label>Last Name: </label>
                        <input type="text" name="lname" value="' . $ln . '" required> <br><br>

                        <label>First Name: </label>
                        <input type="text" name="fname" value="' . $fn . '" required> <br><br>

                        <label>Middle Name: </label>
                        <input type="text" name="mname" value="' . $mn . '" required> <br><br>

                        <label>Course and Section: </label>
                        <input type="text" name="course" value="' . $cs . '" required> <br><br>

                        <input type="submit" name="btnUpdate" value="Update"><br><br><br>
                    </form>';

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

    // Update query
    $queryUpdateRec = "UPDATE studentinfo
                       SET lastname = '$newLastName',
                           firstname = '$newFirstName',
                           middlename = '$newMiddleName',
                           course_section = '$newCourseSection'
                        WHERE sid = $studentId";

    if (mysqli_query($connection, $queryUpdateRec)) {
        $_SESSION["message"] = "<span style='color:green;'>Record updated successfully!</span>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION["message"] = "<span style='color:red;'>Error: " . htmlspecialchars(mysqli_error($connection)) . "</span>";
    }
}

ob_end_flush(); // Flush output buffer
?>
