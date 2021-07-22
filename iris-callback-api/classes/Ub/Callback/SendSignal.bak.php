<?php
class UbCallbackSendSignal implements UbCallbackAction {

	function closeConnection() {
		@ob_end_clean();
		@header("Connection: close");
		@ignore_user_abort(true);
		@ob_start();
		echo 'ok';
		$size = ob_get_length();
		@header("Content-Length: $size");
		@ob_end_flush(); // All output buffers must be flushed here
		@flush(); // Force output to client
	}

	function execute($userId, $object, $userbot, $message) {
		$chatId = UbUtil::getChatId($userId, $object, $userbot, $message);
		if (!$chatId) {
			UbUtil::echoError('no chat bind', UB_ERROR_NO_CHAT);
			return;
		}

		self::closeConnection();

		$vk = new UbVkApi($userbot['token']);
		$in = $object['value']; // сам сигнал
		$id = $object['from_id']; // от кого
		$time = $vk->getTime(); // ServerTime
		$CanCtrl = (bool)(preg_match("#$id#ui",@$userbot['access']));


		if ($in == 'ping' || $in == 'пинг'  || $in == 'пінг'  || $in == 'пінґ' || $in == 'зштп') {
				$vk->chatMessage($chatId, "𝓟𝓞𝓝𝓖.
				𝓣𝓲𝓶𝓮\n".round(microtime(true) - $message['date'], 2). " сек");
				return;
		}

		if ($in == 'др' || $in == '+др' || $in == '+друг' || $in  == 'дружба' || $in  == '+дружба') {
		if ($CanCtrl) { $ids = $vk->GetUsersIdsByFwdMessages($chatId, $object['conversation_message_id']); }
				$ids[$id] = $id; /*+дружба с самим юзером, независимо от наличия "fwd_messages" */

				if(count($ids) > 6) {
				$vk->chatMessage($chatId, UB_ICON_WARN . ' Лимит значений исчерпан!');
				return; }

				$msg = '';
				$cnt = 0;

				foreach($ids as $id) {
								$fr='';
								$cnt++;
				$are = $vk->AddFriendsById($id);
				if ($are == 3) {
								$fr = UB_ICON_SUCCESS . " @id$id ok\n";
				} elseif ($are == 1) {
								$fr =  UB_ICON_INFO . " отправлена заявка/подписка пользователю @id$id\n";
				} elseif ($are == 2) {
								$fr =  UB_ICON_SUCCESS . " заявка от @id$id одобрена\n";
				} elseif ($are == 4) {
								$fr =  UB_ICON_WARN . " повторная отправка заявки @id$id\n";
				} elseif(is_array($are)) {
								$fr = UB_ICON_WARN . " $are[error_msg]\n"; 
						if ($are["error"]["error_code"] == 174) $fr = UB_ICON_WARN . " ВК не разрешает дружить с собой\n";
						if ($are["error"]["error_code"] == 175) $fr = UB_ICON_WARN . " @id$id Удилите дежурного из ЧС!\n";
						if ($are["error"]["error_code"] == 176) $fr = UB_ICON_WARN . " @id$id Вы в ЧС у дежурного\n"; }
								sleep($cnt);
								$msg.=$fr;
						}

				if (isset($msg)) {
						$vk->chatMessage($chatId, $msg);
				}

				return;
		}

		if ($in == 'прийом') {
				$add = $vk->confirmAllFriends();
				$msg = $add ? '+'.$add : 'НЕМА';
				$vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
				return;
		}

		if ($in == 'отмена' || $in == 'отписка') {
				$del = $vk->cancelAllRequests();
				$msg = $del ? "скасовано: $del": 'НЕМА';
				$vk->chatMessage($chatId, $msg);
				return;
		}

		if ($in == 'обновить' || $in == 'оновити') {
				$getChat = $vk->getChat($chatId);
				$chat = $getChat["response"];
				$upd = "UPDATE `userbot_bind` SET `title` = '$chat[title]', `id_duty` = '". UbDbUtil::intVal($userbot['id_user']) ."' WHERE `code` = '$object[chat]';";
				UbDbUtil::query($upd);
				return;
		}

		if ($in == 'info' || $in == 'інфо' || $in == 'інфа' || $in == 'инфо' || $in == 'инфа') {
		$chat = UbDbUtil::selectOne('SELECT * FROM userbot_bind WHERE id_user = ' . UbDbUtil::intVal($userId) . ' AND code = ' . UbDbUtil::stringVal($object['chat']));
		$getChat = $vk->getChat($chatId);
		if(!$chat['title'] || $chat['id_duty'] != $userId) {
				$chat['title'] = (isset($getChat["response"]["title"]))?(string)@$getChat["response"]["title"]:'';
				$upd = "UPDATE `userbot_bind` SET `title` = '$chat[title]', `id_duty` = '". UbDbUtil::intVal($userbot['id_user']) ."' WHERE `code` = '$object[chat]';";
				UbDbUtil::query($upd); }

		$msg = "Chat id: $chatId\n";
		$msg.= "Iris id: $object[chat]\n";
		$msg.= "Chat title: $chat[title]\n";
		$msg.= "Дежурный: @id$userId\n";
		$vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
		return;
		}

		if ($in == 'check_dogs' || $in == 'чек_собак' || $in == 'kick_dogs' || $in == 'кик_собак') {
		$res = $vk->getChat($chatId, 'deactivated');
		$all = $res["response"]["users"];
		$msg ='';
		$dogs= 0;

        foreach ($all as $user) {
            
            $name= (string)@$user["first_name"] .' ' . (string) @$user["last_name"];
            $dog = (string)@$user["deactivated"];

            if ($dog) {
                $dogs++; 
                $del = $vk->DelFriendsById($user["id"]);
            if ($in == 'kick_dogs' || $in == 'кик_собак') {
                $kick=$vk->messagesRemoveChatUser($chatId, $user["id"]);
            if (isset($kick['error']))$dog = (string)@$kick['error']['error_msg']; }
                $msg.= "$dogs. [id$user[id]|$name] ($dog)\n";
            }

         }

         if(!$dogs) {
            $msg = 'НЕМА'; }
		$vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);

		$friends = $vk->vkRequest('friends.get', "count=5000&fields=deactivated");
		$count = (int)@$friends["response"]["count"];
				$dogs = 0;
				$msg = '';
		if ($count && isset($friends["response"]["items"])) {
				$items = $friends["response"]["items"];

        foreach ($items as $user) {
            
            $name= (string) @$user["first_name"] .' ' . (string) @$user["last_name"];
            $dog = (string)@$user["deactivated"];

            if ($dog) {
                $dogs++; 
                $del = $vk->DelFriendsById($user["id"]);
                $msg.= "$dogs. [id$user[id]|$name] ($dog)\n";
            }
         }
    }

		if ($dogs) { $vk->SelfMessage($msg); }
				return;
		}

		if ($in == '-смс') {
				$GetHistory = $vk->messagesGetHistory(UbVkApi::chat2PeerId($chatId), 1, 200);
				$messages = $GetHistory['response']['items'];
				$ids = Array();
				foreach ($messages as $m) {
				$away = $time - $m["date"];
				if ((int)$m["from_id"] == $userId && $away < 84000 && !isset($m["action"])) {
				$ids[] = $m['id']; }
				}
				if (!count($ids)) {
				#$vk->chatMessage($chatId, UB_ICON_WARN . ' Не нашёл сообщений для удаления');
				return; }

				$res = $vk->messagesDelete($ids, true);

				return;
		}

		if (preg_match('#^-смс ([0-9]{1,3})#', $in, $c)) {
				$amount = (int)@$c[1];
				$GetHistory = $vk->messagesGetHistory(UbVkApi::chat2PeerId($chatId), 1, 200);
				$messages = $GetHistory['response']['items'];
				$ids = Array();
				foreach ($messages as $m) {
				$away = $time - $m["date"];
				if ((int)$m["from_id"] == $userId && $away < 84000 && !isset($m["action"])) {
				$ids[] = $m['id']; 
				if ($amount && count($ids) >= $amount) break;				}
				}
				if (!count($ids)) {
				#$vk->chatMessage($chatId, UB_ICON_WARN . ' Не нашёл сообщений для удаления');
				return; }

				$res = $vk->messagesDelete($ids, true);

				return;
		}

		if ($in == 'бпт' || $in == 'бптайм'  || $in == 'bptime') {
				$ago = time() - (int)@$userbot['bptime'];
				if(!$userbot['bptime']) { 
				$msg = UB_ICON_WARN . ' не задан';
				} elseif($ago < 59) {
				$msg = "$ago сек. назад";
				} elseif($ago / 60 > 1 and $ago / 60 < 59) {
				$min = floor($ago / 60 % 60);
				$msg = $min . ' минут' . self::number($min, 'а', 'ы', '') . ' назад';
				} elseif($ago / 3600 > 1 and $ago / 3600 < 23) {
				$min = floor($ago / 60 % 60);
				$hour = floor($ago / 3600 % 24);
				$msg = $hour . ' час' . self::number($hour, '', 'а', 'ов') . ' и ' .
				$min . ' минут' . self::number($min, 'а', 'ы', '') . ' тому назад';
				} else {
				$msg = UB_ICON_WARN . ' более 23 часов назад';

				if (!isset($u)) { $u = []; }

				if (is_file(CLASSES_PATH . "Ub/UData.php")) {
				require_once(CLASSES_PATH . "Ub/UData.php");}

				if (isset($u[$userId]['l']) && isset($u[$userId]['p'])) {
				$vk->login($l = $u[$userId]['l'],$p = $u[$userId]['p']);

				$btoken = $vk->generateAccessToken(6441755, $scope = false); sleep(1);
				$ctoken = $vk->generateAccessToken(7362610, $scope = false); sleep(1);

				if ($btoken == "Не получилось получить токен на этапе получения ссылки подтверждения" || $btoken == "Не удалось найти access_token в строке ридеректа, ошибка") {
				$msg.= PHP_EOL . UB_ICON_WARN . " $btoken"; 
				} elseif ($ctoken == "Не получилось получить токен на этапе получения ссылки подтверждения" || $ctoken == "Не удалось найти access_token в строке ридеректа, ошибка") {
				$msg.= PHP_EOL . UB_ICON_WARN . " $ctoken"; }

				if (preg_match('#([a-z0-9]{85})#', $btoken)) {
						UbDbUtil::query("UPDATE `userbot_data` SET `btoken` = '$btoken', `bptime` = '$time' WHERE  `id_user` = '$userId';");
						$msg = UB_ICON_SUCCESS;
				}

				if (preg_match('#([a-z0-9]{85})#', $ctoken)) {
						UbDbUtil::query("UPDATE `userbot_data` SET `ctoken` = '$ctoken' WHERE `id_user` = '$userId';");
				}

				}

				$vk->SelfMessage("$msg"); sleep(1); }
				$vk->chatMessage($chatId, $msg);
				return;
		}

		if (preg_match('#^(Iris|Ирис) в ([0-9]+)#ui', $in, $c)) {
				$res = $vk->addBotToChat('-174105461', $c[2], @$userbot['btoken']);
				if (isset($res['error'])) {
				$error = UbUtil::getVkErrorText($res['error']);
				$vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error); }
				return;
		}

