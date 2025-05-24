<?php
include 'db_connection.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives</title>
    <style>
        body {
            background-color: #333; /* Darker background color for the page */
            color: #fff; /* Light text color */
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }

        main {
            background-color: #222; /* Darker background color for the form */
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
        }

        h1 {
            color: #fff; /* Light text color for header */
        }

        label {
            display: block;
            color: #fff;
            text-align: left;
            margin-top: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: none;
            margin: 5px 0;
            border-radius: 5px;
            box-sizing: border-box; /* Include padding and border in width and height */
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-label {
            color: #fff;
            margin-left: 10px; /* Add some spacing between checkbox and text */
        }

        .checkbox-label input {
            margin-right: 5px; /* Add spacing between checkbox and text */
        }

        button {
            background-color: #FF4500; /* Fiery orange-red button background color */
            color: #fff; /* Light text color for buttons */
            padding: 10px;
            border: none;
            cursor: pointer;
            margin: 10px 0;
            border-radius: 5px;
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

        <div class="checkbox-container">
            <label class="checkbox-label">
                <input type="checkbox" id="remember_me" name="remember_me" value="1">
                Recuerda Me (Por 30 dias)
            </label>
        </div>

        <button type="submit">Log In</button>

        <footer>Not a member yet? Cringe. Hop on: <a href="register.php">Sign Up</a></footer>
    </form>
</main>
</body>
</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            exit("Your account is set up but not yet approved by D-Dawg. DM me with your username or email and I'll approve you when I can.<br>");
        }
    }
}
?>
