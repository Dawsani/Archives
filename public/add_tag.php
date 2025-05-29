<?php
require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';

$clip_id = $_POST['clip_id'];

$new_tag_name = $_POST['new_tag_name'];

$sql = "INSERT INTO tag VALUES (NULL, '$new_tag_name');";
$result = $mysqli->query($sql);

if ($result === TRUE) {
    $message = "New tag added successfully.";
} else {
    $error = "Error: " . $sql . "<br>" . $mysqli->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Add Tag</title>
    <style>
        body {
            background-color: #333;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }

        header {
            background-color: #222;
            padding: 10px;
        }

        h1 {
            color: #fff;
        }

        button {
            background-color: #FF4500;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin: 10px;
            border-radius: 5px;
        }

        .container {
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: #444;
            border-radius: 5px;
        }

        .message {
            color: #0f0; /* Green for success */
        }

        .error {
            color: #f00; /* Red for error */
        }
    </style>
</head>
<body>
<header>
    <h1>Add Tag</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
</header>

<div class="container">
    <?php
    if (isset($message)) {
        echo '<p class="message">' . $message . '</p>';
    } elseif (isset($error)) {
        echo '<p class="error">' . $error . '</p>';
    }
    ?>
    <button onclick="window.location.href = 'edit_clip_data.php?clip_id=<?php echo $clip_id; ?>'">Back to Edit Clip Data</button>
</div>

</body>
</html>