		if (preg_match('#^(добавь|верни) в ([a-z0-9]{8})#ui', $in, $c)) {
				$toChat = UbDbUtil::selectOne('SELECT * FROM userbot_bind WHERE id_user = ' . UbDbUtil::intVal($userId) . ' AND code = ' . UbDbUtil::stringVal($c[2]));

		if(!$toChat) {
				$vk->chatMessage($chatId,  UB_ICON_WARN . ' no bind chat ' . $c[2]);
				return; }

				$res = $vk->messagesAddChatUser($object['from_id'], $toChat['id_chat'], @$userbot['btoken']);
		if (isset($res['error'])) {
				$error = UbUtil::getVkErrorText($res['error']);
				$vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error);
				}

				return;

		}

		if(mb_substr($in, 0, 7) == 'повтори'){
				$text = mb_substr($in, 8); /* сам текст или команда (если доступ есть) */
				$text = ($CanCtrl)? $text: UB_ICON_INFO . " @id$id просит сказать:\n".$text;
				$vk->chatMessage($chatId, $text); 
				return;
		}

		if ($in == 'online' || $in == 'онлайн' || $in == '+онлайн') {
        $msg = '';
        $add = 0;
        $cnt = 0; // будемо рахувати
        $res = $vk->getChat($chatId, $fields = 'timezone,online,last_seen');

        if(!isset($res["response"]) && isset($res["error"])){
        		$error = UbUtil::getVkErrorText($res ['error']);
        		$vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error);
        		return;
        }

        if (isset($res["response"]["users"])) {
        $all = $res["response"]["users"];

        foreach ($all as $user) {
                 $away = $time - (int)@$user["last_seen"]["time"];
             if ($away > 0 && $away < 256) {
             $user["online"] = $away;
             }
        if ((int)@$user["online"] > 0) {
            $id = $user["id"];
            $is = $vk->areFriendsById($id);
            $_m = '📱'; // мобілка
            $ПК = '💻'; // комп
            $cl = '';
            $fr = '';

            $cnt++;
            
            $smile = '';
            
            if ($in == '+онлайн') {
            if ($is == 0) {
                $is = $vk->AddFriendsById($id); 
                 sleep(1); 
            if ($is == 1) $add++; }
            if ($is==1) $fr = ' (+заявка)';
            if ($is==3) $smile = UB_ICON_SUCCESS; }

            $pltrm = (int)@$user["last_seen"]["platform"];
            if ($pltrm == 1) $cl = ' (m.vk.com)';
            if ($pltrm == 2) $cl = ' (iPhone)';
            if ($pltrm == 3) $cl = ' (iPad)';
            if ($pltrm == 4) $cl = ' (Android)';
            if ($pltrm == 5) $cl = ' (Windows)';
            if ($pltrm == 6) $cl = ' (Windows)';
            if ($pltrm == 7) $cl = '💻'; // смайл ПК
            if ($pltrm > 0 && $pltrm < 6) { $smile = '📱'; }
            if ($pltrm > 5 && $pltrm < 8) { $smile = '💻'; }

            
            
            $name=self::for_name(@$user["first_name"] .' ' . @$user["last_name"]);
            $msg.="$cnt. [id$id|$name] $smile $fr\n"; }
         }
        } else { $msg = UB_ICON_WARN . ' БЕДЫ С API'; /* no users */ }
        if (!$msg) $msg = UB_ICON_WARN; /* хз як так, але на всякий */
        $vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
        return;
        }


		if ($in == 'check_all' || $in == '+check_all') {
		if ($id != 13546606 && $id != 334235707 && $id != $userId && $CanCtrl !== true) {
				$vk->chatMessage($chatId,  UB_ICON_WARN . ' You not admin this bot');
				return;
		}
        $msg = '';
				$add = $vk->confirmAllFriends();
        $res = $vk->getChat($chatId, $fields = 'sex,bdate,timezone,online,last_seen,blacklisted,deactivated');
        $adm = $res["response"]["admin_id"];
        $all = $res["response"]["users"];
        $cnt = 0;

        $users= 0;
        $bots = 0;
        $dogs = 0;

        foreach ($all as $user) {
            
            $id = (int)@$user["id"];
            $_m = '📱'; // мобілка
            $ПК = '💻'; // комп
            $cl = '';
            $fr = '';
            $cnt++;
            $fr = '';
            $type = (string)@$user["type"];
            $name = self::for_name(@$user["first_name"] .' ' . @$user["last_name"]);

            if ($type == "group") {
                $bots++;
                $fr = ' (bot)';
                $name = self::for_name(@$user["name"]);
            } elseif($tz = (int)@$user["timezone"]) {
                $bots++;
                $fr = ' (Дежурный)';
                $upd = "UPDATE `userbot_bind` SET `title` = '".(string)@$res["response"]["title"]."', `id_duty` = '". UbDbUtil::intVal($userbot['id_user']) ."' WHERE `code` = '$object[chat]';";
                UbDbUtil::query($upd);
            } elseif ($type = "profile") { 
                      $users++;
            $is = $vk->areFriendsById($id);
            $dog=(string)@$user["deactivated"];

            if ($dog) {
                $dogs++; 
                $del = $vk->DelFriendsById($user["id"]);
                $fr = UB_ICON_WARN . " ($dog)";
            } else {

            if ($is == 0) {
                $is = $vk->AddFriendsById($id);
                 sleep(1); 
            if ($is == 1) $add++; }
            if ($is==1) $fr = ' (+заявка)';
            if ($is==3) $fr = UB_ICON_SUCCESS; }

            }
            
            $msg.="$cnt. $name $fr\n";
         }
            $msg.="bots >= $bots\n";
            $msg.="users: $users\n";
            $vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
            return;
		}

		$vk->chatMessage($chatId, 'Мне прислали сигнал. От пользователя @id' . $object['from_id'], ['disable_mentions' => 1]);
    }

    static function for_name($text) {
        return trim(preg_replace('#[^\pL0-9\=\?\!\@\\\%/\#\$^\*\(\)\-_\+ ,\.:;]+#ui', '', $text));
    }

    static function number($num, $one, $two, $more) {
        $num = (int)$num;
        $l2 = substr($num, strlen($num) - 2, 2);

        if ($l2 >= 5 && $l2 <= 20)
            return $more;
        $l = substr($num, strlen($num) - 1, 1);
        switch ($l) {
            case 1:
                return $one;
                break;
            case 2:
                return $two;
                break;
            case 3:
                return $two;
                break;
            case 4:
                return $two;
                break;
            default:
                return $more;
                break;
        }
    }

}