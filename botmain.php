<?php
require_once('common.php');

$query = 'select * from toriaezu';
$text = '';
$result = mysqli_query_ex($query);
while($row = mysqli_fetch_assoc($result)){
	$text .= $row['id'].':'.$row['data']."\n";
}

$twitter->tweet($text);
