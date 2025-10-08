<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RegEx</title>
</head>
<body>
    
    <?php
    // Regular Expression Modifiers
    // i - case insensitive
    // m - multi line
    // s - dotall, includes new line characters
    // x - ignore whitespace and comments in pattern
    // A - anchors pattern to start of string

    //Regular Expression Patterns
    // [] - a range of characters
    // \d - a digit (0-9)
    // \D - a non-digit (0-9)
    // \w - a word character (a-z, A-Z, 0-9, _)
    // \W - a non-word character (a-z, A-Z, 0-9, _)
    // \s - a whitespace character (space, tab, newline)
    // \S - a non-whitespace character (space, tab, newline)
    // . - any character except newline

    // Using preg_match() - returns 1 if pattern matches, 0 if not
    $string = "The quick brown fox";
    $pattern = "/quick/i"; // case insensitive search for "quick"
    if (preg_match($pattern, $string)) {
        echo "Match found!";
    } else {
        echo "No match found.";
    }

    // preg_match_all() - returns all matches in an array
    $string2 = "The rain in Spain stays mainly in the plain.";
    $pattern2 = "/ain/i"; // case insensitive search for "ain"
    if (preg_match_all($pattern2, $string2)) {
        echo "<br>Match found!";
    } else {
        echo "<br>No match found.";
    }

    // using preg_replace() - replaces matched patterns with a replacement string
    $string3 = "The quick brown fox jumps over the lazy dog.";
    $pattern3 = "/fox/i"; // case insensitive search for "fox"
    $replacement = "cat";
    $newString = preg_replace($pattern3, $replacement, $string3); 
    echo "<br>" . $newString; // Output: The quick brown cat jumps over the lazy dog.

    ?>
</body>
</html>