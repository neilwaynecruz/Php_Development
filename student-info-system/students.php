<?php
ob_start();
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "iskolar_sis_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

if (isset($_POST["create"])) {
    createStudent();
}

if (isset($_POST["btnUpdate"])) {
    updateStudent();
}

if (isset($_POST["delete"])) {
    deleteStudent();
}

if (isset($_POST["reset"])) {
    resetTable();
}

function sanitize($str) {
    return htmlspecialchars(trim($str));
}

function validateStudent($ln, $fn, $cs) {
    $errors = [];
    $lnTrim = trim($ln);
    $fnTrim = trim($fn);
    $csTrim = trim($cs);

    if ($lnTrim === '') $errors[] = "Last Name is required.";
    if ($fnTrim === '') $errors[] = "First Name is required.";
    if ($csTrim === '') $errors[] = "Course & Section is required.";

    // simple alpha checks (allows spaces, dashes, periods)
    $lnCheck = str_replace([' ', '-', '.'], '', $lnTrim);
    $fnCheck = str_replace([' ', '-', '.'], '', $fnTrim);
    if ($lnTrim !== '' && !ctype_alpha($lnCheck)) $errors[] = "Last Name must contain letters only.";
    if ($fnTrim !== '' && !ctype_alpha($fnCheck)) $errors[] = "First Name must contain letters only.";

    return $errors;
}

function createStudent() {
    global $connection;

    $lastName = ucwords(sanitize($_POST["lName"]));
    $firstName = ucwords(sanitize($_POST["fName"]));
    $middleName = ucwords(sanitize($_POST["mName"]));
    $courseSec = strtoupper(sanitize($_POST["course-section"]));

    $errors = validateStudent($lastName, $firstName, $courseSec);

    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $insertQuery = "INSERT INTO studentinfo (lastname, firstname, middlename, course_section)
                    VALUES ('$lastName','$firstName','$middleName','$courseSec')";
    if (mysqli_query($connection, $insertQuery)) {
        $_SESSION["message"] = '<div class="alert alert-success">Record inserted successfully!</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

function updateStudent() {
    global $connection;

    $studentId = (int) $_POST["sid"];
    $newLastName = ucwords(sanitize($_POST["lname"]));
    $newFirstName = ucwords(sanitize($_POST["fname"]));
    $newMiddleName = ucwords(sanitize($_POST["mname"]));
    $newCourseSection = strtoupper(sanitize($_POST["course"]));

    $errors = validateStudent($newLastName, $newFirstName, $newCourseSection);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $queryUpdateRec = "UPDATE studentinfo
                       SET lastname = '$newLastName',
                           firstname = '$newFirstName',
                           middlename = '$newMiddleName',
                           course_section = '$newCourseSection'
                       WHERE sid = $studentId";
    if (mysqli_query($connection, $queryUpdateRec)) {
        $_SESSION["message"] = '<div class="alert alert-success">Record updated successfully!</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

function deleteStudent() {
    global $connection;
    $id = (int) $_POST["delete_id"];
    $sql = "DELETE FROM studentinfo WHERE sid = $id";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">Record deleted.</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

function resetTable() {
    global $connection;
    mysqli_query($connection, "TRUNCATE TABLE studentinfo");
    $_SESSION["message"] = '<div class="alert alert-warning">All records deleted and ID reset to 1.</div>';
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students | Student Info System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap v5 + Theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Dark navbar so Account/Logout are visible -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="brand-dot"></span> Student Info System
            </a>
            <div class="ms-auto">
                <span class="navbar-text me-3 text-white">Hello, <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <a href="account.php" class="btn btn-outline-light btn-sm me-2">Account</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Create Student</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lName" class="form-control" placeholder="Ex: Cruz" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="fName" class="form-control" placeholder="Ex: Neil Wayne" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="mName" class="form-control" placeholder="Ex: Tubillara">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course & Section</label>
                                <input type="text" name="course-section" class="form-control" placeholder="Ex: BSIT 3-2" required>
                            </div>
                            <button type="submit" name="create" class="btn btn-primary w-100">Submit</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Search Student (by ID)</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="row g-2">
                            <div class="col-8">
                                <input type="number" name="search-id" class="form-control" placeholder="Ex: 23" required step="1">
                            </div>
                            <div class="col-4">
                                <button type="submit" name="search" class="btn btn-secondary w-100">Search</button>
                            </div>
                        </form>
                        <div class="form-text mt-2">If found, an edit form will appear below.</div>
                    </div>
                </div>

                <?php
                // Render update form here if search submitted
                if (isset($_POST["search"])) {
                    searchRec();
                }
                ?>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title mb-0">Student Records</h5>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" onsubmit="return confirm('This will delete all records. Continue?');">
                                <button type="submit" name="reset" class="btn btn-outline-danger btn-sm">Reset All</button>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Student Name</th>
                                        <th>Course & Section</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $selectQuery = "SELECT * FROM studentinfo";
                                $getRecord = mysqli_query($connection, $selectQuery);

                                if ($getRecord && mysqli_num_rows($getRecord) > 0) {
                                    while ($row = mysqli_fetch_assoc($getRecord)) {
                                        $sID = (int)$row['sid'];
                                        $fn = htmlspecialchars($row['firstname']);
                                        $ls = htmlspecialchars($row['lastname']);
                                        $mn = htmlspecialchars($row['middlename']);
                                        $cs = htmlspecialchars($row['course_section']);
                                        echo '<tr>
                                            <td>'. $sID .'</td>
                                            <td>'. $fn .' '. $mn .' '. $ls .'</td>
                                            <td>'. $cs .'</td>
                                            <td class="text-end">
                                                <form action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this record?\');">
                                                    <input type="hidden" name="delete_id" value="'. $sID .'">
                                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center text-muted">No records found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Search routine that prints an Update form (mirrors your style)
function searchRec() {
    global $connection;
    $isFound = false;
    $studId = (int) $_POST["search-id"];

    $selectQuerySearch = mysqli_query($connection, "SELECT * FROM studentinfo");

    if ($selectQuerySearch && mysqli_num_rows($selectQuerySearch) > 0) {
        while ($row = mysqli_fetch_assoc($selectQuerySearch)) {
            if ((int)$row['sid'] === $studId) {
                $stud_id = (int)$row["sid"];
                $fn = htmlspecialchars($row["firstname"]);
                $mn = htmlspecialchars($row["middlename"]);
                $ln = htmlspecialchars($row["lastname"]);
                $cs = htmlspecialchars($row["course_section"]);

                echo '<div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Update Student Record</h5>
                            <form action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Student ID</label>
                                    <input type="number" name="sid" value="'. $stud_id .'" readonly class="form-control" step="1">
                                    <div class="form-text">read only</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lname" value="'. $ln .'" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="fname" value="'. $fn .'" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="mname" value="'. $mn .'" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Course & Section</label>
                                    <input type="text" name="course" value="'. $cs .'" class="form-control" required>
                                </div>
                                <button type="submit" name="btnUpdate" class="btn btn-success w-100">Update</button>
                            </form>
                        </div>
                    </div>';
                $isFound = true;
                break;
            }
        }
    }

    if (!$isFound) {
        echo '<div class="alert alert-danger mt-3">Student record not found!</div>';
    }
}

ob_end_flush();
?>