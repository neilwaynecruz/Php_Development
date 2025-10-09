<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arrays</title>
    <style>
        body {
            font-family: "Poppins", Arial, sans-serif;
            background-color: #fdf7f7;
            color: #2c2c2c;
            margin: 0;
            padding: 0;
            padding-bottom: 70px;
        }

        h1 {
            text-align: center;
            background-color: #800000;
            color: #fff;
            padding: 15px 0;
            margin-bottom: 30px;
        }

        h3 {
            color: #800000;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
            margin-bottom: 10px;
            transition: all 0.3s ease-in-out;
        }

        h3:hover {
            transform: scale(1.2);
        }

        .container {
            width: 85%;
            max-width: 900px;
            background: #ffffff;
            border: 2px solid #800000;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(128, 0, 0, 0.1);
            padding: 25px 40px;
            margin: 20px auto;
        }

        ul, ol {
            padding-left: 25px;
            line-height: 1.9;
        }

        li {
            margin-bottom: 3px;
            transition: all 0.15s ease-in-out;
        }

        li:hover {
            color: #bf0404ff;
            transform: scale3d(1.03, 1.03, 1);
        }

        .section {
            margin-bottom: 30px;
        }

        .sched-box {
            background-color: #fff9e6;
            border: 1px solid #d4af37;
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 12px;
            line-height: 1.8;
            transition: all 0.2s ease-in-out;
        }

        .sched-box:hover {
            transform: scale(1.03);
            background-color: #ffffffff;
            color: rgba(7, 66, 192, 1);
            box-shadow: 0 3px 3px 1px rgba(0, 0, 0, 0.1);
        }

        .sched-box b {
            color: #800000;
        }
    </style>
</head>
<body>
    <h1>Array</h1>

    <div class="container">
        <?php
        // for subjects
        $listOfSubject = array(
            "Fundamentals of Research",
            "Web Development",
            "Multimedia",
            "Database Administration",
            "IT Elective 1",
            "Art Appreciation/Pagpapahalaga sa Sining",
            "Systems Integration and Architecture 1",
        );

        // for students
        $listOfStudents = array(
            "Male" => [
                "Almario, Paul Joshua V.",
                "Andrade, Justine Cholo",
                "Bacaoco, Marvin G.",
                "Balili, Juan Carlo P.",
                "Bernardo, Mark Jhaztine V.",
                "Bontigao, Felix Christian B.",
                "Cabreza, Jay-r P.",
                "Centeno, Brian Gail A.",
                "Colita, Christian O.",
                "Cruz, Neil Wayne T.",
                "Jarlego, Angelo Rabi",
                "Lubao, Christian Louie R.",
                "Morales, Virgilio M.",
                "Moralla, Jeffrey",
                "Pedregal, Joseph Rainer N.",
                "Riano, French Jame A.",
                "Rola, Charles Gabriel J.",
                "Rola, Charlie Magne J.",
                "Salabsab Jr., Gabriel S.",
                "Samorin, John Hector",
                "Santos, Jenero F.",
            ],
            "Female" => [
                "Abayan, Ashley Nicole DC.",
                "Andres, Junella Mae R.",
                "Benitez, Hayden Norleen P.",
                "Camagos, Winona Shanley N.",
                "Cequena, NR-Heart Aman R.",
                "Pardilla, Jannie Ruth S.",
                "Rapsing, Giselle Mae",
                "Sarturio, Shanley Rheyela Joy H.",
            ]
        );

        $classSched = array(
            [
                "subject_code" => "COMP 015",
                "description" => "Fundamentals of Research",
                "day/time" => "Sunday 07:30AM - 10:30AM"
            ],
            [
                "subject_code" => "COMP 016",
                "description" => "Web Development",
                "day/time" => "Saturday 07:30AM - 12:30PM"
            ],
            [
                "subject_code" => "COMP 017",
                "description" => "Multimedia",
                "day/time" => "Monday/Thursday 06:00PM - 09:00PM / 06:00PM - 08:00PM"
            ],
            [
                "subject_code" => "COMP 018",
                "description" => "Database Administration",
                "day/time" => "Saturday/Sunday 01:00PM - 03:00PM / 03:00PM - 06:00PM"
            ],
            [
                "subject_code" => "ELEC IT-E1",
                "description" => "IT Elective 1",
                "day/time" => "Sunday 11:00AM - 04:00PM"
            ],
            [
                "subject_code" => "GEED 006",
                "description" => "Art Appreciation / Pagpapahalaga sa Sining",
                "day/time" => "Friday 06:00PM - 09:00PM"
            ],
            [
                "subject_code" => "INTE 301",
                "description" => "Systems Integration and Architecture 1",
                "day/time" => "Wednesday 06:00PM - 09:00PM"
            ],
        );

        // Subject List
        echo "<div class='section'>";
        echo "<h3>📚 List of Subjects</h3>";
        echo "<ul>";
        foreach ($listOfSubject as $subs) {
            echo "<li>{$subs}</li>";
        }
        echo "</ul></div>";

        // Student List
        echo "<div class='section'>";
        foreach ($listOfStudents as $gender => $students) {
            echo $gender === "Male" ? "<h3>👨‍🎓 {$gender} Students</h3>" : "<h3>👩‍🎓 {$gender} Students</h3>";
            echo "<ol>";
            foreach ($students as $student) {
                echo "<li>{$student}</li>";
            }
            echo "</ol>";
        }
        echo "</div>";

        // Class Schedule
        echo "<div class='section'>";
        echo "<h3>📅 Class Schedule</h3>";
        foreach ($classSched as $schedList) {
            echo "<div class='sched-box'>";
            foreach ($schedList as $sched => $list) {
                echo "<b>" . ucfirst($sched) . ":</b> {$list}<br>";
            }
            echo "</div>";
        }
        echo "</div>";
        ?>
    </div>
</body>
</html>
