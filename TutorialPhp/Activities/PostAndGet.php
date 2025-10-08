<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post and Get Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 400px;
            margin: 40px auto;
            padding: 20px 20px 20px 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="number"] {
            width: 95%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            outline: none;
            transition: all 0.15s ease-in-out;
        }

        input[type="text"]:focus, input[type="number"]:focus{
          border-color: #2563eb;
        }
        input[type="submit"] {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #1e40af;
        }
        .result {
            margin-top: 20px;
            background-color: #f0f9ff;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #c7d2fe;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Order Form</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="product">Product:</label>
            <input type="text" name="product" id="product" required>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" required min="1">

            <label for="price">Price (₱):</label>
            <input type="number" name="price" id="price" required step="0.01" min="0">

            <input type="submit" value="Submit" name="submit-btn">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $product = htmlspecialchars($_POST["product"]);
            $quantity = (float) $_POST["quantity"];
            $price = (float) $_POST["price"];

            if ($product && $quantity > 0 && $price >= 0) {
                $total = $quantity * $price;
                echo "<div class='result'>";
                echo "You ordered <strong>{$quantity}</strong> x <strong>{$product}</strong> at ₱" . number_format($price, 2) . " each.<br>";
                echo "<strong>Total: ₱" . number_format($total, 2) . "</strong>";
                echo "</div>";
            }
        }
        ?>
    </div>

</body>
</html>
