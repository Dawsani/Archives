<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        label {
            display: block;
            color: #fff;
            text-align: left;
            margin-top: 10px;
        }

        footer {
            color: #fff;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<main>
    <form action="login.php" method="post">
        <h1>Log In</h1>

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>


        <input type="checkbox" id="remember_me" name="remember_me" value="1">Remember Me</input><br>


        <button type="submit">Log In</button>

        <footer>Not a member yet? <a href="register.php">Sign Up</a></footer>
        <footer><a href="password-reset/forgot_password.php">Forgot Password</a></footer>

    </form>
</main>
</body>
</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';

    // Retrieve data from the form
    $email = $_POST["email"];
    $password = $_POST["password"];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $login_valid = 1;

    // Get the stored password for the user, if any
    $sql = "SELECT password FROM user WHERE email = '$email'";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row["password"];
    }
    else {
        exit("User not found.<br>");
    }

    // Check to see if the username and hashed password match the user in the db
    if (!password_verify($password, $storedPassword)) {
        //echo "DANGEROUS DEBUG:<br>hp: " . $hashed_password . "<br>sp: " . $storedPassword . "<br>";
        exit("Email/Password are incorrect.<br>");
    }

    // make sure the user is approved
    $sql = "SELECT is_approved FROM user WHERE email = '$email';";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $approved = $row["is_approved"];
        if ($approved == 1) {

            // Create the session, get them to index
            session_start();
            $_SESSION['username'] = $username;

            // Check if they checked remember me
            if (isset($_POST['remember_me']) && $_POST['remember_me'] == 1) {
                $token = bin2hex(random_bytes(16)); // Generate a random token
                $expiration = time() + 30 * 24 * 60 * 60; // 30 days in seconds
                setcookie('remember_me_token', $token, $expiration, '/');
            }

            Header("Location: .");
        }
        else {
            exit("Your account is set up but not yet approved by Dawson. DM me with your username or email and I'll approve you when I can.<br>");
        }
    }
}
?>
