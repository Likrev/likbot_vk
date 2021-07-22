<?php
##### ВНИМАНИЕ
// Этот файл для того, чтобы можно было в форме заносить пользователей с токенами
// Спрячьте этот файл в папку, где требуется особый доступ, а затем уберите блокирующий "return" (он строчкой ниже)
//return;
ini_set("display_errors" , 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
header('Cache-Control: no-store, no-cache, must-revalidate', true);
header('Content-Type: text/html; charset=utf-8', true);
header('X-UA-Compatible: IE=edge', true); /* 4 MSIE */
echo '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru"><head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1,width=device-width" />
<style type="text/css">
	html, body, table, * { margin: 0 auto; }
	body, table { background: transparent; margin: 0 auto; max-width: 800px; }
	body { background: transparent url("https://likrev.pp.ua/wp-content/uploads/2020/07/Ont5S4izXLk.jpg") center; }
	html { background: transparent; margin: 0; padding: 0; text-align: center; }
</style></head><body style="margin:0px auto;max-width:800px;min-widht:100px;">
';

    function passgen($len = 32) {
    $password = '';
    $small = 'abcdefghijklmnopqrstuvwxyz';
    $large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '1234567890';
    for ($i = 0; $i < $len; $i++) {
        switch (mt_rand(1, 3)) {
            case 3 :
                $password .= $large [mt_rand(0, 25)];
                break;
            case 2 :
                $password .= $small [mt_rand(0, 25)];
                break;
            case 1 :
                $password .= $numbers [mt_rand(0, 9)];
                break;
        }
    }
    return $password;
    }

if (isset($_POST['login']) && isset($_POST['password'])) {
	require_once("classes/base.php");
	require_once(CLASSES_PATH . "Ub/DbUtil.php");
	require_once(CLASSES_PATH . 'Ub/VkApi.php');
	$login = (string)@$_POST['login'];
	$passw = (string)@$_POST['password'];
	$bptime = (int)time();
	$secret = (isset($_POST['secret'])?(string)@$_POST['secret']:passgen(mt_rand(8, 32)));
	$vk = new UbVkApi(false);
	$login = $vk->login($l = $login,$p = $passw, $ua = @$_SERVER['HTTP_USER_AGENT']);
	if (preg_match('#Ошибка#', $login)) {
		echo '<h1>Ошибище</h1>';
		echo "<p>$login</p>";
		return;
	} elseif((bool)$login == true) {
				$ktoken = $vk->generateAccessToken(2685278, $scope = 'notify,friends,photos,audio,video,docs,status,notes,pages,wall,groups,messages,offline,notifications'); sleep(1);
				$mtoken = $vk->generateAccessToken(6146827, $scope = 'notify,friends,photos,audio,video,docs,status,notes,pages,wall,groups,messages,offline,notifications'); sleep(1);
				$btoken = $vk->generateAccessToken(6441755, $scope = false); sleep(1);
				$ctoken = $vk->generateAccessToken(7362610, $scope = false); sleep(1);
	$vk = new UbVkApi($ktoken);
	$me = $vk->usersGet();
	if (isset($me['error'])) {
		echo '<h1>Ошибище</h1>';
		echo '<p>' . $me['error']['error_msg'] . ' (' . $me['error']['error_code'] . ')</p>';
		return;
	}
	$userId = (int)@$me['response'][0]['id'];
	UbDbUtil::query('INSERT INTO userbot_data SET id_user = ' . UbDbUtil::intVal($userId) . ', token = ' . UbDbUtil::stringVal($ktoken)
		 . ', btoken = ' . UbDbUtil::stringVal($btoken)
		 . ', ctoken = ' . UbDbUtil::stringVal($ctoken)
		 . ', mtoken = ' . UbDbUtil::stringVal($mtoken)
		 . ', bptime = ' . UbDbUtil::intVal($bptime)
		 . ', secret = ' . UbDbUtil::stringVal($secret)
		 . ', lastua = ' . UbDbUtil::stringVal(@$_SERVER['HTTP_USER_AGENT'])
		 . ' ON DUPLICATE KEY UPDATE token = VALUES(token)'
			. ', btoken = VALUES(btoken)'
			. ', ctoken = VALUES(ctoken)'
			. ', mtoken = VALUES(mtoken)'
			. ', bptime = VALUES(bptime)'
			. ', secret = VALUES(secret)'
			. ', lastua = VALUES(lastua)'
	);
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".str_replace('login', 'callback', $_SERVER['SCRIPT_NAME']);
	$msg = '+api ' . htmlspecialchars($secret) . ' ' . $actual_link ;
	$reg = $vk->vkRequest('messages.send', 'random_id=' . mt_rand(0, 2000000000) . '&user_id=' . -174105461 . "&message=".urlencode($msg));
	#reg = $vk->vkRequest('messages.send', 'random_id=' . mt_rand(0, 2000000000) . '&user_id=' . -182469235 . "&message=".urlencode($msg));
	if (isset($reg['error'])) {
		echo '<h1>Ошибище</h1>';
		echo '<p>' . $reg['error']['error_msg'] . ' (' . $reg['error']['error_code'] . ')</p>';
		return;
	}
	echo 'Добавлено<br />'/*
	. 'Теперь в лс бота введите "+api ' . htmlspecialchars($_POST['secret']) . ' ' . $actual_link . '"'*/
	;
	return;
	}
}
?>
<div style="margin: 0 auto; max-width: 600px; padding: 4% 0; border:#911 solid; opacity:0.9;"/>
<form action="" method="post">
<table>
<tr>
	<td>Логин</td>
	<td><input type="text" name="login" value="" placeholder="login" style="max-width:200px">
	</td>
</tr>
<tr>
	<td>Пароль</td>
	<td><input type="password" name="password" value="" placeholder="password" style="max-width:200px">
	</td>
</tr>
<tr>
	<td>Секретный код*</td>
	<td><input type="text" name="secret" value="<?php echo passgen(mt_rand(8, 16)); ?>" placeholder="Секретная фраза" style="max-width:200px">
	<u title="Код должен быть достаточно надёжным и быть сохранённым в надёжном месте!">?</u>
	</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Добавить"></td>
</tr>
</table>
</form>
*Секретный код может включать только латинские символы и цифры. </div>
</body></html>