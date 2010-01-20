<?php
	error_reporting(E_ALL);
	require_once('Config.class.php');
	$config = Config::singleton();
	$user_psw = $config->getPassword();
	echo $user_psw;
?>
