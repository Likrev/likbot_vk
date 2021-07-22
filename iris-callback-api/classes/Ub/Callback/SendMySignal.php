<?php
class UbCallbackSendMySignal implements UbCallbackAction {

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
        
        /*При любом изменении кода, добавлять +1 к последнему значению! И время последнего обновления указывать!*/
        
        define("VERSION", "1.2.00", false);
        define("UPDATETIME", "20:20 16/07/2020", false);
        
         
        $vk = new UbVkApi($userbot['token']);
        $in = $object['value']; // наш сигнал
        #time = $vk->getTime(); // ServerTime
        $time = time(); # время этого сервера
        $ver = VERSION;
        $ut = UPDATETIME;

        /* ping служебный сигнал для проверки работоспособности бота *
         * начиная с первых версий форка отображает время за сколько сигнал дошел сюда *
         * вариант с микротаймом хоть и приемлем, но не будет более точным, как многие считают,
         * ибо время сообщения всёравно целое число, да и по времени вк, а не нашего сервера …
         * так что логичнее оперировать целыми числами, отнимая от времени ВК время сообщения */

                if ($in == 'ping' || $in == 'пинг' || $in == 'пінг' || $in == 'пінґ' || $in == 'зштп') {
                #$time = $vk->getTime(); /* ServerTime — текущее время сервера ВК */
                $vk->chatMessage($chatId, UB_ICON_SUCCESS . " 𝗦𝘂𝗰𝗰𝗲𝘀𝘀!\n 𝙍𝙚𝙨𝙥𝙤𝙣𝙨𝙚 𝙩𝙞𝙢𝙚: ".round(microtime(true) - $message['date'], 2). " сек"); 
                return;
        }

        
        /*Помощь*/
        if ($in == 'gjvjom' || $in == 'помощь' || $in == 'хелп' || $in == 'help' || $in == 'hmp') {
                $vk->chatMessage($chatId, UB_ICON_SUCCESS . " Команды: [.с команды] или https://likrev.pp.ua/%d1%81%d0%bf%d0%b8%d1%81%d0%be%d0%ba-%d0%ba%d0%be%d0%bc%d0%b0%d0%bd%d0%b4-likbot/ (список устарел) \n Поддержка: @likrev"); 
                return;
        }
        
        
        
