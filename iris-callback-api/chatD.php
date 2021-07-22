<?php

//return;

	require_once("classes/base.php");
	require_once(CLASSES_PATH . "Ub/DbUtil.php");
	require_once(CLASSES_PATH . 'Ub/VkApi.php');
	require_once(CLASSES_PATH . 'Ub/Util.php');

	$duty_id = (int)@$_GET['duty_id'];
	$peer_id = (int)@$_GET['peer_id'];
	$localId = (int)@$_GET['localId'];
	$MyBotId = (int)@$_GET['MyBotId'];
	$ch_code = (string)@$_GET['Iris'];
	
header('Cache-Control: no-store, no-cache, must-revalidate', true);
header('Content-Type: text/html; charset=utf-8', true);
header('X-UA-Compatible: IE=edge', true); /* 4 MSIE */
ini_set("display_errors" , 1);
ini_set('display_startup_errors', 1);

if ($duty_id <= 0 || $ch_code =='') {
		/* якщо нема */
		exit('0');
}

if ($duty_id > 0 && $ch_code !='') {
		$query = "UPDATE `userbot_bind` SET `id_duty` = '$duty_id' WHERE `code` = '$ch_code';";
		UbDbUtil::query($query);
		exit('ok');
}

