<?php
include 'db_connection.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Archives - Clip Submission Form</title>
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
            margin: 10px;
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

        input[type="file"],
        input[type="date"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: none;
            margin: 5px 0;
            border-radius: 5px;
            box-sizing: border-box; /* Include padding and border in width and height */
        }

        input[type="checkbox"],
        input[type="radio"] {
            vertical-align: middle; /* Align checkboxes vertically with the text */
        }
    </style>
</head>
<body>
<header>
    <h1>Clip Submission Form</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
</header>

<form action="submit_clip.php" method="POST" onsubmit="validateForm(event)" enctype="multipart/form-data">
    <div class="row">
        <div class="column">
            <!-- Video -->
            <b>Select video to upload:</b>
            <input type="file" name="file_to_upload" id="file_to_upload" accept=".mp4" required>
            <br>

            <!-- Date -->
            <b>Date:</b>
            <input type="date" name="date" placeholder="Date posted">
        </div>
        <div class="column">
            <!-- Original Poster -->
            <b>Original Poster:</b> <br>
            <?php
            $sql = "SELECT id, name FROM person ORDER BY name";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<input type="radio" name="original_poster_id" value="' . $row['id'] . '"><label for="' . $row['id'] . '">' . $row['name'] . '<br></label>';
            }
            ?>
        </div>
        <div class="column">
            <!-- Users -->
            <b>Involved Users:</b> <br>
            <?php
            $sql = "SELECT id, name FROM person ORDER BY name";
            $result = $mysqli->query($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<input type="checkbox" name="selected_users[]" value="' . $row['id'] . '"><label for="' . $row['id'] . '">' . $row['name'] . '<br></label>';
            }
            ?>
        </div>
        <div class="column">
            <!-- Game -->
            <b>Game:</b> <br>
            <input type="text" id="game_search_input" placeholder="Search for a game">
            <br>
            <div id="game_radio_list">
                <?php
                $sql = "SELECT id, name FROM game";
                $result = $mysqli->query($sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<input type="radio" name="selected_game_id" value="' . $row['id'] . '"><label for="' . $row['id'] . '">' . $row['name'] . '<br></label>';
                }
                $mysqli->close();
                ?>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const searchInput = document.getElementById("game_search_input");
                    const radioList = document.querySelector('#game_radio_list');
                    const originalRadioList = radioList.innerHTML;

                    searchInput.addEventListener("input", function () {
                        const search = this.value.toLowerCase();

                        // Reset to the original list
                        radioList.innerHTML = originalRadioList;

                        // Get all radio buttons in the updated list
                        const radioButtons = radioList.querySelectorAll('input[type="radio"][name="selected_game_id"]');

                        radioButtons.forEach(function (radioButton) {
                            const label = radioButton.nextSibling;
                            const labelText = label.textContent.toLowerCase();

                            if (!labelText.includes(search)) {
                                // Remove the radio button and its associated label
                                radioButton.remove();
                                label.remove();
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
    <!-- Submit Button -->
    <button type="submit" name="submit">Submit Clip</button>
</form>
</body>
</html>
