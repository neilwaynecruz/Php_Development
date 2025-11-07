
<?php
echo '<form method="post">';
$months = array('1' => 'Martha', 'Mary', 'Lazarus');
// $weekdays = array('1' => "Monayday", '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');


foreach ($months as $month => $value ) {
    // echo $value . "<br>";

    // in form

    // echo ''. $month .' '. $value .''. '<br>';
    echo '<input type="checkbox" name="month[]" value="'. $value .'">'. $value .'<br>';
}

echo '<input type="submit" value="Submit" name="submit">';
echo '</form>';


// display the selected checkbox value
if(isset($_POST['submit']) && isset($_POST['month'])) {
    $checked_months = $_POST['month'];
    foreach($checked_months as $checked) {
        echo 'You have selected: '. $checked .'<br>';
    }
}else{
    // echo ''. $checked .'<br>';
    echo 'Selected name: ';
}

?>