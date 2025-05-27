<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Favourite Added</title>
  <style>
    body {
      font-family: 'Times New Roman', sans-serif;
      background-color: #fdf7e3;
      color: #333;
      padding: 40px;
      max-width: 800px;
      margin: auto;
    }
    p {
      font-size: 1.2em;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      color: #4CAF50;
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["dish"])) {
    $dish = htmlspecialchars(strip_tags($_POST["dish"]));
    $file = 'favourites.csv';
    $alreadyExists = false;

    // Read existing favourites
    if (file_exists($file)) {
        $existingFavourites = array_map('str_getcsv', file($file));
        foreach ($existingFavourites as $entry) {
            if (isset($entry[0]) && $entry[0] === $dish) {
                $alreadyExists = true;
                break;
            }
        }
    }

    if ($alreadyExists) {
        echo "<p>ℹ️ <strong>$dish</strong> is already in your favourites.</p>";
    } else {
        $handle = fopen($file, 'a');
        if ($handle) {
            fputcsv($handle, [$dish]);
            fclose($handle);
            echo "<p>✅ Added to favourites: <strong>$dish</strong></p>";
        } else {
            echo "<p>❌ Could not open file for writing.</p>";
        }
    }
} else {
    echo "<p>⚠️ No dish received or invalid method.</p>";
}
?>
  <p><a href="dishdetails.html">⬅ Back to Menu</a></p>
</body>
</html>

