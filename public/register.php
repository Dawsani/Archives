<?php
include 'db_connection.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Sign Up</title>
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
    <form action="register.php" method="post">
        <h1>Sign Up</h1>

        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" minlength="3" maxlength="25" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" minlength="8" maxlength="64" required>
        </div>

        <div>
            <label for="password2">Password Again:</label>
            <input type="password" name="password2" id="password2" minlength="8" maxlength="64" required>
        </div>

        <button type="submit">Register</button>

        <footer>Already a member? <a href="login.php">Login here</a></footer>
    </form>
</main>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    $input_valid = 1;

    // check if the username is in use
    $sql = "SELECT * FROM user WHERE username = '$username';";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $input_valid = 0;
        echo "The username '$username' is already in use.<br>";
    }

    // check if the email is in use
    $sql = "SELECT * FROM user WHERE email = '$email';";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $input_valid = 0;
        echo "The email '$email' is already in use.<br>";
    }

    // make sure the passwords are the same
    if ($password != $password2) {
        $input_valid = 0;
        echo "Those passwords don't match, buddy. <br>";
    }

    // If input is good, make an account
    if ($input_valid == 1) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO user VALUES (NULL, '$username', '$email', '$hashed_password', 0);";
        $result = $mysqli->query($sql);
        // send 'em to login
        Header('Location: ./login.php');
    }
}
?>