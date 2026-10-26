<?php
/**
 * Apex Automation - newsletter subscription handler.
 * Expects a POST request from the .php-email-form component.
 * Returns "OK" on success so the front-end can show its confirmation state.
 */

header('Content-Type: text/plain; charset=utf-8');

// Set your receiving address here.
$receiving_email_address = 'hello@apexautomation.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed.';
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $email);

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 180) {
    echo 'Please enter a valid email address.';
    exit;
}

$subject = 'Newsletter subscription: ' . $email;
$body = "A new subscriber joined the Apex Automation newsletter.\n\nEmail: {$email}\n";

$headers = array(
    'From: Apex Automation Website <' . $receiving_email_address . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
);

if (mail($receiving_email_address, $subject, $body, implode("\r\n", $headers))) {
    echo 'OK';
} else {
    echo 'The subscription could not be completed right now. Please try again later.';
}
