<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Main Page</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/index.css">
        <link rel="stylesheet" href="../css/contact.css">
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    
    <img class="general_img" src="../assets/Mail_Img.png">
    <h1>Email confirmation page.</h1>

        <?php
// I may or may not have spent 3 hours troubleshooting mailhog on linux beofre it decided to work. YAY!
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        die("All fields are required");
    }
    
    // Build email
    $email_body = "Name: $name\nEmail: $email\n\n$message";
    $email_subject = match($subject) {
        'booking' => 'Booking Request',
        'query' => 'General Inquiry',
        'complaint' => 'Complaint',
        default => 'Website Contact'
    };
    
    // Send using direct command
    $cmd = '/usr/local/bin/mhsendmail --smtp-addr=127.0.0.1:1025 test@mailhog.local <<EOF';
    $cmd .= "\nFrom: $email";
    $cmd .= "\nSubject: $email_subject";
    $cmd .= "\n\n$email_body";
    $cmd .= "\nEOF";
    
    $result = shell_exec($cmd." 2>&1");
    
    if (empty($result)) {
        echo "<h2>Success!</h2><p>Message sent. <a href='http://localhost:8025'>View in MailHog</a></p>";
        echo "<a href='../index.html'>Return to main page.</a>";
    } else {
        echo "<h2>Error</h2><pre>".htmlspecialchars($result)."</pre>";
    }
} else {
    header("Location: index.html");
}
?>

        <script src="" async defer></script>
    </body>
</html>