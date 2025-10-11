<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Pay Calculator</title>
    <style>
        body {
            display: flex;
            flex-direction: row;
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
            padding-right: 40px;
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
            transition: all 0.18s ease-in-out;
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

        select:hover, select:focus {
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

        <label for="loans">Loans:</label>
        <input type="number" name="loans" id="loans" required step="0.1" min="0" placeholder="leave it 0 if none">

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="">-- Select Status --</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
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
                'philhealth' => $philhealth,
                'sss' => $sss,
                'insurance' => $insurance,
                'loans' => $loans,
                'tax' => $tax,
                'taxRate' => $taxRate * 100,
                'total' => $total
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

        echo '<div class="result-box">';
        echo "<h3>Payroll Breakdown</h3>";
        echo "<table>";
        echo '<tr><th>Item</th><th style="padding-left:16%;">Amount</th></tr>';
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
    }
    ?>
</body>
</html>
