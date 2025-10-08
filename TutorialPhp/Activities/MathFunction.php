<?php
session_start(); // Start the session to store results

// ✅ Handle reset first (so old outputs don't flash)
if (isset($_POST["reset"])) {
    session_unset();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Function</title>
</head>
<body style="font-family:Arial;">

<form style="margin:5px;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

    <!-- Circumference of a Circle -->
    <h1 style="margin: 10px 0;">Circumference of a Circle</h1>
    <label for="radius">Radius:</label>
    <input type="number" name="radius" id="radius" step="0.001" min="0.001"
           value="<?php echo isset($_POST['radius']) ? $_POST['radius'] : ''; ?>">
    <input type="submit" value="Calculate" name="circumference">

    <?php
    if (isset($_POST["circumference"])) {
        $radius = (float)$_POST["radius"];
        $_SESSION['circumference_result'] = round(2 * pi() * $radius, 2) . " cm";
    }

    if (isset($_SESSION['circumference_result'])) {
        echo "<br><br>Circumference of circle: " . $_SESSION['circumference_result'];
    }
    ?>


    <!-- Area of a Circle -->
    <h1 style="margin: 10px 0;">Area of a Circle</h1>
    <label for="radius2">Radius:</label>
    <input type="number" name="radius2" id="radius2" step="0.001" min="0.001"
           value="<?php echo isset($_POST['radius2']) ? $_POST['radius2'] : ''; ?>">
    <input type="submit" value="Calculate" name="area">

    <?php
    if (isset($_POST["area"])) {
        $radius = (float)$_POST["radius2"];
        $_SESSION['area_result'] = round(pi() * pow($radius, 2), 2) . " cm²";
    }

    if (isset($_SESSION['area_result'])) {
        echo "<br><br>Area of a circle: " . $_SESSION['area_result'];
    }
    ?>


    <!-- Volume of a Sphere -->
    <h1 style="margin: 10px 0;">Volume of a Sphere</h1>
    <label for="radius3">Radius:</label>
    <input type="number" name="radius3" id="radius3" step="0.001" min="0.001"
           value="<?php echo isset($_POST['radius3']) ? $_POST['radius3'] : ''; ?>">
    <input type="submit" value="Calculate" name="volume">

    <?php
    if (isset($_POST["volume"])) {
        $radius = (float)$_POST["radius3"];
        $_SESSION['volume_result'] = round((4 / 3) * pi() * pow($radius, 3), 2) . " cm³";
    }

    if (isset($_SESSION['volume_result'])) {
        echo "<br><br>Volume of a sphere: " . $_SESSION['volume_result'];
    }
    ?>

    <br><br>
    <input type="submit" value="Reset All" name="reset">

</form>

</body>
</html>
