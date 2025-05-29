<?php
require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Search Clips</title>
    <style>
        body {
            background-color: #333; /* Darker background color for the page */
            color: #fff; /* Light text color */
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }

        header {
            background-color: #222; /* Darker header background color */
            padding: 10px;
        }

        h1 {
            color: #fff; /* Light text color for header */
        }

        button {
            background-color: #FF4500; /* Fiery orange-red button background color */
            color: #fff; /* Light text color for buttons */
            padding: 10px;
            border: none;
            cursor: pointer;
            margin: 10px 0; /* Added margin at the bottom of the button */
            border-radius: 5px;
        }

        .row {
            display: flex;
            justify-content: center; /* Center the columns horizontally */
            flex-wrap: wrap;
        }

        .column {
            flex: 1;
            max-width: 250px;
            padding: 10px;
            margin: 10px 5px;
            text-align: left; /* Left-align the content within each column */
        }

        input[type="checkbox"] {
            vertical-align: middle; /* Align checkboxes vertically with the text */
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: none;
            margin: 5px 0;
            border-radius: 5px;
            box-sizing: border-box; /* Include padding and border in width and height */
        }

        .column b {
            display: block;
            color: #fff;
            text-align: left;
            margin-top: 10px;
        }

    </style>
</head>
<body>
<header>
    <h1>Search Clips</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
</header>

<form action="clip_search_result.php" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="column">
            <!-- File Name -->
            <b>File Name:</b>
            <input type="text" name="clip_title" id="clip_title">
        </div>
        <div class="column">
            <!-- Date -->
            <b>Earliest Date:</b>
            <input type="date" name="earliest_date">
            <b>Latest Date:</b>
            <input type="date" name="latest_date">
        </div>
        <div class="column">
            <!-- Users -->
            <b>Involved Users:</b> <br>
            <?php
            $sql = "SELECT id, name FROM person ORDER BY name";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<input type="checkbox" name="selected_users[]" value="' . $row['id'] . '">';
                echo $row['name'] . '<br>';
            }
            ?>
        </div>
        <div class="column">
            <!-- Games -->
            <b>Games:</b> <br>
            <?php
            $sql = "SELECT id, name FROM game ORDER BY name";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<input type="checkbox" name="selected_games[]" value="' . $row['id'] . '">';
                echo $row['name'] . '<br>';
            }
            ?>
        </div>
        <div class="column">
            <!-- Users -->
            <b>Tags:</b> <br>
            <?php
            $sql = "SELECT id, name FROM tag";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<input type="checkbox" name="selected_tags[]" value="' . $row['id'] . '">';
                echo $row['name'] . '<br>';
            }
            ?>
        </div>
    </div>
    <!-- Submit Button -->
    <button type="submit" name="submit_search">Search</button>
</form>

</body>
</html>
