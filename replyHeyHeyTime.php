<?php
require_once('common.php');

class subTwitterStreamingAPI extends twitterStreamingAPI{
	public function inAction($tweetData){
		if( isset($tweetData['in_reply_to_screen_name']) &&
			$tweetData['in_reply_to_screen_name'] === BOT_SCREEN_NAME)
		{
			global $twiRest;
			$time = date('H:i:s');
			$text = "@{$tweetData['user']['screen_name']} へいへいへい {$time}";
			$twiRest->tweet($text, $tweetData['id_str']);
		}
	}
}

$a = new subTwitterStreamingAPI();
$a->action();
