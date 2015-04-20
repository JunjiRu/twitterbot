<?php
function mysqli_query_ex($query){
	global $db;
	return mysqli_query($db, $query);
}
function mysqli_real_escape_string_ex($str){
	global $db;
	return mysqli_real_escape_string($db, $str);
}

function makeRandString($length){
    $charSet = array_merge(range('a', 'z'), range('0', '9'));
    $str = '';
    $count = count($charSet) - 1;
    for($i=0;$i<$length;$i++){
        $str .= $charSet[rand(0, $count)];
    }
    return $str;
}
