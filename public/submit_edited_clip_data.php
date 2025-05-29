<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';

    $clip_id = $_POST['clip_id'];
    echo "clip_id: " . $clip_id . "<br>";

    $selected_tags = $_POST['selected_tags'];
    if (isset($selected_tags) && is_array($selected_tags)) {
        echo "tags: ";
        foreach ($selected_tags as $selected_tag_id) {

            // INSERT int o clip_tag
            $sql = "INSERT INTO clip_tag VALUES ($clip_id, $selected_tag_id)";
            $result = $mysqli->query($sql);
            if ($result === TRUE) {
                #echo "New clip added sucessfully. The last inserted ID is: " . $mysqli->insert_id . "<br>";
            }
            else {
                #echo "Error: " . $sql . "<br>" . $mysqli->error . "<br>";
            }

            echo $selected_tag_id . " ";
        } echo "<br>";
    }
    else {
        echo "no selected users.<br>";
    }

    header("Location: index.php");


?>