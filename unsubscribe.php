<?php
require_once 'functions.php';
// TODO: Implement the form and logic for email unsubscription.
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();
        $_SESSION['code'] = $code;
        $_SESSION['unsubscribe_email'] = $email;
        if (sendVerificationEmail($email, $code)) {
            $message = "Unsubscribe verification code sent successfully. Please check your inbox.";
        } else {
            $message = "Failed to send unsubscribe verification code. Please try again or check your email address.";
        }
    }

    if (isset($_POST['verification_code'])) {
        $email = $_SESSION['unsubscribe_email'] ?? '';
        $code = $_POST['verification_code'];

        if (verifyCode($email, $code)) {
            if (unsubscribeEmail($email)) {
                $message = "Your email has been successfully verified and unsubscribed from our mailing list.";
            } else {
                $message = "Your email was successfully verified, but it does not appear to be subscribed to our mailing list.";
            }
        } else {
            $message = "The verification code you entered is invalid. Please check and try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Unsubscribe from XKCD Comics</h1>
    <div class="outer-div">
        <form method="post">
            <label for="unsubscribe-email">
                <strong>We're sorry to see you go!</strong><br>
                Enter your email address to unsubscribe from daily XKCD comics.
            </label>
            <input type="email" name="unsubscribe_email" placeholder="Enter Your Email" required>
            <button id="submit-unsubscribe">Unsubscribe</button>
        </form>

        <form method="post">
            <label for="verification_code">
                <strong>Confirm Unsubscription</strong><br>
                Enter the 6-digit code sent to your email to Unsubscribe
            </label>
            <input type="text" name="verification_code" maxlength="6" placeholder="Enter Unsubscription Code" required>
            <button id="submit-verification">Verify</button>
        </form>

        <?php if ($message) echo "<p>$message</p>"; ?>
    </div>
</body>

</html>