<?php
$path = dirname(__FILE__).'/botmain.php';
exec("nohup php '{$path}'");
exit();
