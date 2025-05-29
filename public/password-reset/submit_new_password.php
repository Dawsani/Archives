<?php

    require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST["password"];
        $password2 = $_POST["password2"];
    }

    // make sure the passwords are the same
    if ($password != $password2) {
        $input_valid = 0;
        echo "Those passwords don't match, buddy. <br>";
    }


?>