<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Display</title>
</head>
<body>
    <h1>Product Information Display</h1>
    
    <?php
    // Check if form data was submitted
    if (isset($_POST['product_code']) && isset($_POST['description']) && isset($_POST['price'])) {
        // Get values from form
        $product_code = htmlspecialchars($_POST['product_code']);
        $description = htmlspecialchars($_POST['description']);
        $price = htmlspecialchars($_POST['price']);
        
        // Display in table format
        echo "<h2>Table Format</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>Product Code</td><td>" . $product_code . "</td></tr>";
        echo "<tr><td>Description</td><td>" . $description . "</td></tr>";
        echo "<tr><td>Price</td><td>$" . $price . "</td></tr>";
        echo "</table>";
        
        // Display in form format
        echo "<h2>Form Format</h2>";
        echo "<form>";
        echo "<p>";
        echo "<label>Product Code:</label><br>";
        echo "<input type='text' value='" . $product_code . "' readonly>";
        echo "</p>";
        echo "<p>";
        echo "<label>Description:</label><br>";
        echo "<input type='text' value='" . $description . "' readonly>";
        echo "</p>";
        echo "<p>";
        echo "<label>Price:</label><br>";
        echo "<input type='text' value='$" . $price . "' readonly>";
        echo "</p>";
        echo "</form>";
        
    } else {
        echo "<p>No product data received. Please <a href='input.php'>submit the form</a> first.</p>";
    }
    ?>
    
    <p><a href="input.php">Enter Another Product</a></p>
</body>
</html>