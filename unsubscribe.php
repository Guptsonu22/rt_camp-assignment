<?php
require 'functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();

        $codes = file_exists('unsubscribe_codes.json') ? json_decode(file_get_contents('unsubscribe_codes.json'), true) : [];
        $codes[$email] = $code;
        file_put_contents('unsubscribe_codes.json', json_encode($codes));

        sendUnsubscribeVerificationEmail($email, $code);
        echo '<p>Unsubscribe verification code sent to your email.</p>';
    }

    if (isset($_POST['unsubscribe_verification_code'])) {
        $inputCode = $_POST['unsubscribe_verification_code'];
        $codes = file_exists('unsubscribe_codes.json') ? json_decode(file_get_contents('unsubscribe_codes.json'), true) : [];

        foreach ($codes as $email => $code) {
            if ($inputCode == $code) {
                unsubscribeEmail($email);
                unset($codes[$email]);
                file_put_contents('unsubscribe_codes.json', json_encode($codes));
                echo '<p>Email unsubscribed successfully.</p>';
                break;
            }
        }
    }
}
?>

<h2>Unsubscribe from GitHub Updates</h2>
<form method="POST">
    <input type="email" name="unsubscribe_email" required>
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<h2>Enter Unsubscribe Verification Code</h2>
<form method="POST">
    <input type="text" name="unsubscribe_verification_code">
    <button id="verify-unsubscribe">Verify</button>
</form>
