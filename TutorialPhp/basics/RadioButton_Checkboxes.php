<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Button</title>
</head>
<body>
    <form action="RadioButton_Checkboxes.php" method="post">
        Credit Cards: <br>
        <input type="radio" name="credit_card" value="Visa Card">
        <label for="credit_card">Visa</label><br>

        <input type="radio" name="credit_card" value="Master Card">
        <label for="credit_card">Master Card</label><br>
        
        <input type="radio" name="credit_card" value="American Express">
        <label for="credit_card">American Express</label><br><br>


        Foods: <br>
        <input type="checkbox" name="foods[]" value="Sinigang">
        <label for="foods[]">Sinigang</label><br>
        <input type="checkbox" name="foods[]" value="Adobo">
        <label for="foods[]">Adobo</label><br>
        <input type="checkbox" name="foods[]" value="Chicharon">
        <label for="foods[]">Chicharon</label><br>
        <input type="checkbox" name="foods[]" value="Kare-kare">
        <label for="foods[]">Kare-kare</label><br><br>

        <input type="submit" name="submit-btn" value="submit"><br><br>

    </form>
    
</body>
</html>

<?php
    if(isset($_POST["submit-btn"])){
        $credit_card = null;
        $foods = null;

        if(isset($_POST["credit_card"])){
            $credit_card = $_POST["credit_card"];
        }

        switch($credit_card){
            case "Visa Card":
                echo "You selected a <b>Visa Card</b><br><br>";
                break;
            case "Master Card":
                echo "You selected a <b>Master Card</b><br><br>";
                break;
            case "American Express":
                echo "You selected a <b>American Express</b><br><br>";
                break;
            default:
                echo "Please make a selection for credit cards<br><br>";
                return;
        }

        if(isset($_POST["foods"])){
            $foods = $_POST["foods"];

            echo "Foods you like: <br>";
            foreach($foods as $food){
                echo "<b>" . $food . "</b>" . "<br>";
            }
        }
    }
?>