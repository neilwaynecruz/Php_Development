<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        
        /*

        date(format,timestamp)

        GET DATE

        d - Represents the day of the month (01 to 31)
        m - Represents a month (01 to 12)
        Y - Represents a year (in four digits)
        l (lowercase 'L') - Represents the day of the week

        D - Represents a textual representation of a day, three letters (Mon through Sun)
        M - Represents a short textual representation of a month, three letters (Jan through Dec)
        y - Represents a year (in two digits)
        L - Whether it's a leap year (1 if it is a leap year, 0 if not)
        */

         
        echo "Today is " . date("Y/m/d") . "<br>";
        echo "Today is " . date("Y.m.d") . "<br>";
        echo "Today is " . date("Y-m-d") . "<br>";
        echo "Today is " . date("l") . "<br><br>";

        /*

        GET A TIME

        H - 24-hour format of an hour (00 to 23)
        h - 12-hour format of an hour with leading zeros (01 to 12)
        i - Minutes with leading zeros (00 to 59)
        s - Seconds with leading zeros (00 to 59)
        a - lowercase Ante meridiem and Post meridiem (am or pm)
        A - UPPERCASE Ante meridiem and Post meridiem (am or pm)

        */

               echo "The time is " . date("H:i:s A") . "<br><br>";
        
        /*
        GET YOUR TIMEZONE
        */

        date_default_timezone_set("Asia/Manila");
        echo "The time is " . date("h:i:s a") . "<br><br>";


        /*

        GET YOUR TIMEZONE

        mktime(hour, minute, second, month, day, year)

        */

        $d=mktime(11, 14, 54, 8, 12, 2014);
        echo "Created date is " . date("Y-m-d h:i:sa", $d) . "<br><br>";


         /*

        Create a Date From a String With strtotime()

        strtotime(time, now)

        */

        $d = strtotime("6:30:10pm May 14 2025");
        echo "Created date is " . date("Y-m-d h:i:sa", $d) . "<br><br>";

        $tom=strtotime("tomorrow");
        echo date("Y-m-d h:i:sa", $tom) . "<br>";

        $sat=strtotime("next Saturday");
        echo date("Y-m-d h:i:sa", $sat) . "<br>";

        $next=strtotime("+3 Months");
        echo date("Y-m-d h:i:sa", $next) . "<br><br>";
        

        $startdate = strtotime("Today");
        $enddate = strtotime("+6 weeks", $startdate);

        while($startdate < $enddate){
            echo date("M-d", $startdate) . "<br>";
            $startdate = strtotime("+1 week", $startdate);
        }
    ?>

    <footer>
       <br><br> © 2010-<?php echo date("Y");?>
    </footer>
</body>
</html>