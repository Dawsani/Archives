<?php

function userEmailToId($conn, $email) {
	$stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
	$stmt->bind_param('s', $email);
	$stmt->execute();

	$userId = -1;

	$stmt->store_result();
	$stmt->bind_result($userId);
	$stmt->fetch();

	return $userId;
}

?>