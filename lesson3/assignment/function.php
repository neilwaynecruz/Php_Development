<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Function</title>
    <style>
        body {
            font-family: "Poppins", Arial, sans-serif;
            background-color: #D4AF37;
            color: #2c2c2c;
            margin: 0;
            padding: 0;
        }

        h3 {
            color: #800000;
            text-align: center;
            font-size: 22px;
        }
        

        form {
            background-color: #ffffff;
            border: 2px solid #800000;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(128, 0, 0, 0.1);
            padding: 30px;
            padding-top: 10px;
            padding-right: 45px; 
            width: 400px;
            margin: 40px auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4a0000;
        }

        input[type="number"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #bfbfbf;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            outline: none;
        }

        input[type="number"]:focus, select:focus {
            border-color: #800000;
            box-shadow: 0 0 5px rgba(128, 0, 0, 0.4);
        }

        input[type="submit"] {
            width: 100%;
            background-color: #800000;
            color: #fff;
            border: none;
            padding: 10px 0;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.18s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #a31919;
        }

        .result-box {
            background-color: #fff5e1;
            border: 1px solid #e5c07b;
            border-radius: 10px;
            width: 420px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 3px 8px rgba(128, 0, 0, 0.1);
        }

        .result-box p {
            font-size: 16px;
            line-height: 1.6;
            margin: 5px 0;
        }

        .highlight {
            color: #800000;
            font-weight: bold;
        }

        select:hover, select:focus{
            border-color: #800000;
            background-color: #fff2f2ff;
            box-shadow: 0 0 5px rgba(128, 0, 0, 0.4);
        }
    </style>
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <h2 style="text-align:center; color:#800000;">Net Pay Calculator</h2>
        <label for="base-pay">Base Pay:</label>
        <input type="number" name="base-pay" id="base-pay" required step="0.1" min="0">

        <label for="allowances">Allowances:</label>
        <input type="number" name="allowances" id="allowances" required step="0.1" min="0">

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="">-- Select Status --</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
        </select>

        <input type="submit" value="Calculate" name="calculate-btn" id="calculate-btn">
    </form>

    <?php
    if (isset($_POST["calculate-btn"])){
        function grossPay(): float{
            $basePay = (float) $_POST["base-pay"];
            $allowances = (float) $_POST["allowances"];
            return $basePay + $allowances;
        }

        function deduction(): float{
            $status = trim($_POST["status"]);
            $basePay = (float) $_POST["base-pay"];

            if ($status === "Single") {
                return $basePay * 0.25;
            } elseif ($status === "Married") {
                return $basePay * 0.15;
            } else {
                return 0.0;
            }
        }

        function netPay($gross, $deduction): float{
            return $gross - $deduction;
        }

        $gross_pay = grossPay();
        $deduct = deduction();
        $netPay = netPay(gross: $gross_pay, deduction: $deduct);

        echo '<div class="result-box">';
        echo "<h3>Computation Result</h3>";
        echo "<p>Base Pay: <span class='highlight'>₱" . number_format((float) $_POST["base-pay"], 2) . "</span></p>";
        echo "<p>Allowance: <span class='highlight'>₱" . number_format((float) $_POST["allowances"], 2) . "</span></p>";
        echo "<p>Status: <span class='highlight'>" . $_POST["status"] . (($_POST["status"] === "Single") ? " (25% tax of base pay) " : " (15% tax of base pay) ") . "</span></p>";
        echo "<p>Gross Pay: <span class='highlight'>₱" . number_format($gross_pay, 2) . "</span></p>";
        echo "<p>Deduction: <span class='highlight'>₱" . number_format($deduct, 2) . "</span></p>";
        echo "<p>Net Pay: <span class='highlight'>₱" . number_format($netPay, 2) . "</span></p>";
        echo '</div>';
    }
    ?>
</body>
</html>
