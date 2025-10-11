<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Schedule</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
      text-align: center;
      padding-bottom: 50px;
    }

    h2, h3 {
      color: #800000;
      margin-top: 20px;
    }

    form {
      margin: 20px auto;
      padding: 15px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      width: 300px;
    }

    select, input[type="submit"] {
      padding: 8px 12px;
      margin: 5px;
      font-size: 14px;
    }

    select {
      border: 1px solid #ccc;
      border-radius: 4px;
      width: 150px;
    }

    input[type="submit"] {
      background-color: #800000;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #a00000;
    }

    table {
      border-collapse: collapse;
      width: 55%;
      margin: 20px auto;
      background-color: white;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
      text-align: center;
      font-size: 17px;
    }

    th {
      background-color: #800000;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    tr:hover {
      background-color: #ffeaa7;
    }

  </style>
</head>
<body>
  <?php
  $sections = array(
      "BSIT 3-1" => [
          "Subject Code" => ["COMP 015", "COMP 016", "COMP 017", "COMP 018", "ELEC IT-E1", "GEED 006", "INTE 301"],
          "Description" => ["Fundamentals of Research", "Web Development", "Multimedia", "Database Administration", "IT Elective 1", "Art Appreciation/Pagpapahalaga sa Sining", "Systems Integration and Architecture 1"],
          "Professor" => ["Prof. Lianne Mendoza", "Prof. Carlo Dela Cruz", "Prof. Jessa Villanueva", "Prof. Patrick Reyes", "Prof. Andrea Salazar", "Prof. Mark Bautista", "Prof. Denise Ramos"],
          "Schedule" => ["Monday 8:00AM - 11:00AM", "Tuesday 1:00PM - 5:00PM", "Wednesday 6:00PM - 9:00PM / Friday 6:00PM - 8:00PM", "Thursday 9:00AM - 12:00PM", "Saturday 10:00AM - 3:00PM", "Sunday 8:00AM - 11:00AM", "Friday 2:00PM - 5:00PM"],
          "Students"=> [
            "Male" => [
                "Reyes, Adrian L.",
                "Santos, Miguel C.",
                "Garcia, David F.",
                "Lim, Ethan G.",
                "Tan, Lucas P.",
                "Cruz, Noah A.",
                "Chua, James B.",
                "Lee, William S.",
                "Ocampo, Benjamin D.",
                "Yap, Alexander M.",
            ],
            "Female" => [
                "Garcia, Sophia M.",
                "Cruz, Isabella R.",
                "Ramos, Olivia T.",
                "Mendoza, Ava C.",
                "Sy, Mia E.",
                "Torres, Charlotte N.",
                "Gomez, Amelia J.",
                "Castillo, Harper L.",
                "Villanueva, Evelyn K.",
            ],
          ]
      ],
      "BSIT 3-2" => [
          "Subject Code" => ["COMP 015", "COMP 016", "COMP 017", "COMP 018", "ELEC IT-E1", "GEED 006", "INTE 301"],
          "Description" => ["Fundamentals of Research", "Web Development", "Multimedia", "Database Administration", "IT Elective 1", "Art Appreciation/Pagpapahalaga sa Sining", "Systems Integration and Architecture 1"],
          "Professor" => ["Prof. Rachel Atian Nayre", "Prof. Marilou Novida", "Prof. Sharifa Aira Abirin", "Prof. Noel Gagolinan", "Prof. Sharifa Aira Abirin", "Prof. Alvin Servana", "Prof. Rowinner Bautista"],
          "Schedule" => ["Sunday 7:30AM - 10:30AM", "Saturday 7:30AM - 12:30AM", "Monday 6:00PM - 9:00PM / Thursday 6:00PM - 8:00PM", "Saturday 1:00PM - 6:00PM", "Sunday 11:00AM - 4:00PM", "Friday 6:00PM - 9:00PM", "Wednesday 6:00PM - 9:00PM"],
          "Students"=> [
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
            ],
          ]
      ]
  );
  ?>

  <h2>Select Section</h2>    
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <select name="section">
          <option value="" disabled selected>-- Select Section --</option>
          <?php
              $selectedSection = $_POST["section"] ?? '';
              foreach ($sections as $section => $details) {
                  $isSelected = ($section === $selectedSection) ? "selected" : "";
                  echo "<option value='$section' $isSelected>$section</option>";
              }
          ?>
      </select>
      <input type="submit" value="Show Schedule" name="show-sched-btn" id="show-sched-btn">
  </form>

  <?php
  if (isset($_POST["show-sched-btn"])) {
      if (empty($_POST["section"])) {
          echo "<h3>Please select a valid section.</h3>";
      } else {
          $selectedSection = $_POST["section"];
          
          echo "<h2>Class Schedule for {$selectedSection}</h2>";
          echo "<table>";
          echo "<tr><th>Subject Code</th><th>Description</th><th>Professor</th><th>Schedule</th></tr>";

          $code = $sections[$selectedSection]["Subject Code"];
          $descriptions = $sections[$selectedSection]["Description"];
          $professors = $sections[$selectedSection]["Professor"];
          $schedules = $sections[$selectedSection]["Schedule"];

          foreach ($code as $index => $subjectCode) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($subjectCode) . "</td>";
              echo "<td>" . htmlspecialchars($descriptions[$index]) . "</td>";
              echo "<td>" . htmlspecialchars($professors[$index]) . "</td>";
              echo "<td>" . htmlspecialchars($schedules[$index]) . "</td>";
              echo "</tr>";
          }
          echo "</table>";

          echo "<h2>Student List</h2>";
          $maleStudents = $sections[$selectedSection]["Students"]["Male"];
          $femaleStudents = $sections[$selectedSection]["Students"]["Female"];
          $maxRows = max(count($maleStudents), count($femaleStudents));

          echo "<table>";
          echo "<tr><th>Male</th><th>Female</th></tr>";

          for ($i = 0; $i < $maxRows; $i++) {
              echo "<tr>";

              $maleName = isset($maleStudents[$i]) ? htmlspecialchars($maleStudents[$i]) : '';
              echo "<td>" . $maleName . "</td>";

              $femaleName = isset($femaleStudents[$i]) ? htmlspecialchars($femaleStudents[$i]) : '';
              echo "<td>" . $femaleName . "</td>";
              echo "</tr>";
          }
          echo "</table>";
      }
  } else {
      echo "<h3>Select a section to view the schedule.</h3>";
  }
  ?>
</body>
</html>