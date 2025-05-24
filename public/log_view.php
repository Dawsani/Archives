<?php
include 'db_connection.php'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['video_id'])) {
        // Log the view in your database or perform other actions
        // For example, you can use PHP to update a view count in your database.
    }
}
?>