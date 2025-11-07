

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Handling</title>
</head>
<body>
    <h1>Choose what type of form method you preffer?</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
        <button type="submit" name="method" value="post">POST</button>
        <button type="submit" name="method" value="get">GET</button>
    </form>

    <?php
        if(isset($_GET['method'])){
             $method = htmlspecialchars($_GET['method']);
             echo "<h2>" . strtoupper($method) .  " Method Form</h2>";
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="<?php echo htmlspecialchars(strtolower($method))?>">
        Name: <input type="text" name="name" id="name" required><br><br>
        Username: <input type="text" name="username" id="username" required><br><br>
        Password: <input type="text" name="password" id="password" required><br><br>
        <input type="submit" name="submit" id="submit">
    </form>
    <?php }?>

    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])){
            echo "<H3>POST DATA RECEIVED</H3>";
            echo "Name: " . htmlspecialchars($_POST['name']) . "<br>";
            echo "Username: " . htmlspecialchars($_POST['username']) . "<br>";
            echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";
        }

        if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])){
            echo "<H3>GET DATA RECEIVED</H3>";
            echo "Name: " . htmlspecialchars($_GET['name']) . "<br>";
            echo "Username: " . htmlspecialchars($_GET['username']) . "<br>";
            echo "Password: " . htmlspecialchars($_GET['password']) . "<br>";
        }
    ?>

</body>
</html>


<?php
?>

