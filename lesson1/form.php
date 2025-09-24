<!DOCTYPE html>
<html>
<head>
    <title>Product Entry Form</title>
</head>
<body>
    <h2>Enter Product Information</h2>
    <form action="table.php" method="post">
        <label for="code">Product Code:</label>
        <input type="text" id="code" name="code" required><br><br>

        <label for="desc">Description:</label>
        <input type="text" id="desc" name="desc" required><br><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>