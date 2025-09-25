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
            transition: all 0.2s ease;
        }
        input[type="submit"]:hover {
            background: #a34652ff;
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
            transition: background-color 0.3s ease;
        }

        th:hover, td:hover{
            background-color: #ad3949ff;
        }

    </style>
</head>
<body>
    <h1>Product Entry (TABLE)</h1>
    <form method="post">
        <label for="code">Product Code:</label>
        <input type="text" id="code" name="code" required>

        <label for="desc">Description:</label>
        <input type="text" id="desc" name="desc" required>

        <label for="price">Price:</label>
        <input type="number" step="0.5" id="price" name="price" >

        <input type="submit" name="btnSubmit" value="Submit">
        <input type="button" value="Reset" onclick="resetProducts()" style="background: #862633; color: #FFD700;">
        <input type="hidden" id="resetField" name="btnReset" value="">
    </form>

    <?php
        session_start(); // Start the session

        // If reset button clicked, clear products
        if (isset($_POST['btnReset']) && $_POST['btnReset'] === "reset") {
            $_SESSION['products'] = [];
        }

        // Initialize the products array if not set
        if (!isset($_SESSION['products'])) {
            $_SESSION['products'] = [];
        }

        // If form submitted, append to products array in session
        if (isset($_POST['btnSubmit'])) {
            $code = $_POST['code'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];

            // Add new product to session array
            $_SESSION['products'][] = [
                'code' => $code,
                'desc' => $desc,
                'price' => $price
            ];
        }

        // Display products from session
        if (!empty($_SESSION['products'])) {
            echo "<h2>Product List</h2>";
            echo "<table>";
            echo "<tr><th>Product Code</th><th>Description</th><th>Price</th></tr>";
            foreach ($_SESSION['products'] as $product) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($product['code']) . "</td>";
                echo "<td>" . htmlspecialchars($product['desc']) . "</td>";
                echo "<td>" . htmlspecialchars($product['price']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    ?>
    <script>
    function resetProducts() {
        document.getElementById('resetField').value = 'reset';
        document.forms[0].submit();
    }
    </script>
</body>
</html>