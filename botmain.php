<?php
require_once('common.php');
require_once('watchReplies.php');

//リプライ監視スタート
$twiStreaming = new subTwitterStreamingAPI();
$twiStreaming->action();
