<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../private/scripts/load-config.php';

function verify_password_reset_token($email, $token) {
	$mysqli = db_connect();

	// check if the token and email are valid
	$stmt = $mysqli->prepare("  SELECT user_id, code
								FROM password_reset
								WHERE code = ? AND expire > NOW()");
	$stmt->bind_param("s", $token);
	$stmt->execute();

	// Get the result
	$result = $stmt->get_result();

	// get userId 
	$userId = userEmailToId($mysqli, $email);

	// see if the proviced code exists
	if ($result->num_rows === 0) {
		return FALSE;
	}

	// make sure the code is tied to the input email
	$row = $result->fetch_assoc();
	if ($row["user_id"] != $userId) {
		return FALSE;
	}

	return TRUE;
}

function db_connect() {
	$configs = load_config(); 

	$host = $configs['dbhost'];
	$username = $configs['dbuser'];
	$user_pass = $configs['dbpassword'];
	$database_in_use = $configs['dbname'];

	$mysqli = new mysqli($host, $username, $user_pass, $database_in_use);

	// Check the connection
	if ($mysqli->connect_error) {
		die("Connection failed: " . $mysqli->connect_error);
	}

	return $mysqli;
}

function crypto_rand_secure($min, $max) {
	$range = $max - $min;
	if ($range < 0) return $min; // not so random...
	$log = log($range, 2);
	$bytes = (int) ($log / 8) + 1; // length in bytes
	$bits = (int) $log + 1; // length in bits
	$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
	do {
		$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
		$rnd = $rnd & $filter; // discard irrelevant bits
	} while ($rnd >= $range);
	return $min + $rnd;
}

function getToken($length=32){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	for($i=0;$i<$length;$i++){
		$token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
	}
	return $token;
}

function send_email($recipient, $resetToken) {

	$fromName = "The Archives";
	$subject = "Password Reset";

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
	$headers .= "From: $fromName <dawson.dwm@gmail.com>" . "\r\n";

	$msg = <<<HTML
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<title>Archives Password Reset</title>
		</head>
		<body>
			<h1>Password Reset</h1>
			<p>Here is your password reset code as requested:</p><br>
			<h2>{$resetToken}</h2><br>
			<p>If you did not request this password reset, let Dawson know.</p>
		</body>
	</html>
	HTML;

	// use wordwrap() if lines are longer than 70 characters
	$msg = wordwrap($msg,70);

	// send email
	mail($recipient, $subject, $msg, $headers);
}

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