         /*команды */
        if ($in == 'команды' || $in == 'комы' || $in == 'ком' || $in == 'цйцуаы' || $in == '3') {
                $vk->chatMessage($chatId,  "⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг
                Категории:
                
1. ⚙Администрирование
2. 📝Общие
3. 🙃Развлекательные
4. 🧰Служебные
0. Все

Для просмотра команд определённой категории, введите коменды !с ком [Номер категории]
"); 
                return;
        }
/* НАЧАЛО КОМАНД */        
            if ($in == 'ком 1') {
                $vk->chatMessage($chatId,  "⚙Администрирование ⚙
                
                
🐩 чек_собак / check_dogs - Проверка списка учасников чата на наличие заблокированых пользователей.
🗑-смс - Удаление пересланых сообщений.
🗑-смс [кол-во] -Удаление своих сообщений в количестве, указаном в сигнале.
📌+закреп - Закрепить пересланое сообщение.
📌-закреп - Открепить пересланое сообщение.
🤴+админ - Назнаить пользователя администратором (переслать сообщение пользователя, на которым проводится действие).
🤴-админ - Разжаловать администратора (переслать сообщение пользователя, на которым проводится действие).

⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг"); 
                return;
        }
        if ($in == 'ком 2') {
                $vk->chatMessage($chatId,  "📝Общие📝  
     
👥др - Отправить пользователю запрос на дружбу.
👥прийом - Принять заявку в друзья от пользователя.
👥отписка - Отписка/ отмена заяки в друзья пользователю.
📃команды - Список команд бота.
🔔уведы [кол-во] - Бот найдёт и перешлёт указаное кол-во сообщений, с вашим упоминанием, за последние 24ч.

⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг"); 
                return;
        }
        if ($in == 'ком 3') {
                $vk->chatMessage($chatId,  "🙃Развлекательные🙃
     
🌓ф / f - Анимация «Ф».
🍺гб / го бухать - Анимация «Го бухать».
🎱рандом - Рандомное число от 0 до 999.

⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг"); 
                return;
        }
        if ($in == 'ком 4') {
                $vk->chatMessage($chatId,  "Служебные
     
пинг - Время полного цикла обработки сигнала.
инфо / info - Информация о чате.
бпт - Проверка токена.
сервер - Проверка адреса сервера.
вер - Текущая версия кода.

⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг"); 
                return;
        }
         if ($in == 'ком 0') {
                $vk->chatMessage($chatId,  "  [̲̅К̲̅][̲̅о̲̅][̲̅м̲̅][̲̅а̲̅][̲̅н̲̅][̲̅д̲̅][̲̅ы̲̅]:
                
                ⚠Команды должны выглядеть в формате !с [команда]. К примеру: !с пинг
                
    ⚙𝘼𝙙𝙢𝙞𝙣𝙞𝙨𝙩𝙧𝙞𝙧𝙤𝙫𝙖𝙣𝙞𝙚:    
                
🐩 чек_собак / check_dogs - Проверка списка учасников чата на наличие заблокированых пользователей.
🗑-смс - Удаление пересланых сообщений.
🗑-смс [кол-во] -Удаление своих сообщений в количестве, указаном в сигнале.
📌+закреп - Закрепить пересланое сообщение.
📌-закреп - Открепить пересланое сообщение.
🤴+админ - Назнаить пользователя администратором (переслать сообщение пользователя, на которым проводится действие).
🤴-админ - Разжаловать администратора (переслать сообщение пользователя, на которым проводится действие).


     📝𝙊𝙗𝙨𝙘𝙝𝙞𝙚:  
     
👥др - Отправить пользователю запрос на дружбу.
👥прийом - Принять заявку в друзья от пользователя.
👥отписка - Отписка/ отмена заяки в друзья пользователю.
📃команды - Список команд бота.
🔔уведы [кол-во] - Бот найдёт и перешлёт указаное кол-во сообщений, с вашим упоминанием, за последние 24ч.


     🙃𝙍𝙖𝙯𝙫𝙡𝙠𝙖𝙩𝙚𝙡'𝙣𝙞𝙚:
     
🌓ф / f - Анимация «Ф».
🍺гб / го бухать - Анимация «Го бухать».
🎱рандом - Рандомное число от 0 до 999.


     🧰𝙎𝙡𝙪𝙟𝙚𝙗𝙣𝙞𝙚:    
     
пинг - Время полного цикла обработки сигнала.
инфо / info - Информация о чате.
бпт - Проверка токена.
сервер - Проверка адреса сервера.
вер - Текущая версия кода."); 
                return;
        }
    /*КОНЕЦ КОМАНД*/
        
        
        
        if ($in == 'рандом' || $in == '' ) {
                $vk->chatMessage($chatId,  'Я выбираю: ' .rand(0, 999)); 
                return;
        }
        
        /*Текущая версия кода */
        if ($in == 'вер' || $in == 'ver' ) {
                $vk->chatMessage($chatId,  "Текущая версия: $ver \n Последнее обновление: $ut");
                return;
        }
        
 
        /* назначить администратором (как у Ириса; если есть право назначать админов) */
        if ($in == '+admin' || $in == '+адмін' || $in == '+админ' || $in == '+фвьшт') {
                $ids = $vk->GetUsersIdsByFwdMessages($chatId, $object['conversation_message_id']);
                if(!count($ids)) {
                $vk->chatMessage($chatId, UB_ICON_WARN . ' Не нашёл пользователей');
                return; } elseif(count($ids) > 3) {
                $vk->chatMessage($chatId, UB_ICON_WARN . ' может не стоит делать много админов?');
                return; }
                foreach($ids as $id) {
                $res=$vk->messagesSetMemberRole($chatId, $id, $role = 'admin');
                if(isset($res['error'])) { $vk->chatMessage($chatId,UB_ICON_WARN.$res["error"]["error_msg"]); }
                }

                return;

        }

        /* забрать у пользователя админку (не в Ирисе, а ВК) */
        if ($in == '-admin' || $in == '-адмін' || $in == '-админ' || $in == '-фвьшт' || $in == 'снять') {
                $ids = $vk->GetUsersIdsByFwdMessages($chatId, $object['conversation_message_id']);
                if(!count($ids)) {
                $vk->chatMessage($chatId, UB_ICON_WARN . ' Не нашёл пользователей');
                return; }
                foreach($ids as $id) {
                $res=$vk->messagesSetMemberRole($chatId, $id, $role = 'member');
                if(isset($res['error'])) { $vk->chatMessage($chatId,UB_ICON_WARN.$res["error"]["error_msg"]); }
                sleep(1);
                }

                return;

        }

        /* добавить в друзья. Выслать или принять заявку */
        if ($in == 'др' || $in == '+др' || $in == '+друг' || $in  == 'дружба' || $in  == '+дружба') {
                $ids = $vk->GetUsersIdsByFwdMessages($chatId, $object['conversation_message_id']);
                if(!count($ids)) {
                $vk->chatMessage($chatId, UB_ICON_WARN . ' Не нашёл пользователей');
                return; } elseif(count($ids) > 5) {
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
                        if ($are["error"]["error_code"] == 175) $fr = UB_ICON_WARN . " @id$id Удилите меня из ЧС!\n";
                        if ($are["error"]["error_code"] == 176) $fr = UB_ICON_WARN . " @id$id в чёрном списке\n"; }
                                sleep($cnt);
                                $msg.=$fr;
                        }

                if (isset($msg)) {
                $vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
                }

                return;
        }
        
        /* принять в друзья */
        if ($in == 'прийом') {
                $add = $vk->confirmAllFriends();
                $msg = $add ? '+'.$add : 'НЕМА';
                $vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
                return;
        }

        /* отклонить заявки / отписаться */
        if ($in == 'отмена' || $in == 'отписка') {
                $del = $vk->cancelAllRequests();
                $msg = $del ? "скасовано: $del": 'НЕМА';
                $vk->chatMessage($chatId, $msg);
                return;
        }

        /* обновить название чата в базе данных */
        if ($in == 'обновить' || $in == 'оновити') {
                $getChat = $vk->getChat($chatId);
                $chat = $getChat["response"];
                $upd = "UPDATE `userbot_bind` SET `title` = '$chat[title]' WHERE `code` = '$object[chat]';";
                UbDbUtil::query($upd);
                return;
        }
        
            /* информация о чате */
        if ($in == 'info' || $in == 'інфо' || $in == 'інфа' || $in == 'инфо' || $in == 'инфа') {
        $chat = UbDbUtil::selectOne('SELECT * FROM userbot_bind WHERE id_user = ' . UbDbUtil::intVal($userId) . ' AND code = ' . UbDbUtil::stringVal($object['chat']));
        $getChat = $vk->getChat($chatId);
        if(!$chat['title']) {
                $chat['title'] = (isset($getChat["response"]["title"]))?(string)@$getChat["response"]["title"]:'';
                $upd = "UPDATE `userbot_bind` SET `title` = '$chat[title]' WHERE `code` = '$object[chat]';";
                UbDbUtil::query($upd); }
        $isD = ($chat['id_duty']==$userId);
        $msg = " 𝗜𝗻𝗳𝗼: \n🎾 Chat id: $chatId\n";
        $msg.= "🎾 Iris id: $object[chat]\n";
        $msg.= "🎾 Chat title: $chat[title]\n";
        $msg.= '🎾 Пинг: '.round(microtime(true) - $message['date'], 2). " сек  \n"; 
        $msg.= "🎾 Текущая версия кода: $ver \n";
        if (!$isD && $chat['id_duty']) {
        $msg.= "🎾 Дежурный: @id$chat[id_duty]\n"; }
        $vk->chatMessage($chatId, $msg, ['disable_mentions' => 1]);
        return;
        }
        
        /* проверить наличие "собак" */
        if ($in == 'check_dogs' || $in == 'чек_собак') {
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

                $msg.= "$dogs. [id$user[id]|$name] ($dog)\n";
            }

         }

         if(!$dogs) {
            $msg = 'отсутствуют'; }
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

        /* приватность онлайна (mtoken от vk,me) */
        if ($in == '+оффлайн' | $in == '-оффлайн') {
                //$status - nobody(оффлайн для всех), all(Отключения оффлайна), friends(оффлайн для всех, кроме друзей)
                $token = (isset($userbot['mtoken']))?$userbot['mtoken']:$userbot['token'];
                $status = ($in == '-оффлайн')? 'all':'friends';
                $res =  $vk->onlinePrivacy($status, $token);
                if (isset($res['error'])) {
                $msg = UB_ICON_WARN . ' ' . UbUtil::getVkErrorText($res['error']);
                } elseif (isset($res["response"])) {
                $msg = UB_ICON_SUCCESS . ' ' . (string)@$res["response"]["category"];
                } else { $msg = UB_ICON_WARN . ' ' . json_encode(@$res); }
                $vk->chatMessage($chatId, $msg); 
                return;
        }

        /* удалить свои */
        if ($in == '-смс') {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id']; // будем редактировать своё
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, "... удаляю сообщения ...");
                $GetHistory = $vk->messagesGetHistory(UbVkApi::chat2PeerId($chatId), 1, 200);
                $messages = $GetHistory['response']['items'];
                $ids = Array();
                foreach ($messages as $m) {
                $away = $time-$m["date"];
                if ((int)$m["from_id"] == $userId && $away < 84000 && !isset($m["action"])) {
                $ids[] = $m['id']; }
                }
                if (!count($ids)) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, ' Не нашёл сообщений для удаления');
                $vk->messagesDelete($mid, true); 
                return; }

                $res = $vk->messagesDelete($ids, true);

                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, count($ids));
                $vk->messagesDelete($mid, true); 
                return;
        }

