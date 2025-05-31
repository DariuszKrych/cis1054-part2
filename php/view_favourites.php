<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>My Favourite Dishes</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/view_favourites.css">
        <link rel="stylesheet" href="../css/index.css">
    </head>
    <body>
    <img class="fav_img" src="../assets/Mail_Img.png">
    <h1>My Favourite Dishes</h1>

    <?php
    // Fixed file path
    $filename = __DIR__ . '/../data/favourites.csv';
    $favourites = [];

    if (file_exists($filename)) {
        $favourites = file($filename, FILE_IGNORE_NEW_LINES);
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

    // Handle email sending
    $emailResult = '';
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_email"])) {
        $to = $_POST["email"];
        $subject = "Your Favourite Dishes";
        $body = "Here are your selected favourite dishes:\n\n";
        $body .= implode("\n", $favourites);
        
        // Build the email using heredoc syntax
        $emailContent = "To: $to\n";
        $emailContent .= "Subject: $subject\n";
        $emailContent .= "From: no-reply@restaurant.com\n";
        $emailContent .= "Content-Type: text/plain; charset=utf-8\n\n";
        $emailContent .= $body;
        
        // Build mhsendmail command
        $cmd = '/usr/local/bin/mhsendmail --smtp-addr=127.0.0.1:1025 ' . escapeshellarg($to) . ' <<EOF' . "\n";
        $cmd .= $emailContent;
        $cmd .= "\nEOF";
        
        // Execute command and capture output
        $result = shell_exec($cmd . " 2>&1");
        
        if (empty($result)) {
            $emailResult = "<p style='color: green;'>Email sent successfully to $to!</p>";
        } else {
            $emailResult = "<p style='color: red;'>Failed to send email: " . htmlspecialchars($result) . "</p>";
        }
    }
    ?>
    
    <?php echo $emailResult; ?>
    
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

    <p><a href="../index.html">⬅ Back to Main Page</a></p>
</body>
</html>