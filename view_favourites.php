<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favourite Dishes</title>
    <style>
        body {
            font-family: 'Times New Roman', sans-serif;
            background-color: #fdf7e3;
            color: #333;
            padding: 40px;
            max-width: 800px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .favourites-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .favourite-card {
            background-color: #fff8e1;
            border: 1px solid #f4b400;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            font-size: 18px;
            position: relative;
        }

        .dish-name {
            font-weight: bold;
        }

        .remove-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            float: right;
        }

        .remove-btn:hover {
            background-color: #c9302c;
        }

        .empty-msg {
            font-style: italic;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }

        form.email-form {
            margin-top: 30px;
            text-align: center;
        }

        form.email-form input[type="email"] {
            padding: 8px;
            width: 60%;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        form.email-form button {
            padding: 8px 16px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form.email-form button:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <h1>My Favourite Dishes</h1>

    <?php
    $filename = "favourites.csv";
    $favourites = [];

    if (file_exists($filename)) {
        $favourites = file($filename, FILE_IGNORE_NEW_LINES);
    }

    // Handle email sending
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_email"])) {
        $to = $_POST["email"];
        $subject = "Your Favourite Dishes";
        $body = "Here are your selected favourite dishes:\n\n";
        $body .= implode("\n", $favourites);
        $headers = "From: no-reply@restaurant.com";

        if (mail($to, $subject, $body, $headers)) {
            echo "<p style='color: green;'>Email sent successfully to $to!</p>";
        } else {
            echo "<p style='color: red;'>Failed to send email. Showing simulated message instead:</p>";
            echo "<pre>$body</pre>";
        }
    }

    // Handle dish removal
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_dish"])) {
        $dishToRemove = $_POST["remove_dish"];
        $favourites = file($filename, FILE_IGNORE_NEW_LINES);
        $favourites = array_filter($favourites, fn($dish) => $dish !== $dishToRemove);
        file_put_contents($filename, implode(PHP_EOL, $favourites));
        header("Location: view_favourites.php");
        exit();
    }
    ?>

    <div class="favourites-container">
        <?php
        if (!empty($favourites)) {
            foreach ($favourites as $dish) {
                echo "<div class='favourite-card'>
                        <span class='dish-name'>" . htmlspecialchars($dish) . "</span>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='remove_dish' value='" . htmlspecialchars($dish) . "'>
                            <button type='submit' class='remove-btn'>Remove</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p class='empty-msg'>No favourites added yet.</p>";
        }
        ?>
    </div>

    <?php if (!empty($favourites)): ?>
        <form method="POST" class="email-form">
            <h2>Send Your Favourite Dishes</h2>
            <label for="email">Enter your email address:</label><br><br>
            <input type="email" name="email" id="email" required>
            <button type="submit" name="send_email">Send List</button>
        </form>
    <?php endif; ?>
</body>
</html>
