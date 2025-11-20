<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Filter</title>
</head>
<body>
    
    <?php
        $int = 122;
        $min = 1;
        $max = 200;


        if (empty(filter_var($int, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max))))) {
        echo("$int is not within the legal range");
        } else {
        echo("$int is within the legal range");
        }


        echo "<br><br>";

        $ip = "2001:0db8:85a3:08d3:1319:8a2e:0370:7334";

        if (!empty(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))) {
        echo("$ip is a valid IPv6 address");
        } else {
        echo("$ip is not a valid IPv6 address");
        }
    ?>

</body>
</html>