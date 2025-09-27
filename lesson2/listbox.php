<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>listbox</title>
</head>
<body>
<?php
// Define the list of names with keys
$names = array(
    '1' => 'Martha',
    '2' => 'Mary',
    '3' => 'Lazarus'
);

// Get the selected name from POST if submitted, otherwise empty
$selected = $_POST['selNames'] ?? '';
?>

<!-- Form starts here -->
<form method="post">
    <label for="selNames">Select name:</label>
    <select name="selNames" id="selNames">
        <option value="">-- Select name --</option>
        <?php foreach ($names as $key => $name): ?>
            <!-- Keep the selection after form submission -->
            <option value="<?= htmlspecialchars($key) ?>" <?= ($selected == $key) ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <input type="submit" name="click" value="Submit">
    <br><br>
</form>

<?php
// Display the selected value after form submission
if (isset($_POST['click'])) {
    if (!empty($selected) && isset($names[$selected])) {
        echo "Selected name: " . htmlspecialchars($names[$selected]);
    } else {
        echo "Please select a valid name.";
    }
}
?>

    
</body>
</html>