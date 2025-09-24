<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Product Data Entry</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            margin: 40px 0;
        }
        h1, h2 {
            color: #862633; /* PUP Maroon */
            margin-bottom: 16px;
        }
        form {
            background: #fff;
            padding: 16px 24px;
            border-radius: 8px;
            border: 4px solid #862633;
            margin-bottom: 24px;
            min-width: 320px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            font-size: 16px;
            color: #862633;
        }
        input[type="text"], input[type="number"] {
            margin-top: 4px;
            margin-bottom: 12px;
            padding: 8px 10px;
            font-size: 15px;
            border: 1px solid #A05229; /* PUP Brown */
            border-radius: 5px;
            background: #ffeb99b4; /* PUP Yellow */
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background: #862633; /* PUP Maroon */
            color: #FFD700;     /* PUP Gold */
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 8px;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        input[type="submit"]:hover {
            background: #a34652ff; /* PUP Brown */
        }
        table {
            border-collapse: collapse;
            margin-top: 12px;
            background: #fff;
            border-radius: 8px;
            min-width: 300px;
            border: 2px solid #862633;
        }
        th, td {
            border: 2px solid #ffffffff;
            padding: 10px 14px;
            text-align: center;
            font-size: 15px;
        }
        th,td {
            background: #862633;
            color: #FFD700;
            font-weight: 600;
        }

    </style>
</head>
<body>
    <h1>Product Entry</h1>
    <form method="post">
        <label for="code">Product Code:</label>
        <input type="text" id="code" name="code" required>

        <label for="desc">Description:</label>
        <input type="text" id="desc" name="desc" required>

        <label for="price">Price:</label>
        <input type="number" step="0.5" id="price" name="price" >

        <input type="submit" name="btnSubmit" value="Submit">
    </form>

    <?php
        // Check if form was submitted
        if (isset($_POST['btnSubmit'])) {
            // Get values from form
            $code = $_POST['code'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];

            // Show submitted data in a table
            echo "<h2>Product</h2>";
            echo "<table>";
            echo "<tr><th>Product Code</th><th>Description</th><th>Price</th></tr>";
            echo "<tr>";
            echo "<td>" . htmlspecialchars($code) . "</td>";
            echo "<td>" . htmlspecialchars($desc) . "</td>";
            echo "<td>" . htmlspecialchars($price) . "</td>";
            echo "</tr>";
            echo "</table>";
        }
    ?>
</body>
</html>