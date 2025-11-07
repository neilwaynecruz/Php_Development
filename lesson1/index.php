<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Entry</title>

    <!-- Import Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* === COLORS AND STYLES VARIABLES === */
        :root {
            --main-color: #4A90E2;
            --secondary-color: #6a8ba8;
            --background-color: #F8F9FA;
            --white: #FFFFFF;
            --text-color: #34495e;
            --border-color: #E0E0E0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* === PAGE LAYOUT === */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        /* === MAIN CONTAINER === */
        .container {
            max-width: 800px;
            width: 100%;
            background-color: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        /* === HEADINGS === */
        h1, h2 {
            text-align: center;
            color: var(--main-color);
        }

        /* === FORM STYLES === */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        input[type="text"] {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
        }

        input[type="text"]:focus {
            border-color: var(--main-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        /* === BUTTON STYLE === */
        input[type="submit"] {
            padding: 12px;
            background-color: var(--main-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        input[type="submit"]:hover {
            background-color: #357ABD;
        }

        /* === TABLE STYLE === */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid var(--border-color);
        }

        th {
            background-color: var(--secondary-color);
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #F0F4F7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Data Entry</h1>

        <!-- === STUDENT FORM === -->
        <form method="post">
            <div class="form-group">
                <label for="studname">Name:</label>
                <input type="text" id="studname" name="studname">
            </div>
            <div class="form-group">
                <label for="studcourse">Course:</label>
                <input type="text" id="studcourse" name="studcourse">
            </div>
            <div class="form-group">
                <label for="school">School:</label>
                <input type="text" id="school" name="school">
            </div>
            <input type="submit" name="btnSubmit" value="Save">
        </form>

        <?php
            // Check if form was submitted
            if (isset($_POST['btnSubmit'])) {
                // Get values from form
                $name = $_POST['studname'];
                $course = $_POST['studcourse'];
                $school = $_POST['school'];

                // Show submitted data in a table
                echo "<h2>Submitted Data</h2>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Course</th><th>School</th></tr>";
                echo "<tr>";
                echo "<td>" . htmlspecialchars($name) . "</td>";
                echo "<td>" . htmlspecialchars($course) . "</td>";
                echo "<td>" . htmlspecialchars($school) . "</td>";
                echo "</tr>";
                echo "</table>";
            }
        ?>
    </div>
</body>
</html>
