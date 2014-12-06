<?php
function mysqli_query_ex($query){
	global $db;
	return mysqli_query($db, $query);
}
function mysqli_real_escape_string_ex($str){
	global $db;
	return mysqli_real_escape_string($db, $str);
}
