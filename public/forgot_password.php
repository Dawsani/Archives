<?php
include($_SERVER["DOCUMENT_ROOT"] . "/scripts/helpers.php");

$passwordChangeSuccessBlock = <<<HTML
    <div>
        <p>Password changed successfully.</p>
        <a href="/login.php"><button>Return to Login</button></a>
    </div>
HTML;
        
$emailForm = <<<HTML
    <form action="" method="POST">
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <button type="submit">Send Password Reset Link</button>
    </form>
HTML;

function generateTokenForm($email, $error = FALSE) {
    $tokenForm = <<<HTML
        <p>A verification code has been sent to {$email}. Type the code in the space below within the next 30 minutes:</p>
        <form action="" method="POST">
            <div>
                <input type="hidden" name="email" id="email" value="{$email}">
                <label for="token">Password Reset Token:</label>
                <input type="text" name="token" id="token" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    HTML;

    if ($error) {
        $tokenForm .= "<p style='color:red;'>Invalid code</p>";
    }

    return $tokenForm;
}

function generateNewPasswordForm($email, $token, $passwordMissMatchError = FALSE) {

    $newPasswordForm = <<<HTML
        <form action="" method="POST">
            <input type="hidden" name="email" id="email" value="{$email}" reqiored>
            <input type="hidden" name="token" id="token" value="{$token}" required>

            <div>
                <label for="password1">New Password:</label>
                <input type="password" name="password1" id="password1" minlength="3" maxlength="25" required>
            </div>

            <div>
                <label for="password2">Repeat New Password:</label>
                <input type="password" name="password2" id="password2" minlength="3" maxlength="25" required>
            </div>

            <button type="submit">Reset Password</button>
        </form>
    HTML;

    if ($passwordMissMatchError) {
        $newPasswordForm .= "<p style='color:red'>The provided passwords do not match.</p><br>";
    }

    return $newPasswordForm;
}

?>

<html>
<head>
    <title>The Archives</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
    <main>
    <h1>Reset Password</h1>

    <?php
        // if an email isn't set, get the email from the user
        if (!isset($_POST["email"])) {
            echo $emailForm;
        }

        // if there is an email but no code get the code from the user
        else if (isset($_POST["email"]) and !isset($_POST["token"])) {

            // send the email only if its a post (not a refresh)
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                
                $recipient = $_POST["email"];
                $resetToken = getToken(6);
                
                // update the db
                $mysqli = db_connect();
                $stmt = $mysqli->prepare("INSERT INTO password_reset (user_id, code, expire) VALUES (?, ?, NOW() + INTERVAL 30 MINUTE)");
                
                $userId = userEmailToId($mysqli, $recipient);
                $stmt->bind_param("is", $userId, $resetToken);
                $stmt->execute();

                send_email($recipient, $resetToken);
            }

            $email = $_POST["email"];

            $tokenForm = generateTokenForm($email);
            echo $tokenForm;
        }

        // if both the email and token are submitted
        else if (isset($_POST["email"])) {

            $email = $_POST["email"];

            // check if a token was submitted, and if it's right
            if (isset($_POST["token"])) {
                
                $token = $_POST["token"];

                if ((isset($_POST["password1"]) and isset($_POST["password2"]))) {

                    $password1 = $_POST["password1"];
                    $password2 = $_POST["password2"];
                    
                    $input_valid = 1;

                    // make sure the passwords match
                    if ($password1 != $password2) {
                        $input_valid = 0;
                    }

                    // If input is good, make an account
                    if ($input_valid == 1 and $_SERVER["REQUEST_METHOD"] === "POST") {
                        $mysqli = db_connect();
                        $hashed_password = password_hash($password1, PASSWORD_BCRYPT);
                        $stmt = $mysqli->prepare("  UPDATE user 
                                                    SET password = ?
                                                    WHERE id = ?;");

                        $userId = userEmailToId($mysqli, $email);
                        $stmt->bind_param("si", $hashed_password, $userId);
                        $result = $stmt->execute();

                        echo $passwordChangeSuccessBlock;
                    }

                    else {
                        $newPasswordForm = generateNewPasswordForm($email, $token, TRUE);
                        echo $newPasswordForm;
                    }
                }
                else {
                    if (verify_password_reset_token($email, $token)) {
                        // show password reset input
                        $newPasswordForm = generateNewPasswordForm($email, $token, FALSE);
                        echo $newPasswordForm;
                    }
                    else {
                        // show token input and error message
                        $tokenForm = generateTokenForm($email, TRUE);
                        echo $tokenForm;
                    }
                }
            }
            else {
                // show token input
                echo $tokenForm;
            }
        }
    ?>
    </main>
</body>
</html>

