<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string
{
  // TODO: Implement this function
  return strval(rand(100000, 999999));
}

// Mentioned in the readme file to implement this function inside functions.php
function verifyCode(string $email, string $code): bool
{
  return isset($_SESSION['code']) &&
    ($email === ($_SESSION['email'] ?? $_SESSION['unsubscribe_email'] ?? '')) &&
    $_SESSION['code'] === $code;
}


/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool
{
  if ($email === ($_SESSION['unsubscribe_email'] ?? '')) {
    $subject = "Confirm Un-subscription";
    $message = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
  } else if ($email === ($_SESSION['email'] ?? '')) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
  } else {
    return false; // Unknown email — neither register nor unsubscribe
  }

  $headers = "From: no-reply@example.com\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8\r\n";

  return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool
{
  $file = __DIR__ . '/registered_emails.txt';
  // TODO: Implement this function
  if (file_exists($file)) {
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (in_array($email, $emails)) {
      return false; // Already registered
    }
  }

  return file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}


/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool
{
  $file = __DIR__ . '/registered_emails.txt';
  // TODO: Implement this function
  if (!file_exists($file)) return false;

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!in_array($email, $emails)) return false;

  $updatedEmails = array_filter($emails, fn($e) => trim($e) !== $email);
  return file_put_contents($file, implode(PHP_EOL, $updatedEmails) . PHP_EOL, LOCK_EX) !== false;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string
{
  // TODO: Implement this function
  $latestComicData = file_get_contents('https://xkcd.com/info.0.json');
  $latest = json_decode($latestComicData, true);
  $maxId = $latest['num'];

  // Get a random comic ID (between 1 and max ID)
  $randomId = rand(1, $maxId);

  // Fetch random comic data
  $randomComicData = file_get_contents("https://xkcd.com/$randomId/info.0.json");
  $comic = json_decode($randomComicData, true);

  // Return formatted HTML with a placeholder for email
  return <<<HTML
<h2>XKCD Comic</h2>
<img src="{$comic['img']}" alt="XKCD Comic">
<p><a href="http://localhost:3000/xkcd-SafuraAnam/src/unsubscribe.php?email={{email}}&confirm=true" id="unsubscribe-button">Unsubscribe</a></p>
HTML;
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void
{
  $file = __DIR__ . '/registered_emails.txt';
  // TODO: Implement this function
  if (!file_exists($file)) {
    return;
  }

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$emails) return;

  $formattedHTML = fetchAndFormatXKCDData();

  foreach ($emails as $email) {
    $emailHTML = str_replace('{{email}}', urlencode($email), $formattedHTML);
    $subject = "Your XKCD Comic";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";


    mail($email, $subject, $emailHTML, $headers);
  }
}
