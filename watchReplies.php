<?php
require_once('common.php');

class subTwitterStreamingAPI extends twitterStreamingAPI{
	public function inAction($tweetData){
		if( isset($tweetData['in_reply_to_screen_name']) &&
			$tweetData['in_reply_to_screen_name'] === BOT_SCREEN_NAME)
		{
			global $twiRest;
			$time = date('H:i:s');
			$tweetText = explode(' ', $tweetData['text']);
			$reply = "@{$tweetData['user']['screen_name']} ";
			if(count($tweetText) === 2 && is_numeric($tweetText[1])){
				$tweetText[1] = (int)$tweetText[1];
				$reply .= "{$tweetText[1]}";

				if($tweetText[1] === 1){
					$reply .= "は1．";
				}else if($tweetText[1] === 57){
					$reply .= "はグロタンディーク素数．";
				}else if(!$this->isRegisteredCompositeNumber($tweetText[1]) &&
						 ($this->isRegisteredPrimeNumber($tweetText[1]) || $this->isPrimeNumber($tweetText[1]))){
					$reply .= "は素数．";
				}else{
					$reply .= 'の素因数:'.$this->getPrimeFactor($tweetText[1]);
				}
			}else{
				$reply .= "数字ではない．";
			}
			$twiRest->tweet($reply.' '.$time, $tweetData['id_str']);
		}
	}

	private function isRegisteredPrimeNumber($num){
		$query = "SELECT COUNT(*) as count FROM number WHERE number = {$num} AND prime_factor IS NULL";
		$result = mysqli_query_ex($query);
		$row = mysqli_fetch_assoc($result);
		if($row['count'] > 0){
			return true;
		}
		return false;
	}
	private function isRegisteredCompositeNumber($num){
		$query = "SELECT COUNT(*) as count FROM number WHERE number = {$num} AND prime_factor IS NOT NULL";
		$result = mysqli_query_ex($query);
		$row = mysqli_fetch_assoc($result);
		if($row['count'] > 0){
			return true;
		}
		return false;
	}
	private function isPrimeNumber($num){
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
	private function getPrimeFactor($num){
		$query = "SELECT prime_factor FROM number WHERE number = {$num}";
		$result = mysqli_query_ex($query);
		$row = mysqli_fetch_assoc($result);
		return $row['prime_factor'];
	}
}

$a = new subTwitterStreamingAPI();
$a->action();
