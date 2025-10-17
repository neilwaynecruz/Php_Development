<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arrays Act</title>
</head>
<body>
    <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method="POST">
        <label for="txtBox">Text Box:</label>
        <input type="text" name="country" id="country">
        <input type="submit" name="submit-btn" value="submit">
    </form>
    
</body>
</html>

<?php

if (isset($_POST["submit-btn"])) {
    echo "<br>";
      $capitals = array(
        "Philippines" => "Manila",
        "USA" => "Washington D.C.",
        "South Korea" => "North Korea",
        "India" => "New Delhi",
        "Japan" => "Kyoto",
    );
    $country = ucfirst(strtolower(htmlspecialchars($_POST["country"]))) === "Usa" ? "USA" : ucfirst(htmlspecialchars($_POST["country"]));

    if(!array_key_exists($country,$capitals )){
        echo "Aray koh wala yung key na <b>\"{$country}\"</b> sa associative array";
        return;
    }else{
        $capital = $capitals[$country];
        echo 'The capital of  ' . '<b>' .$country . '</b>' . ' is: ' . '<b>' . $capital . '</b>';
    }

    }
?>