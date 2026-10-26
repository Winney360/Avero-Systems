<?php
/**
 * Apex Automation - contact form handler.
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

function clean_line($value)
{
    $value = trim((string) $value);
    return str_replace(array("\r", "\n", "%0a", "%0d"), '', $value);
}

$name    = clean_line($_POST['name'] ?? '');
$email   = clean_line($_POST['email'] ?? '');
$subject = clean_line($_POST['subject'] ?? '');
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    echo 'Please complete all required fields.';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Please enter a valid email address.';
    exit;
}

if (strlen($name) > 120 || strlen($email) > 180 || strlen($subject) > 200) {
    echo 'One or more fields are too long.';
    exit;
}

if (strlen($message) > 5000) {
    echo 'Your message is too long. Please keep it under 5,000 characters.';
    exit;
}

$safe_subject = 'Website enquiry: ' . $subject;
$body = "New enquiry from the Apex Automation website\n\n"
      . "Name: {$name}\n"
      . "Email: {$email}\n"
      . "Subject: {$subject}\n\n"
      . "Message:\n{$message}\n";

$headers = array(
    'From: Apex Automation Website <' . $receiving_email_address . '>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
);

if (mail($receiving_email_address, $safe_subject, $body, implode("\r\n", $headers))) {
    echo 'OK';
} else {
    echo 'The message could not be sent right now. Please email us directly at ' . $receiving_email_address . '.';
}
