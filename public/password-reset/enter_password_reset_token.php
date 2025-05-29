<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/password-reset/send_email.php';
    require $_SERVER["DOCUMENT_ROOT"] . "/password-reset/helpers.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/scripts/helpers.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recipient = $_POST["email"];
        $resetToken = getToken(6);

        // need to get user id from email
        // code from the code
        // expiration date set to 30 minutes from now
        require $_SERVER["DOCUMENT_ROOT"] . "/scripts/db_connection.php";
        
        $stmt = $mysqli->prepare("INSERT INTO password_reset (user_id, code, expire) VALUES (?, ?, NOW() + INTERVAL 30 MINUTE)");
        
        $userId = userEmailToId($mysqli, $recipient);
        $stmt->bind_param("is", $userId, $resetToken);
        $stmt->execute();

        send_email($recipient, $resetToken);
    }
?>

<html>
<head>
    <title>The Archives</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
    <main>
        <form action="reset_password.php" method="post">
            <h1>Reset Password</h1>

            <div>
                <label for="token">Password Reset Token:</label>
                <input type="text" name="token" id="token" required>
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </main>
</body>
</html>