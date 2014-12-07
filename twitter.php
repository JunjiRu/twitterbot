<?php
require_once(appRoot.'/auth/OAuth.php');
require_once(appRoot.'/auth/twitteroauth.php');

define('CONSUMER_KEY', CONSUMER_KEY1);
define('CONSUMER_SECRET', CONSUMER_SECRET1);
define('ACCESS_TOKEN', ACCESS_TOKEN1);
define('ACCESS_TOKEN_SECRET', ACCESS_TOKEN_SECRET1);

class twitter{
	private $con;
	public function __construct() {
		$this->con = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	}

	public function tweet($text, $in_reply_to_status_id = null){
		$param = array(
			'status' => $text,
			'in_reply_to_status_id' => $in_reply_to_status_id,
		);
		$this->con->OAuthRequest(
			'https://api.twitter.com/1.1/statuses/update.json',
			'POST',
			$param);
	}
}
