<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Input Form</title>
</head>
<body>
    <h1>Product Information Entry</h1>
    
    <form method="POST" action="display.php">
        <p>
            <label for="product_code">Product Code:</label><br>
            <input type="text" id="product_code" name="product_code" required>
        </p>
        
        <p>
            <label for="description">Description:</label><br>
            <input type="text" id="description" name="description" required>
        </p>
        
        <p>
            <label for="price">Price:</label><br>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
        </p>
        
        <p>
            <input type="submit" value="Submit Product">
        </p>
    </form>
</body>
</html>