<?php
require $_SERVER["DOCUMENT_ROOT"] . '/scripts/check_logged_in.php';
require $_SERVER["DOCUMENT_ROOT"] . '/scripts/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
  <h1>The Archives</h1>
  <button onclick="window.location.href = 'clip_submission_form.php'">Upload Clip</button>
  <button onclick="window.location.href = 'clip_search.php'">Search Clips</button>
</header>

<div class="video-container">
  <?php
  // Set the number of clips to show per page
  $clipsPerPage = 21;

  // Get the page number from the URL or set it to 1 if not present
  $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

  // Calculate the offset based on the current page
  $offset = ($currentPage - 1) * $clipsPerPage;

  // Display clips based on pagination
  $sql = "SELECT clip.id as cid, title, post_date, name FROM clip JOIN game ON clip.game_id = game.id ORDER BY post_date DESC LIMIT $clipsPerPage OFFSET $offset;";
  $result = $mysqli->query($sql);

  if ($result->num_rows > 0) {
	  while ($row = $result->fetch_assoc()) {

      $videoCard =  
        <<<HTML
        <div class='video-card'>
          <b>{$row["title"]}</b>{$row["name"]} {$row["post_date"]}<br>
          <img 
            id='thumbnail-{$row["cid"]}' 
            src='scripts/load-data.php?src=thumbnails/{$row["title"]}.jpg' 
            onclick="toggleVideo({$row['cid']}, '{$row['title']}')">
          <video 
            id='video-player-{$row["cid"]}'
            controls style='display: none;'
          >
            <!-- Initially, no source is specified -->
          </video><br>
          <button onclick="window.location.href = 'edit_clip_data.php?clip_id={$row['cid']}';\">Add tag</button>
          <button onclick="window.location.href = 'share_clip.php?clip_id={$row['cid']}'\">Share</button><br><br>
        </div>
        HTML;

        echo $videoCard;
    }
  } else {
    echo "0 results";
  }
  ?>
</div>

<!-- Show more button below the last row of clips -->
<div class="show-more-container">
  <?php
  $nextPage = $currentPage + 1;
  echo "<button onclick=\"window.location.href = 'index.php?page=$nextPage'\">Show More</button>";
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
