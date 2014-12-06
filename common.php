<?php
mb_internal_encoding('UTF-8');
define('appRoot', dirname(__FILE__));

//定数XXXXX1をconfig内でdefine gitに載せられない情報のため
require_once(appRoot . '/config.php');
require_once(appRoot . '/extendMysqli.php');

define('CONSUMER_KEY', CONSUMER_KEY1);
define('CONSUMER_SECRET', CONSUMER_SECRET1);
define('ACCESS_TOKEN', ACCESS_TOKEN1);
define('ACCESS_TOKEN_SECRET', ACCESS_TOKEN_SECRET1);

define('DB_HOSTNAME', DB_HOSTNAME1);
define('DB_USERNAME', DB_USERNAME1);
define('DB_PASSWORD', DB_PASSWORD1);
define('DB_DATABASE', DB_DATABASE1);

ini_set('date.timezone', 'Asia/Tokyo');

global $db;
$db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
mysqli_set_charset($db, 'utf8');

require_once(appRoot.'/auth/OAuth.php');
require_once(appRoot.'/auth/twitteroauth.php');
