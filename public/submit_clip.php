<?php
// Connect to mySQL
include 'db_connection.php';

// Function to generate a thumbnail using FFmpeg
function generateThumbnail($inputVideo, $outputThumbnail) {
    $ffmpegCommand = "ffmpeg -i \"$inputVideo\" -ss 00:00:00 -frames:v 1 \"$outputThumbnail\"";
    exec($ffmpegCommand);
}

function compress_clip($inputVideo) {
    $output_file = "clips/" . pathinfo($inputVideo, PATHINFO_FILENAME) . ".mp4";
    $ffmpegCommand = "ffmpeg -i \"$inputVideo\" -s 852x480 -r 30 -c:v libx264 -crf 23 -c:a aac -b:a 128k \"$output_file\" > output.txt 2>&1";
    exec($ffmpegCommand, $output, $returnCode);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Upload Result</title>
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

        .message {
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <h1>Upload Result</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
</header>

</body>
</html>

<?php
// Get uploaded file
$target_dir = "original_clips/";
$file_name = $_FILES["file_to_upload"]["name"];
$clip_title = str_replace(".mp4", "", $file_name);
$target_file = $target_dir . basename($file_name);
$upload_ok = 1;
$image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if file is of correct type
if ($image_file_type == "mp4") {
    $upload_ok = 1;
} else {
    $upload_ok = 0;
    echo "File is not a video. <br>";
    //header("Location: ../?upload=not_a_video_file");
}

// Check if file already exists
if (file_exists($target_file)) {
    $upload_ok = 0;
    echo "Sorry, file already exists. <br>";
    //header("Location: ../?upload=file_" . $target_file . "_already_exists");
}

// if everything is ok, try to upload file
else {
    if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
        // create a thumbnail of the video
        $thumbnailFile = "thumbnails/" . pathinfo($target_file, PATHINFO_FILENAME) . ".jpg";
        generateThumbnail($target_file, $thumbnailFile);
        compress_clip($target_file);

        echo "The file " . htmlspecialchars(basename($_FILES["file_to_upload"]["name"])) . " has been uploaded. <br>";
    } else {
        $upload_ok = 0;
        echo "Sorry, there was an error uploading your file.<br>";
        //header("Location: ../?upload=error_uploading");
    }
}

if ($upload_ok == 1) {
    // Get clip info
    $post_date = $_POST["date"];
    if ($post_date == '') {
        $post_date = 'NULL';
    } else {
        $post_date = "(DATE '" . $post_date . "')";
    }

    $game_id = $_POST['selected_game_id'];
    if ($game_id == '') {
        $game_id = 'NULL';
    }

    $original_poster_id = $_POST['original_poster_id'];
    if ($original_poster_id == '') {
        $original_poster_id = 'NULL';
    }

    $selected_users = $_POST['selected_users'];

    // Add the clip into the clip table
    $sql = "INSERT INTO clip 
            VALUES (NULL, 
                    '$clip_title', 
                    $game_id, 
                    $original_poster_id,
                    $post_date, 
                    0);";
    $result = $mysqli->query($sql);
    if ($result === TRUE) {
        echo "New clip added successfully. The last inserted ID is: " . $mysqli->insert_id . "<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error . "<br>";
    }

    function add_clip_user_entry($mysqli, $clip_title, $user_id) {
        $sql = "INSERT INTO clip_person VALUES (
            (SELECT clip.id FROM clip WHERE clip.title LIKE '$clip_title'),
            '$user_id'
        )";
        $result = $mysqli->query($sql);
        if ($result === TRUE) {
            # echo "New clip user relation added successfully. The last inserted ID is: " . $mysqli->insert_id . "<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error . "<br>";
        }
    }

    // Add a clip_user entry for every user involved
    if (isset($selected_users) && is_array($selected_users)) {
        foreach ($selected_users as $selected_user_id) {
            add_clip_user_entry($mysqli, $clip_title, $selected_user_id);
        }
    }
    //header("Location: ../?upload=success");
}
?>
