<html>
<head>
    <title>The Archives</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
    <main>
        <form action="enter_password_reset_token.php" method="post">
            <h1>Reset Password</h1>

            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <button type="submit">Send Password Reset Link</button>
        </form>
    </main>
</body>
</html>