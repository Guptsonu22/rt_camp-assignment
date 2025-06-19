<?php
require_once 'functions.php';

$successMessage = "";
$errorMessage = "";
$email = "";

$codesFile = 'verification_codes.json';
$codes = file_exists($codesFile) ? json_decode(file_get_contents($codesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        $codes[$email] = $code;
        file_put_contents($codesFile, json_encode($codes));
        sendVerificationEmail($email, $code);
        $successMessage = "Verification code sent to $email";
    } elseif (isset($_POST['verification_code'])) {
        $email = trim($_POST['email_hidden']);
        $code = trim($_POST['verification_code']);
        $storedCode = $codes[$email] ?? null;

        if ($code === $storedCode) {
            registerEmail($email);
            unset($codes[$email]);
            file_put_contents($codesFile, json_encode($codes));
            $successMessage = "🎉 Registered Successfully!";
        } else {
            $errorMessage = "❌ Incorrect verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GitHub Timeline Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 40px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            margin-top: 12px;
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 16px;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
        }
        .success {
            background-color: #e6ffe6;
            color: #2e7d32;
        }
        .error {
            background-color: #ffe6e6;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Subscribe for GitHub Timeline Updates</h2>
        
        <?php if ($successMessage): ?>
            <div class="message success"><?= $successMessage ?></div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php endif; ?>

        <!-- Email Input Form -->
        <form method="POST">
            <label for="email">Enter your Email:</label>
            <input type="email" name="email" required>
            <button id="submit-email" type="submit">Submit</button>
        </form>

        <br>

        <!-- Verification Code Form -->
        <form method="POST">
            <label for="verification_code">Enter Verification Code:</label>
            <input type="text" name="verification_code" maxlength="6" required>
            <input type="hidden" name="email_hidden" value="<?= htmlspecialchars($email) ?>">
            <button id="submit-verification" type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
