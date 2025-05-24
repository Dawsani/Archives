<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Edit Clip Data</title>
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

        .form-container {
            max-width: 400px;
        }

        .form {
            background-color: #444;
            padding: 20px;
            border-radius: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: none;
            margin: 5px 0;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="checkbox"] {
            vertical-align: middle;
        }

        label {
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <h1>Edit Clip Data</h1>
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

        if ($result == TRUE) {
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
    }
    ?>

    <div class="form-container">
        <form action="add_tag.php" method="post" class="form">
            <b>Create New Tag</b><br>
            <input type="hidden" name="clip_id" value="<?php echo $clip_id ?>">
            <input type="text" name="new_tag_name" placeholder="New tag name"><br>
            <button>Create New Tag</button>
        </form>
    </div>

    <div class="form-container">
        <form action="submit_edited_clip_data.php" method="POST" class="form">
            <b>Select tags to apply</b><br>
            <input type="hidden" name="clip_id" value="<?php echo $clip_id ?>">
            <?php
            // Display all tags
            $sql = "SELECT * FROM tag WHERE tag.id NOT IN (SELECT tag.id FROM tag JOIN clip_tag ON tag.id = tag_id WHERE clip_id = $clip_id);";
            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<input type="checkbox" name="selected_tags[]" value="' . $row['id'] . '"><label for="' . $row['id'] . '">' . $row['name'] . '</label><br>';
                }
                echo '<button type="submit" name="submit">Submit Changes</button>';
            } else {
                echo "All tags already added to this clip!<br>";
            }
            $mysqli->close();
            ?>
        </form>
    </div>
</div>

</body>
</html>
