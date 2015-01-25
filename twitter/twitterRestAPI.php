<?php
require_once(appRoot.'/twitter/OAuth.php');
require_once(appRoot.'/twitter/twitteroauth.php');

class twitterRestAPI{
	private $con;
	public function __construct() {
		$this->con = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	}

	public function tweet($text, $in_reply_to_status_id = null, $in_reply_to_user_id = null){
		$param = array(
			'status' => $text,
			'in_reply_to_status_id' => $in_reply_to_status_id,
			'in_reply_to_user_id' => $in_reply_to_user_id,
		);
		$this->con->OAuthRequest(
			'https://api.twitter.com/1.1/statuses/update.json',
			'POST',
			$param);
	}

    public function getTweetFromStatusId($statusId){
		$param = array(
			'id' => $statusId,
		);
		return
            json_decode(
                $this->con->OAuthRequest(
                    'https://api.twitter.com/1.1/statuses/show.json',
                    'GET',
                    $param
                ),
                true
            );
	}
}
