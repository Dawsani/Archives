<?php

include('../db_connection.php');

$config = require $_SERVER['DOCUMENT_ROOT'] . '/../private/config/config.php';
$baseDir = realpath($config['datadirectory']); // Canonical path

if (!isset($_GET['src'])) {
    http_response_code(400);
    exit('Missing image parameter.');
}

$requestedPath = $_GET['src'];
$fullPath = realpath($baseDir . '/' . $requestedPath);

// Validate that real path is inside the allowed base directory
if ($fullPath === false || strpos($fullPath, $baseDir) !== 0) {
    http_response_code(403);
    exit('Access denied.');
}

if (!file_exists($fullPath)) {
    http_response_code(404);
    exit('Image not found.');
}

// Determine content type (optional: based on file extension)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
readfile($fullPath);
exit;