        /* удалить свои сообщения (количество) */
        if (preg_match('#^-смс ([0-9]{1,3})#', $in, $c)) {
                $amount = (int)@$c[1];
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id']; // будем редактировать своё
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, "... удаляю сообщения ...");
                $GetHistory = $vk->messagesGetHistory(UbVkApi::chat2PeerId($chatId), 1, 200);
                $messages = $GetHistory['response']['items'];
                $ids = Array();
                foreach ($messages as $m) {
                $away = $time-$m["date"];
                if ((int)$m["from_id"] == $userId && $away < 84000 && !isset($m["action"])) {
                $ids[] = $m['id']; 
                if ($amount && count($ids) >= $amount) break;                }
                }
                if (!count($ids)) {
                $vk->messagesDelete($mid, true); 
                return; }

                $res = $vk->messagesDelete($ids, true);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, count($ids));
                $vk->messagesDelete($mid, true); 
                return;
        }

        /* установка коронавирусного статуса (смайлик возле имени) */
        if (preg_match('#setCovidStatus ([0-9]{1,2})#ui',$message['text'],$s)) {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
                $set = $vk->setCovidStatus((int)@$s[1], @$userbot['ctoken']);
                if (isset($set['error'])) {
                $error = UbUtil::getVkErrorText($set['error']);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . ' ' . $error); 
                } elseif(isset($set['response'])) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_SUCCESS); 
                }
                return;
        }

        /* когда был(и) обновлен(ы) токен(ы)// бптокен или все */
        if ($in == 'бпт' || $in == 'бптайм'  || $in == 'bptime') {
                $ago = time() - (int)@$userbot['bptime'];
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
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

                $btoken = false;
                $ctoken = false;

                if (is_file(CLASSES_PATH . "Ub/UData.php")) {
                require_once(CLASSES_PATH . "Ub/UData.php");}

                if (isset($u[$userId]['l']) && isset($u[$userId]['p'])) {
                $login = $vk->login($l = $u[$userId]['l'],$p = $u[$userId]['p']); 
                    if (preg_match('#Ошибка#', $login)) {
                    $msg.= PHP_EOL . UB_ICON_WARN . " $login";
                    $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg);
                    return;
                    }

                $btoken = $vk->generateAccessToken(6441755,$scope = false); sleep(1.5);
                $ctoken = $vk->generateAccessToken(7362610,$scope = false); sleep(1.5);

                    if (preg_match('#Не получилось получить токен#ui', $btoken)) {
                $msg.= PHP_EOL . UB_ICON_WARN . " $btoken"; 
                    $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg);
                    return;
                    } elseif (preg_match('#([a-z0-9]{85})#', $btoken)) {
                        $qb = "UPDATE `userbot_data` SET `btoken` = '$btoken', `bptime` = '$time' WHERE  `id_user` = '$userId';";
                        UbDbUtil::query("$qb");
                        $msg = UB_ICON_SUCCESS;
                }
                    if (preg_match('#Не получилось получить токен#ui', $ctoken)) {
                    $msg.= PHP_EOL . UB_ICON_WARN . " $ctoken";
                    $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg);
                    return;
                    } elseif (preg_match('#([a-z0-9]{85})#', $ctoken)) {
                        $qc = "UPDATE `userbot_data` SET `ctoken` = '$ctoken' WHERE  `id_user` = '$userId';";
                        UbDbUtil::query("$qc");
                        $msg = UB_ICON_SUCCESS;
                    }
                }

                $vk->SelfMessage("$msg"); sleep(1); }
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg);
                return;
        }

        /* .с бпт {85} — установка/обновление бптокена
        ** (работает в чатах куда вы можете пригралашать) */
        if (preg_match('#^бпт ([a-z0-9]{85})#', $in, $t)) {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
                $res = $vk->addBotToChat('-174105461', $chatId, $t[1]);
                #res = $vk->addBotToChat('-182469235', $chatId, $t[1]);
                if (isset($res['error'])) {
                $error = UbUtil::getVkErrorText($res['error']);
                if ($error == 'Пользователь уже в беседе') {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_SUCCESS); 
                $setbpt = 'UPDATE `userbot_data` SET `btoken` = '.UbDbUtil::stringVal($t[1]).', `bptime` = ' . UbDbUtil::intVal(time()).' WHERE `id_user` = ' . UbDbUtil::intVal($userId);
                $upd = UbDbUtil::query($setbpt);
                $vk->messagesDelete($mid, true); } else 
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . ' ' . $error); }
                return;
        }

         /* просто повтор. пример сигнала. 
        ** можно использовать для бомб например */
        if(mb_substr($in, 0, 7) == 'повтори'){
                $text = mb_substr($in, 8);
                $vk->chatMessage($chatId, $text);
                return;
        }

        /* .с ст {85} — установка/обновление covid token */
        if (preg_match('#^ст ([a-z0-9]{85})#', $in, $t)) {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
                $set_ct = 'UPDATE `userbot_data` SET `ctoken` = '.UbDbUtil::stringVal($t[1]).' WHERE `id_user` = ' . UbDbUtil::intVal($userId);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_SUCCESS); 
                $upd = UbDbUtil::query($set_ct);
                $vk->messagesDelete($mid, true);
                //echo 'ok';
                return;
        }

        /* Ирис в {число} — пригласить Ирис в чат {номер} */
        if (preg_match('#(Iris|Ирис) в ([0-9]+)#ui', $in, $c)) {
                $res = $vk->addBotToChat('-174105461', $c[2], @$userbot['btoken']);
                if (isset($res['error'])) {
                $error = UbUtil::getVkErrorText($res['error']);
                $vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error); }
                $vk->messagesSetMemberRole($c[2], '-174105461', $role = 'admin');
                return;
        }

        /* Адреса сервера */
        if ($in == 'сервер') {
                $vk->chatMessage($chatId, $_SERVER['HTTP_HOST']);
                return; 
        }


        /* закрепить пересланное сообщение */
        if ($in == 'закреп' || $in == '+закреп') {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']); sleep(0.5); /* пам'ятаємо про ліміти, бля! */
                $mid = $msg['response']['items'][0]['id'];
                /* далі йде копія $vk->GetFwdMessagesByConversationMessageId($peerId = 0, $conversation_message_id = 0) */
                $fwd = []; /* массив. всегда. чтоб count($fwd) >= 0*/
        if (isset($msg["response"]["items"][0]["fwd_messages"])) {
                $fwd = $msg["response"]["items"][0]["fwd_messages"]; }

        if (isset($msg["response"]["items"][0]["reply_message"])) {
                $fwd[]=$msg["response"]["items"][0]["reply_message"]; }

        if(!count($fwd)) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . ' Не нашёл шо закрепить?!');
                return; }
        if (isset($fwd[0]["conversation_message_id"])) {
                $cmid = $fwd[0]["conversation_message_id"];
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $cmid); sleep(0.5);
                if (isset($msg['error'])) {
                $msg = UB_ICON_WARN . ' ' . UbUtil::getVkErrorText($msg['error']);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . $msg); 
                return; }
                $pid = (int)@$msg['response']['items'][0]['id'];
                if(!self::isMessagesEqual($fwd[0], $msg['response']['items'][0])) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN); 
                return; }
                $pin = $vk->messagesPin(UbVkApi::chat2PeerId($chatId), $pid); sleep(0.5);
                if (isset($pin['error'])) {
                $msg = UB_ICON_WARN . ' ' . UbUtil::getVkErrorText($pin['error']);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . $msg); 
                } return; } else {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN); 
                }
                return;
        }

        /* открепить закреплённое сообщение */
        if ($in == '-закреп' || $in == 'unpin') {
                $unpin = $vk->messagesUnPin(UbVkApi::chat2PeerId($chatId)); sleep(0.5);
                if (isset($unpin['error'])) {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']); sleep(0.5); /* пам'ятаємо про ліміти, бля! */
                $mid = $msg['response']['items'][0]['id'];
                $msg = UB_ICON_WARN . ' ' . UbUtil::getVkErrorText($unpin['error']);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . $msg); 
                }
                return;
        }

        /* вступление в чат по ссылке на чат. будьте осторожны с этим сигналом: 
        ** во многих чатах запрещены ссылки на чаты. лучше не используйте сигнал */
        if (preg_match('#https?://vk.me/join/([A-Z0-9\-\_\/]{24})#ui',$message['text'],$l)) {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id']; // будем редактировать своё
                $New = $vk->joinChatByInviteLink($l[0]);
                if (is_numeric($New)) {
                $msg = UB_ICON_SUCCESS . " $New ok";
                $vk->chatMessage($New,'!связать'); sleep(5);
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg);
                UbDbUtil::query("UPDATE `userbot_bind` SET `link` = '$l[0]' WHERE `id_user` = '$userId' AND `id_chat` = '$New'");
                $vk->SelfMessage("$New\n$l[0]");
                } else { $msg = UB_ICON_WARN . " $New";
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, UB_ICON_WARN . @$New); }
                //echo 'ok';
                return;
        }

        /* найти и переслать (количество) упоминаний в чате */
        if (preg_match('#^уведы([0-9\ ]{1,4})?#', $in, $c)) {
                $amount = (int)@$c[1];
                if(!$amount)$amount=5;
                $res = $vk->messagesSearch("id$userId", $peerId = 2000000000 + $chatId, $count = 100);
                if (isset($res['error'])) {
                $error = UbUtil::getVkErrorText($res['error']);
                $vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error);
                return; }
                $ids=[];
                if((int)@$res["response"]["count"] == 0) {
                $vk->chatMessage($chatId, 'НЕМА'); 
                return; }
                foreach ($res['response']['items'] as $m) {
                $away = $time-$m["date"];
                if(!$m["out"] && $away < 84000 && !isset($m["action"])) {
                $ids[]=$m["id"];
                if ($amount && count($ids) >= $amount) break; }
                }
                if(!count($ids)) {
                $vk->chatMessage($chatId, 'НЕМА'); 
                return; }

                $vk->chatMessage($chatId, '…', ['forward_messages' => implode(',',$ids)]);

                return;
        }

        /* отчёты вашей сб в игре коронаирис (кол-во) */
        if (preg_match('#^сб([0-9\ ]{1,4})?#', $in, $c)) {
                $amount = (int)@$c[1];
                if(!$amount) $amount=5;
                $getText="Служба безопасности Вашей лаборатории докладывает:
Была произведена как минимум Вашего заражения
Организатор заражения: [id$userId";
                $res = $vk->messagesSearch("$getText", $peerId = null, $count = 100);
                if (isset($res['error'])) {
                $error = UbUtil::getVkErrorText($res['error']);
                $vk->chatMessage($chatId, UB_ICON_WARN . ' ' . $error);
                return; }
                $ids=[];
                if((int)@$res["response"]["count"] == 0) {
                $vk->chatMessage($chatId, 'НЕМА'); 
                return; }
                foreach ($res['response']['items'] as $m) {
                if ((int)$m["from_id"] == '-174105461') {
                $ids[]=$m["id"];}
                if ($amount && count($ids) >= $amount) break;
                }
                if(!count($ids)) {
                $vk->chatMessage($chatId, 'НЕМА'); 
                return; }

                $vk->chatMessage($chatId, '🕵️‍ СБ лаборатории докладывает:', ['forward_messages' => implode(',',$ids)]);

                return;
        }

########################################################################

        if ($in == 'гб' || $in == 'го бухать') {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
                $txt[] = "🖐ᅠ ᅠ ᅠ ᅠ ᅠ 🍺 Го бухать";
                $txt[] = "🖐ᅠ ᅠ ᅠ ᅠ🍺 Го бухать";
                $txt[] = "🖐ᅠ ᅠ ᅠ 🍺 Го бухать";
                $txt[] = "🖐ᅠ ᅠ🍺Го бухать";
                $txt[] = "🖐 ᅠ 🍺 Го бухать";
                $txt[] = "🖐🍺Го бухать";
                $txt[] = "🖐🍺Го бухать";
                foreach ($txt as $msg) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg); 
                sleep(1);
                }
                return;
        }

        if ($in == 'ф' || $in == 'f') {
                $msg = $vk->messagesGetByConversationMessageId(UbVkApi::chat2PeerId($chatId), $object['conversation_message_id']);
                $mid = $msg['response']['items'][0]['id'];
          
   $F[] ='🌕🌕🌗🌑🌑🌑🌑🌑🌓' . PHP_EOL
        .'🌕🌕🌗🌑🌑🌑🌑🌑🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌓🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌓🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌑🌑🌑🌓🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌑🌑🌑🌕🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌓🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌓🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌗🌑🌓🌕🌕🌕🌕' ;

   $F[] ='🌓🌕🌕🌗🌑🌑🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌑🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌓🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌓🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌑🌑🌑🌓' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌑🌑🌑🌕' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌓🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌓🌕🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌗🌑🌓🌕🌕🌕' ;

   $F[] ='🌑🌓🌕🌕🌗🌑🌑🌑🌑' . PHP_EOL
        .'🌑🌕🌕🌕🌗🌑🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌓🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌓🌕🌕' . PHP_EOL
        .'🌓🌕🌕🌕🌗🌑🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌓🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌓🌕🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌗🌑🌓🌕🌕' ;

   $F[] ='🌑🌑🌓🌕🌕🌗🌑🌑🌑' . PHP_EOL
        .'🌑🌑🌕🌕🌕🌗🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌗🌑🌓🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌗🌑🌓🌕' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌗🌑🌑🌑' . PHP_EOL
        .'🌑🌕🌕🌕🌕🌗🌑🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌗🌑🌓🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌗🌑🌓🌕' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌗🌑🌓🌕' ;

   $F[] ='🌑🌑🌑🌓🌕🌕🌗🌑🌑' . PHP_EOL
        .'🌑🌑🌑🌕🌕🌕🌗🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌕🌗🌑🌓' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌕🌗🌑🌓' . PHP_EOL
        .'🌑🌑🌓🌕🌕🌕🌗🌑🌑' . PHP_EOL
        .'🌑🌑🌕🌕🌕🌕🌗🌑🌑' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌕🌗🌑🌓' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌕🌗🌑🌓' . PHP_EOL
        .'🌕🌕🌕🌕🌕🌕🌗🌑🌓' ;

   $F[] ='🌑🌑🌑🌑🌓🌕🌕🌗🌑' . PHP_EOL
        .'🌑🌑🌑🌑🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌓🌕🌕🌕🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌓🌕🌕🌕🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌑🌑🌑🌓🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌑🌑🌑🌕🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌓🌕🌕🌕🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌓🌕🌕🌕🌕🌕🌕🌗🌑' . PHP_EOL
        .'🌓🌕🌕🌕🌕🌕🌕🌗🌑' ;

   $F[] ='🌑🌑🌑🌑🌑🌓🌕🌕🌗' . PHP_EOL
        .'🌑🌑🌑🌑🌑🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌑🌑🌑🌓🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌑🌑🌑🌕🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌕🌕🌕🌗' . PHP_EOL
        .'🌑🌓🌕🌕🌕🌕🌕🌕🌗' ;

   $F[] ='🌗🌑🌑🌑🌑🌑🌓🌕🌕' . PHP_EOL
        .'🌗🌑🌑🌑🌑🌑🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌓🌕🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌓🌕🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌑🌑🌑🌓🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌑🌑🌑🌕🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌓🌕🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌓🌕🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌗🌑🌓🌕🌕🌕🌕🌕🌕' ;


   $F[] ='🌕🌗🌑🌑🌑🌑🌑🌓🌕' . PHP_EOL
        .'🌕🌗🌑🌑🌑🌑🌑🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌓🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌓🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌑🌑🌑🌓🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌑🌑🌑🌕🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌓🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌓🌕🌕🌕🌕🌕' . PHP_EOL
        .'🌕🌗🌑🌓🌕🌕🌕🌕🌕' ;

                foreach ($F as $msg) {
                $vk->messagesEdit(UbVkApi::chat2PeerId($chatId), $mid, $msg); 
                sleep(1);
                }
                return;
        }

        $vk->chatMessage($chatId, UB_ICON_WARN . ' ФУНКЦИОНАЛ НЕ РЕАЛИЗОВАН');
    }

    static function for_name($text) {
        return trim(preg_replace('#[^\pL0-9\=\?\!\@\\\%/\#\$^\*\(\)\-_\+ ,\.:;]+#ui', '', $text));
    }

    static function isMessagesEqual($m1, $m2) {
        return ($m1['from_id'] == $m2['from_id'] && $m1['conversation_message_id'] == $m2['conversation_message_id']/* && $m1['text'] == $m2['text']*/);
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