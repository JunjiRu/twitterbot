<?php
mb_internal_encoding('UTF-8');
define('BOT_SCREEN_NAME', 'junjiru');
define('appRoot', dirname(__FILE__));

//定数XXXXX1をconfig内でdefine gitに載せられない情報のため
require_once(appRoot.'/config.php');
require_once(appRoot.'/extendMysqli.php');
require_once(appRoot.'/twitter.php');

define('DB_HOSTNAME', DB_HOSTNAME1);
define('DB_USERNAME', DB_USERNAME1);
define('DB_PASSWORD', DB_PASSWORD1);
define('DB_DATABASE', DB_DATABASE1);

ini_set('date.timezone', 'Asia/Tokyo');

global $db;
$db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
mysqli_set_charset($db, 'utf8');

$twitter = new twitter();

function encode_rfc3986($str){
	return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode(($str))));
}

/* デバッグ利用 */
global $count;
$count = 0;
function echoText($value = 'defaultValue'){
	global $count;
	var_dump($value);
	echo $count+"\n";
	$count++;
}
