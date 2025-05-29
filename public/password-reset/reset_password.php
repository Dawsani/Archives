<?php
    // verify via the code that this is valid
    require $_SERVER["DOCUMENT_ROOT"] . '/password-reset/send_email.php';
    require $_SERVER["DOCUMENT_ROOT"] . "/password-reset/helpers.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/scripts/helpers.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $token = $_POST["token"];

        require $_SERVER["DOCUMENT_ROOT"] . "/scripts/db_connection.php";
        
        $stmt = $mysqli->prepare("  SELECT user_id
                                    FROM password_reset
                                    WHERE code = ? AND expire > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // check if this is a valid token
        if ($result->num_rows === 0) {
            error_log("Invalid token provided");
            header('Location: /password-reset/enter_password_reset_token.php');
        }

        $stmt = $mysqli->prepare("DELETE FROM password_reset WHERE code = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
    }
?>


<html>
<head>
    <title>The Archives</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
    <main>
        <form action="submit_new_password.php" method="post">
            <h1>Reset Password</h1>

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
    </main>
</body>
</html>