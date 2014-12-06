<?php
require_once('common.php');

$query = 'select * from toriaezu';
$text = '';
$result = mysqli_query_ex($query);
while($row = mysqli_fetch_assoc($result)){
	$text .= $row['id'].':'.$row['data']."\n";
}

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$connection->OAuthRequest(
	'https://api.twitter.com/1.1/statuses/update.json',
	'POST',
	array('status' => $text));
