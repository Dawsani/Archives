<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clip Search Results</title>
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

    .video-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }

    .video-card {
      margin: 20px;
      text-align: center;
      padding: 10px;
      background-color: #222;
      border-radius: 10px;
      border: 2px solid transparent;
    }

    video {
      width: 100%;
      max-width: 640px;
      height: auto;
    }

    img {
      width: 100%;
      max-width: 640px;
      height: auto;
      cursor: pointer;
    }

    button {
      background-color: #FF4500;
      color: #fff;
      padding: 5px 10px;
      border: none;
      cursor: pointer;
      margin: 5px;
    }

    .show-more-container {
      margin-top: 20px; /* Adjust the margin as needed */
    }
  </style>
</head>
<body>
<header>
    <h1>Clip Search Results</h1>
    <button onclick="window.location.href = 'index.php'">Home</button>
    <button onclick="window.location.href = 'clip_search.php'">Search Clips</button>
</header>

<?php
// Define initial SEARCH
$sql = "SELECT DISTINCT title, name, post_date, user_count, cid
        FROM (
            SELECT clip.title, clip.id AS cid, game.name, clip.post_date, COUNT(clip_person.person_id) AS user_count, original_poster_id";

$search_constraints = 0;

// Get clip info

// Selected tags
$tags_are_selected = 0;
if (isset($_POST['selected_tags'])) {
    $tags_are_selected = 1;
    $sql .= ", tag_id FROM clip join clip_person ON clip.id = clip_id JOIN game ON clip.game_id = game.id";
    
    $selected_tags_array = $_POST['selected_tags'];
    $selected_tags = implode(',', $selected_tags_array);
    
    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " JOIN clip_tag ON clip.id = clip_tag.clip_id WHERE ";
    }
    $sql .= "tag_id IN ($selected_tags)";
}

if ($tags_are_selected == 0) {
    $sql .= " FROM clip join clip_person ON clip.id = clip_id JOIN game ON clip.game_id = game.id";
}

// Clip title
$clip_title = $_POST["clip_title"];
if ($clip_title != '') {
    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " WHERE ";
    }
    else {
        $sql .= " AND ";
    }
    $sql .= "title LIKE '%$clip_title%'";
}

// Earliest date
$earliest_date = $_POST["earliest_date"];
if ($earliest_date != '') {
    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " WHERE ";
    }
    else {
        $sql .= " AND ";
    }
    $sql .= "post_date >= '$earliest_date'";
}

// Latest date
$latest_date = $_POST["latest_date"];
if ($latest_date != '') {
    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " WHERE ";
    }
    else {
        $sql .= " AND ";
    }
    $sql .= "post_date <= '$latest_date'";
}

// Selected games
if (isset($_POST['selected_games'])) {
    $selected_games = $_POST['selected_games'];
    $selected_games = implode(',', $selected_games);

    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " WHERE ";
    }
    else {
        $sql .= " AND ";
    }
    $sql .= "game.id IN ($selected_games)";
}

// Selected users
$users_are_selected = 0;
if (isset($_POST['selected_users'])) {
    $selected_users_array = $_POST['selected_users'];
    $selected_users = implode(',', $selected_users_array);
    
    $users_are_selected = 1;
    $search_constraints++;
    if ($search_constraints == 1) {
        $sql .= " WHERE ";
    }
    else {
        $sql .= " AND ";
    }
    $sql .= "person_id IN ($selected_users)";
}

$sql .= " GROUP BY clip.id";

// if users are selected add the ORDER BY
if ($users_are_selected == 1) {
    $sql .= " ORDER BY user_count DESC, post_date";
}

$sql .= ") AS subquery;";

$result = $mysqli->query($sql);
if ($result == TRUE) {
    #echo "Search succeeded. <br>";
}
else {
    echo "Error: " . $sql . "<br>" . $mysqli->error . "<br>";
}

$clip_contains_all_searched_users = 1;
// Display all searched clips
if ($result->num_rows > 0) {
    echo "<div class=\"video-container\">";
    while ($row = $result->fetch_assoc()) {
      echo "<div class='video-card'>
              <b>" . $row["title"] . "</b> " . $row["name"] . " " . $row["post_date"] . "<br>
              <img id='thumbnail-" . $row["cid"] . "' src='scripts/load-data.php?src=thumbnails/" . $row["title"] . ".jpg' onclick=\"toggleVideo('" . $row['cid'] . "', '" . $row['title'] . "')\">
              <video id='video-player-" . $row["cid"] . "' controls style='display: none;'>
                <!-- Initially, no source is specified -->
              </video>
              <br>
              <button onclick=\"window.location.href = 'edit_clip_data.php?clip_id=" . $row['cid'] . "';\">Add tag</button>
              <button onclick=\"window.location.href = 'share_clip.php?clip_id=" . $row['cid'] . "'\">Share</button><br><br>
            </div>";
    }
    echo "</div>";
  } else {
    echo "0 results";
  }
?>

<!-- Show more button below the last row of clips -->
<div class="show-more-container">
  <?php
  $nextPage = $currentPage + 1;
  #echo "<button onclick=\"window.location.href = 'clip_search_result.php?page=$nextPage'\">Show More</button>";
  ?>
</div>

<script>
  function toggleVideo(videoId, clipName) {
    var thumbnail = document.getElementById('thumbnail-' + videoId);
    var videoPlayer = document.getElementById('video-player-' + videoId);

    // Toggle the visibility of the thumbnail and video player
    thumbnail.style.display = thumbnail.style.display === 'none' ? 'block' : 'none';
    videoPlayer.style.display = videoPlayer.style.display === 'none' ? 'block' : 'none';

    // If the video player is displayed, load the video
    if (videoPlayer.style.display === 'block') {
      videoPlayer.src = 'scripts/load-data.php?src=clips/' + clipName + '.mp4';
      videoPlayer.load();
      videoPlayer.play();
    }
  }
</script>

</body>
</html>
