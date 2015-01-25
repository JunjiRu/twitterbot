<?php
class subTwitterStreamingAPI extends twitterStreamingAPI{
    public function action(){
        $prevHour = null;
        $twiRest = new twitterRestAPI();
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
                $this->execPeriodic($twiRest);
                $prevHour = date('H');
            }
		}
	}

    public function inAction($tweetData, $twiRest){
        $time = date('H:i:s');
        if( isset($tweetData['in_reply_to_screen_name']) &&
            $tweetData['in_reply_to_screen_name'] === BOT_SCREEN_NAME)
        {
            $tweetText = explode(' ', $tweetData['text']);
            $reply = "@{$tweetData['user']['screen_name']} ";
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
                for($i=1;$i<count($tweetText);$i++){
                    $reply .= $tweetText[$i].' ';
                }

                $this->registerUserMemo($reply);
                $reply .= 'をメモとして登録しました．1時間毎にリプります．';
            }else if($tweetText[1] === 'del'){
                $reply .= 'メモを削除しました． ';
                $targetData = $twiRest->getTweetFromStatusId($tweetData['in_reply_to_status_id']);
                //文末のタイムスタンプを削除
                $targetData = explode(' ', $targetData['text']);
                unset($targetData[count($targetData)-1]);
                $this->deleteUserMemo(implode(' ', $targetData));
            }else{
                $reply = '';
            }
            if($reply !== ''){
                $twiRest->tweet($reply.' '.$time, $tweetData['id_str'], $tweetData['user']['id_str']);
            }
        }
    }

    public function execPeriodic($twiRest){
        $time = date('H:i:s');
        $query = 'select * from user_memo';
        $result = mysqli_query_ex($query);
        while($row = mysqli_fetch_assoc($result)){
            $twiRest->tweet($row['content'].' '.$time);
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
    protected function registerUserMemo($text){
        //TODO: 重複への対処
        //TODO: @のエスケープ
        //TODO: 文末スペースの削除
        $text = mysqli_real_escape_string_ex($text);
        $query = "insert into user_memo(content)values('{$text}')";
        mysqli_query_ex($query);
    }
    protected function deleteUserMemo($target){
        //TODO: userのメモと一緒に定型文がつっこまれてるので分離するか何かして対策を
        $target = mysqli_real_escape_string_ex($target);
        $query = "delete from user_memo where content = '{$target}'";
        mysqli_query_ex($query);
    }
}
