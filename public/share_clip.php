<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Share Clip</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .video-container {
            max-width: 640px;
        }

        .video {
            width: 100%;
            height: auto;
        }

        .message {
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <h1>Share Clip</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
</header>

<div class="container">
    <?php
    // Display video clip
    if (isset($_GET['clip_id'])) {
        $clip_id = $_GET['clip_id'];

        // Fetch the video information based on the $clipId (You might need a database query here)
        $sql = "SELECT clip.title FROM clip WHERE clip.id = $clip_id;";
        $result = $mysqli->query($sql);

        // Display all searched clips
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="video-container">
                        <video class="video" controls="controls">
                            <source src="clips/' . $row['title'] . '.mp4" type="video/mp4" />
                        </video>
                    </div>';
            }
        } else {
            echo "No results";
        }
    } else {
        echo "Clip ID not provided.";
    }
    $mysqli->close();
    ?>

    <br>
    Just copy the link to this page to share the clip.
</div>

</body>
</html>
