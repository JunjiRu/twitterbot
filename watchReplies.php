<?php
class subTwitterStreamingAPI extends twitterStreamingAPI{
    public function action(){
        $prevHour = null;
        $interval = 1;
        $twiRest = new twitterRestAPI();
        $twiRest->tweet('起動');
		fwrite($this->con,
			"GET https://userstream.twitter.com/1.1/user.json HTTP/1.1\r\n".
			"Host: userstream.twitter.com\r\n".
			'Authorization: OAuth ' . http_build_query($this->oauthParam, '', ',')."\r\n\r\n");
		while(true){
			$res = array();
			if(!feof($this->con)){
				$res = json_decode(fgets($this->con), true);
				$this->inAction($res, $twiRest);
			}

            if($prevHour !== date('H')){
                $this->tweetFromTweetBox($twiRest, null, 1);
                $prevHour = date('H');
                $interval++;
                if($interval%3 === 0){
                    $this->tweetFromTweetBox($twiRest, null, 3);
                    if($interval%6 === 0){
                        $this->tweetFromTweetBox($twiRest, null, 6);
                        if($interval%12 === 0){
                            $this->tweetFromTweetBox($twiRest, null, 12);
                            if($interval%24 === 0){
                                $this->tweetFromTweetBox($twiRest, null, 24);
                            }
                        }
                    }
                }
            }
		}
	}

    public function inAction($tweetData, $twiRest){
        $time = date('H:i:s');
        if( isset($tweetData['in_reply_to_screen_name']) &&
            $tweetData['in_reply_to_screen_name'] === BOT_SCREEN_NAME)
        {
            $tweetText = explode(' ', $tweetData['text']);
            $userScreenName = "@{$tweetData['user']['screen_name']}";
            $reply = "{$userScreenName} ";
            if(count($tweetText) === 2 && is_numeric($tweetText[1])){
                $tweetText[1] = (int)$tweetText[1];
                $reply .= $tweetText[1];

                if($tweetText[1] === 1){
                    $reply .= 'は1．';
                }else if($tweetText[1] === 57){
                    $reply .= 'はグロタンディーク素数．';
                }else if($tweetText[1] < 1){
                    $reply .= 'は考慮しない．';
                }else if(!$this->isRegisteredCompositeNumber($tweetText[1]) &&
                         ($this->isRegisteredPrimeNumber($tweetText[1]) || $this->isPrimeNumber($tweetText[1]))){
                    $reply .= 'は素数．';
                }else{
                    $reply .= 'の素因数:'.$this->getPrimeFactor($tweetText[1]);
                }

            }else if($tweetText[1] === 'memo' && count($tweetText) > 2){
                $interval = 3;
                if($tweetText[2] === '-h'){
                    if(is_numeric($tweetText[3]) && $tweetText[3] <= 24){
                        $interval = round($tweetText[3]);
                        unset($tweetText[2]);
                        unset($tweetText[3]);
                    }
                }
                unset($tweetText[0]);

                $text = $userScreenName.' '.str_replace('@', '＠', implode(' ', $tweetText));
                if(mb_strlen($text.' '.$time.' '.'   ') > 140){
                    $reply .= '長すぎ';
                }else{
                    $result = $this->registerUserMemo($userScreenName, $text, $interval);
                    if($result === false){
                        $reply .= 'メモ登録失敗';
                    }else{
                        $reply .= "メモ登録成功 ．削除する場合はこのbotに del {$result} とリプライ．";
                    }
                }

            }else if($tweetText[1] === 'del'){
                if(isset($tweetText[2]) && strlen($tweetText[2]) === 3 && $this->deleteUserMemo($userScreenName, $tweetText[2])){
                    $reply .= 'メモ削除完了';
                }else{
                    $reply .= 'メモ削除失敗';
                }

            }else if($tweetText[1] === 'getmemos'){
                $this->tweetFromTweetBox($twiRest, $userScreenName);
                $reply = '';

            }else if($tweetText[1] === 'delmemos'){
                $this->tweetFromTweetBox($twiRest, $userScreenName);
                $succeeded = $this->deleteUserMemo($userScreenName);
                if($succeeded){
                    $reply .= 'メモ全削除完了';
                }else{
                    $reply .= 'メモ削除失敗';
                }
            }else{
                $reply = '';
            }

            if($reply !== ''){
                $twiRest->tweet($reply.' '.$time, $tweetData['id_str'], $tweetData['user']['id_str']);
            }
        }
    }

    public function tweetFromTweetBox($twiRest, $author = null, $interval = null){
        $time = date('H:i:s');
        $query = 'select * from tweet_box where true';
        if($author !== null){
            $query .= " and author = '{$author}'";
        }
        if($interval !== null){
            $query .= " and `interval` = {$interval}"; 
        }
        $result = mysqli_query_ex($query);
        while($row = mysqli_fetch_assoc($result)){
            $twiRest->tweet($row['content'].' '.$row['id'].' '.$time);
        }
    }

    public function isRegisteredPrimeNumber($num){
        $query = "SELECT COUNT(*) as count FROM number WHERE number = {$num} AND prime_factor IS NULL";
        $result = mysqli_query_ex($query);
        $row = mysqli_fetch_assoc($result);
        if($row['count'] > 0){
            return true;
        }
        return false;
    }
    public function isRegisteredCompositeNumber($num){
        $query = "SELECT COUNT(*) as count FROM number WHERE number = {$num} AND prime_factor IS NOT NULL";
        $result = mysqli_query_ex($query);
        $row = mysqli_fetch_assoc($result);
        if($row['count'] > 0){
            return true;
        }
        return false;
    }
    public function isPrimeNumber($num){
        $numOrg = $num;
        $primeFactor = array();

        while($num%2===0){
            $primeFactor[] = 2;
            $num /= 2;
        }
        for($i=3;$i<=$num;$i+=2){
            while($num%$i===0){
                $primeFactor[] = $i;
                $num /= $i;
            }
        }

        if(count($primeFactor) === 1){
            $query = "insert into number(number)values({$numOrg})";
            mysqli_query_ex($query);
            return true;
        }else{
            $primeFactorText = implode(',', $primeFactor);
            $query = "insert into number(number, prime_factor)values({$numOrg}, '{$primeFactorText}')";
            mysqli_query_ex($query);
            return false;
        }
    }
    public function getPrimeFactor($num){
        $query = "SELECT prime_factor FROM number WHERE number = {$num}";
        $result = mysqli_query_ex($query);
        $row = mysqli_fetch_assoc($result);
        return $row['prime_factor'];
    }
    protected function registerUserMemo($author, $text, $interval){
        $id = '';
        while(true){
            $id = makeRandString(3);
            $query = "select id from tweet_box where author = '{$author}' and id = '{$id}'";
            $result = mysqli_query_ex($query);
            if(mysqli_num_rows($result) === 0){
                break;
            }
        }
        $text = mysqli_real_escape_string_ex($text);
        $query = "insert into tweet_box(author, content, `interval`, id)values('{$author}', '{$text}', $interval, '{$id}')";
        if(mysqli_query_ex($query)){
            return $id;
        }
        return false;
    }
    protected function deleteUserMemo($author, $id = null){
        $where = "where author = '{$author}'";
        if($id !== null){
            $id = mysqli_real_escape_string_ex($id);
            $where .= " and id = '{$id}'";
        }
        $query = "delete from tweet_box {$where}";

        return mysqli_query_ex($query);
    }
}
