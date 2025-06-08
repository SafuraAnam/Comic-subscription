<?php
require_once 'functions.php';
// TODO: Implement the form and logic for email registration and verification
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        $_SESSION['code'] = $code;
        $_SESSION['email'] = $email;
        if (sendVerificationEmail($email, $code)) {
            $message = "Verification code sent successfully. Please check your inbox.";
        } else {
            $message = "Failed to send verification code. Please try again or check your email address.";
        }
    }

    if (isset($_POST['verification_code'])) {
        $email = $_SESSION['email'] ?? '';
        $code = $_POST['verification_code'];

        if (verifyCode($email, $code)) {
            if (registerEmail($email)) {
                $message = "🎉Email verified successfully! You're now subscribed to receive a new XKCD comic every day. Stay tuned!";
            } else {
                $message = "🔁This email is already registered. You're already subscribed to daily XKCD comics!";
            }
        } else {
            $message = "❌Invalid verification code. Please double-check the code and try again.";
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
    <h1>XKCD Comic Registration</h1>
    <div class="outer-div">
        <form method="post">
            <label for="Subs-email">
                <strong>Love XKCD? Get a new comic every day!</strong><br>
                Enter your email, verify it, and enjoy daily surprises in your inbox.
            </label>

            <input type="email" name="email" placeholder="Enter Your Email" required>
            <button id="submit-email">Submit</button>
        </form>

        <form method="post">
            <label for="subs-code">
                <strong>Comic Security Check!</strong><br>
                Just one step left, pop in the 6-digit code we sent to your inbox.
            </label>
            <input type="text" name="verification_code" maxlength="6" placeholder="Enter Verification Code" required>
            <button id="submit-verification">Verify</button>
        </form>
        <?php if ($message) echo "<p>$message</p>"; ?>
    </div>
</body>

</html>