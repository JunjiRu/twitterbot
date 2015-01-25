<?php
mb_internal_encoding('UTF-8');
ini_set('date.timezone', 'Asia/Tokyo');
define('appRoot', dirname(__FILE__));
require_once('common_ini.php');

define('BOT_SCREEN_NAME', 'junjiru_bot');

//定数XXXXX1をconfig内でdefine gitに載せられない情報のため
require_once(appRoot.'/config.php');

define('CONSUMER_KEY', CONSUMER_KEY1);
define('CONSUMER_SECRET', CONSUMER_SECRET1);
define('ACCESS_TOKEN', ACCESS_TOKEN1);
define('ACCESS_TOKEN_SECRET', ACCESS_TOKEN_SECRET1);

require_once(appRoot.'/extendMysqli.php');
require_once(appRoot.'/twitter/twitterRestAPI.php');
require_once(appRoot.'/twitter/twitterStreamingAPI.php');

define('DB_HOSTNAME', DB_HOSTNAME1);
define('DB_USERNAME', DB_USERNAME1);
define('DB_PASSWORD', DB_PASSWORD1);
define('DB_DATABASE', DB_DATABASE1);

global $db;
$db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
mysqli_set_charset($db, 'utf8');
