<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Pay Calculator</title>
    <style>
        /* CSS styles remain the same */
        body {
            display: flex;
            flex-direction: row;
            justify-content: center;     
            align-items: stretch;
            gap: 30px;                   
            font-family: "Poppins", Arial, sans-serif;
            background-color: #D4AF37;
            color: #2c2c2c;
            margin: 0;
            padding: 20px; 
        }

        .card {
            background-color: #ffffff;
            border: 2px solid #800000;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(128, 0, 0, 0.1);
            padding: 30px;
            width: 400px; 
            margin-top: 40px; 
            box-sizing: border-box; 
        }

        h3 {
            color: #800000;
            text-align: center;
            font-size: 22px;
            margin-top: 0; 
            margin-bottom: 25px;
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
            transition: all 0.18s ease-in-out;
            box-sizing: border-box;
        }

        input[type="number"]:focus, select:focus {
            border-color: #800000;
            box-shadow: 0 0 5px rgba(128, 0, 0, 0.4);
        }

        select:hover, select:focus {
            border-color: #800000;
            background-color: #fff2f2ff;
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

       
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #e5c07b;
            transition: all 0.16s ease;
        }

        th {
            text-align: left;
            color: #4a0000;
            font-size: 16px;
        }
        
        th:last-child {
            text-align: right;
        }

        td:last-child {
            text-align: right;
        }

        td:hover {
            color: #ff0000ff;
            transform: scale(1.02);
        }
        .highlight {
            color: #800000;
            font-weight: bold;
        }

        .net {
            color: #800000;
            font-weight: bold;
            font-size: 18px;
        }
        .placeholder-text {
            color: #4a0000;
            text-align: center;
            line-height: 1.5;
            padding: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
        }
    </style>
</head>
<body>

    <?php
    $basePayValue = '';
    $allowancesValue = '';
    $loansValue = '';
    $statusValue = '';

    if (isset($_POST["calculate-btn"])) {
        $basePayValue = isset($_POST["base-pay"]) ? htmlspecialchars($_POST["base-pay"]) : '';
        $allowancesValue = isset($_POST["allowances"]) ? htmlspecialchars($_POST["allowances"]) : '';
        $loansValue = isset($_POST["loans"]) ? htmlspecialchars($_POST["loans"]) : '';
        $statusValue = isset($_POST["status"]) ? htmlspecialchars($_POST["status"]) : '';
    }
    ?>

    <form class="card" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <h3>Net Pay Calculator</h3>

        <label for="base-pay">Base Pay:</label>
        <input type="number" name="base-pay" id="base-pay" required step="0.01" min="0" value="<?php echo $basePayValue; ?>">

        <label for="allowances">Allowances:</label>
        <input type="number" name="allowances" id="allowances" required step="0.01" min="0" value="<?php echo $allowancesValue; ?>">

        <label for="loans">Loans:</label>
        <input type="number" name="loans" id="loans" required step="0.01" min="0" placeholder="Enter 0 if none" value="<?php echo $loansValue; ?>">

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="" disabled <?php if ($statusValue === '') echo 'selected'; ?>>-- Select Status --</option>
            <option value="Single" <?php if ($statusValue === 'Single') echo 'selected'; ?>>Single</option>
            <option value="Married" <?php if ($statusValue === 'Married') echo 'selected'; ?>>Married</option>
        </select>
        <input type="submit" value="Calculate" name="calculate-btn" id="calculate-btn">
    </form>

    <?php
    if (isset($_POST["calculate-btn"])) {
        function grossPay($basePay, $allowances): float {
            return $basePay + $allowances;
        }

        function deductions($basePay, $status, $loans): array {
            $philhealth = $basePay * 0.10;
            $sss = $basePay * 0.10;
            $insurance = 500;
            $taxRate = ($status === "Single") ? 0.25 : 0.15;
            $tax = $basePay * $taxRate;
            $total = $philhealth + $sss + $insurance + $loans + $tax;

            return [
                'philhealth' => $philhealth, 'sss' => $sss, 'insurance' => $insurance,
                'loans' => $loans, 'tax' => $tax, 'taxRate' => $taxRate * 100, 'total' => $total
            ];
        }

        function netPay($gross, $deductions): float {
            return $gross - $deductions;
        }

        $basePay = (float) $_POST["base-pay"];
        $allowances = (float) $_POST["allowances"];
        $status = $_POST["status"];
        $loans = (float) $_POST["loans"];

        $gross = grossPay($basePay, $allowances);
        $deduct = deductions($basePay, $status, $loans);
        $net = netPay($gross, $deduct['total']);

        echo '<div class="card">';
        echo "<h3>Payroll Breakdown</h3>";
        echo "<table>";
        echo '<tr><th>Item</th><th>Amount</th></tr>';
        echo "<tr><td class='highlight'>Base Pay</td><td class='highlight'>₱" . number_format($basePay, 2) . "</td></tr>";
        echo "<tr><td class='highlight'>Allowance</td><td class='highlight'>₱" . number_format($allowances, 2) . "</td></tr>";
        echo "<tr><td class='highlight'>Gross Pay</td><td class='highlight'>₱" . number_format($gross, 2) . "</td></tr>";
        echo "<tr><td>PhilHealth (10%)</td><td>₱" . number_format($deduct['philhealth'], 2) . "</td></tr>";
        echo "<tr><td>SSS (10%)</td><td>₱" . number_format($deduct['sss'], 2) . "</td></tr>";
        echo "<tr><td>Insurance (Fixed)</td><td>₱" . number_format($deduct['insurance'], 2) . "</td></tr>";
        echo "<tr><td>Loan</td><td>₱" . number_format($deduct['loans'], 2) . "</td></tr>";
        echo "<tr><td class='highlight'>Status</td><td class='highlight'>" . $status . "</td></tr>";
        echo "<tr><td>Tax Based on Status (" . $deduct['taxRate'] . "%)</td><td>₱" . number_format($deduct['tax'], 2) . "</td></tr>";
        echo "<tr><td class='highlight'>Total Deductions</td><td class='highlight'>₱" . number_format($deduct['total'], 2) . "</td></tr>";
        echo "<tr><td class='net'>Net Pay</td><td class='net'>₱" . number_format($net, 2) . "</td></tr>";
        echo "</table>";
        echo '</div>';
    } else {
        echo '<div class="card">';
        echo "<h3>Payroll Breakdown</h3>";
        echo '<p class="placeholder-text">Please fill out the form and click "Calculate" to see the results here.</p>';
        echo '</div>';
    }
    ?>
</body>
</html>