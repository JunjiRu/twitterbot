<?php
abstract class twitterStreamingAPI{
	abstract public function inAction($tweetData, $min);

	protected $con;
	protected $oauthParam;
	public function __construct(){
		$this->oauthParam = array(
			'oauth_consumer_key' => CONSUMER_KEY,
			'oauth_nonce' => microtime(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_token' => ACCESS_TOKEN,
			'oauth_version' => '1.0',
		);

		foreach($this->oauthParam as $key => $val){
			$this->oauthParam[$key] = OAuthUtil::urlencode_rfc3986($val);
		}

		ksort($this->oauthParam);
		$base_string =
			'GET&'.
			rawurlencode('https://userstream.twitter.com/1.1/user.json').'&'.
			rawurlencode(http_build_query($this->oauthParam, '', '&'));

		$key = implode('&', array(rawurlencode(CONSUMER_SECRET), rawurlencode(ACCESS_TOKEN_SECRET)));
		$this->oauthParam['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_string, $key, true));
		while(!$this->con = fsockopen('ssl://userstream.twitter.com', 443)){}
	}

	public function action(){
		fwrite($this->con,
			"GET https://userstream.twitter.com/1.1/user.json HTTP/1.1\r\n".
			"Host: userstream.twitter.com\r\n".
			'Authorization: OAuth ' . http_build_query($this->oauthParam, '', ',')."\r\n\r\n");
		while(true){
			$res = array();
			if(!feof($this->con)){
				$res = json_decode(fgets($this->con), true);
				$this->inAction($res);
			}
		}
	}
}

/*
ツイートのデータは以下の配列で取得される
ツイートする時にも同様のパラメータを設定したら幸せになれるかも
@junjiru から @junjiru_bot へリプライを送った場合
array(24) {
  ["created_at"]=>
  string(30) "Wed Dec 10 14:09:18 +0000 2014"
  ["id"]=>
  int(542682470663991296)
  ["id_str"]=>
  string(18) "542682470663991296"
  ["text"]=>
  string(17) "@junjiru_bot 4321"
  ["source"]=>
  string(82) "<a href="http://sites.google.com/site/yorufukurou/" rel="nofollow">YoruFukurou</a>"
  ["truncated"]=>
  bool(false)
  ["in_reply_to_status_id"]=>
  int(542678772328050688)
  ["in_reply_to_status_id_str"]=>
  string(18) "542678772328050688"
  ["in_reply_to_user_id"]=>
  int(2921846564)
  ["in_reply_to_user_id_str"]=>
  string(10) "2921846564"
  ["in_reply_to_screen_name"]=>
  string(11) "junjiru_bot"
  ["user"]=>
  array(39) {
    ["id"]=>
    int(407670920)
    ["id_str"]=>
    string(9) "407670920"
    ["name"]=>
    string(43) "じゅんじ@がんば．．．るぞい！"
    ["screen_name"]=>
    string(7) "junjiru"
    ["location"]=>
    string(6) "東京"
    ["profile_location"]=>
    NULL
    ["url"]=>
    NULL
    ["description"]=>
    NULL
    ["protected"]=>
    bool(false)
    ["followers_count"]=>
    int(187)
    ["friends_count"]=>
    int(115)
    ["listed_count"]=>
    int(22)
    ["created_at"]=>
    string(30) "Tue Nov 08 11:18:33 +0000 2011"
    ["favourites_count"]=>
    int(13383)
    ["utc_offset"]=>
    int(32400)
    ["time_zone"]=>
    string(7) "Irkutsk"
    ["geo_enabled"]=>
    bool(false)
    ["verified"]=>
    bool(false)
    ["statuses_count"]=>
    int(62807)
    ["lang"]=>
    string(2) "ja"
    ["contributors_enabled"]=>
    bool(false)
    ["is_translator"]=>
    bool(false)
    ["is_translation_enabled"]=>
    bool(false)
    ["profile_background_color"]=>
    string(6) "C0DEED"
    ["profile_background_image_url"]=>
    string(48) "http://abs.twimg.com/images/themes/theme1/bg.png"
    ["profile_background_image_url_https"]=>
    string(49) "https://abs.twimg.com/images/themes/theme1/bg.png"
    ["profile_background_tile"]=>
    bool(false)
    ["profile_image_url"]=>
    string(75) "http://pbs.twimg.com/profile_images/453205717210497024/nwDGgnfb_normal.jpeg"
    ["profile_image_url_https"]=>
    string(76) "https://pbs.twimg.com/profile_images/453205717210497024/nwDGgnfb_normal.jpeg"
    ["profile_link_color"]=>
    string(6) "0084B4"
    ["profile_sidebar_border_color"]=>
    string(6) "C0DEED"
    ["profile_sidebar_fill_color"]=>
    string(6) "DDEEF6"
    ["profile_text_color"]=>
    string(6) "333333"
    ["profile_use_background_image"]=>
    bool(true)
    ["default_profile"]=>
    bool(true)
    ["default_profile_image"]=>
    bool(false)
    ["following"]=>
    NULL
    ["follow_request_sent"]=>
    NULL
    ["notifications"]=>
    NULL
  }
  ["geo"]=>
  NULL
  ["coordinates"]=>
  NULL
  ["place"]=>
  NULL
  ["contributors"]=>
  NULL
  ["retweet_count"]=>
  int(0)
  ["favorite_count"]=>
  int(0)
  ["entities"]=>
  array(4) {
    ["hashtags"]=>
    array(0) {
    }
    ["symbols"]=>
    array(0) {
    }
    ["user_mentions"]=>
    array(1) {
      [0]=>
      array(5) {
        ["screen_name"]=>
        string(11) "junjiru_bot"
        ["name"]=>
        string(3) "bot"
        ["id"]=>
        int(2921846564)
        ["id_str"]=>
        string(10) "2921846564"
        ["indices"]=>
        array(2) {
          [0]=>
          int(0)
          [1]=>
          int(12)
        }
      }
    }
    ["urls"]=>
    array(0) {
    }
  }
  ["favorited"]=>
  bool(false)
  ["retweeted"]=>
  bool(false)
  ["filter_level"]=>
  string(6) "medium"
  ["lang"]=>
  string(3) "und"
  ["timestamp_ms"]=>
  string(13) "1418220558216"
}
 */
