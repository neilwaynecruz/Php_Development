<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Product In Form</title>
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
            width: 35%;
            display: flex;
            flex-direction: column;
        }
        .output-form {
            background: #fff;
            padding: 16px 24px;
            border-radius: 8px;
            border: 4px solid #862633;
            min-width: 320px;
            width: 35%;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .output-form h2 {
            text-align: center;
            margin-bottom: 14px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            font-size: 20px;
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
        input[type="submit"],input[type="button"] {
            background: #862633; 
            color: #FFD700;     
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 8px;
            font-weight: bold;
            transition: all 0.2s ease-in-out;
            width: 100%; 
            text-align: center;
            letter-spacing: 1px;
        }
        input[type="submit"]:hover, input[type="button"]:hover {
            background: #a34652ff;
        }
    </style>
</head>
<body>
    <h1>Product Entry (Form)</h1>
    <form method="post">
        <label for="code">Product Code:</label>
        <input type="text" id="code" name="code" required>

        <label for="desc">Description:</label>
        <input type="text" id="desc" name="desc" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01"  id="price" name="price" required>

        <input type="submit" name="btnSubmit" value="Submit">
        <input type="submit" name="btnReset" value="Reset">
    </form>

    <?php
        // Show latest submitted product only
        if (isset($_POST['btnSubmit'])) {
            $code = $_POST['code'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];
            
            // Display the submitted data in a form style
            echo '<div class="output-form">';
            echo '<h2>Form Result</h2>';
            echo '<label>Product Code:</label>';
            echo '<input type="text" value="' . htmlspecialchars($code) . '" readonly>';
            echo '<label>Description:</label>';
            echo '<input type="text" value="' . htmlspecialchars($desc) . '" readonly>';
            echo '<label>Price:</label>';
            echo '<input type="text" value="' . htmlspecialchars($price) . '" readonly>';
            echo '</div>';
        }
        else {
            if (isset($_POST['btnReset'])) {
                // for reset button functionality: do nothing, just clear the form
            }
        }
    ?>
</body>
</html>