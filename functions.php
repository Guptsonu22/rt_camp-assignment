<?php

function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== $email);
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: no-reply@example.com' . "\r\n";
    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    // Simulated GitHub timeline for testing — replace with actual fetch if needed
    $data = [
        ['event' => 'Push', 'user' => 'testuser'],
        ['event' => 'Fork', 'user' => 'anotheruser'],
    ];
    return $data;
}

function formatGitHubData($data) {
    $html = '<h2>GitHub Timeline Updates</h2>';
    $html .= '<table border="1"><tr><th>Event</th><th>User</th></tr>';
    foreach ($data as $item) {
        $html .= '<tr><td>' . htmlspecialchars($item['event']) . '</td><td>' . htmlspecialchars($item['user']) . '</td></tr>';
    }
    $html .= '</table>';
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $timelineData = fetchGitHubTimeline();
    $formattedData = formatGitHubData($timelineData);

    foreach ($emails as $email) {
        $unsubscribeUrl = 'http://localhost:8000/unsubscribe.php?email=' . urlencode($email);
        $message = $formattedData;
        $message .= '<p><a href="' . $unsubscribeUrl . '" id="unsubscribe-button">Unsubscribe</a></p>';

        $subject = "Latest GitHub Updates";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-reply@example.com' . "\r\n";

        mail($email, $subject, $message, $headers);
    }
}

?>
