<?
    const TESTING = false;

    $gameId = $_POST['gameNum'];
    
    include("../include/connection.php");
    
    //gamesInfo
    $getGameInfoQuery = mysql_query("
        SELECT 
            `number`, 
            `date_open`, 
            `date_game`,
            `bar_ids`, 
            `cost`, 
            `min_people`, 
            `max_people` 
        FROM 
            `games_info` 
        WHERE 
            `id`='".$gameId."' 
        LIMIT 1
    ", $db);
    $gameInfoResult = mysql_fetch_array($getGameInfoQuery);
    $number = $gameInfoResult['number'];
    $dateGame = $gameInfoResult['date_game'];
    $dateOpen = $gameInfoResult['date_open'];
    $barIds = $gameInfoResult['bar_ids'];
    $cost = $gameInfoResult['cost'];
    $minPeople = $gameInfoResult['min_people'];
    $maxPeople = $gameInfoResult['max_people'];
    
    $getGameTypeQuery = mysql_query("
        SELECT 
            gt.id, 
            gt.name,
            gt.back_img, 
            gt.friendly_url 
        FROM 
            game_types as gt 
        LEFT JOIN 
            games_info gi 
        ON 
            gi.game_type = gt.id 
        WHERE 
            gi.id='".$gameId."'
    ", $db);
    $gameTypeResult = mysql_fetch_array($getGameTypeQuery);
    $idGameType = $gameTypeResult['id'];
    $nameGame = $gameTypeResult['name'];
    $backImg = $gameTypeResult['back_img'];
    $nameGame = str_replace("{number}", "".$number, $nameGame);
    $friendlyURL = $gameTypeResult['friendly_url']."-".str_replace("#","",$number);

    function isEarlier($fullDate) {
        $dateParts = date_parse($fullDate);
        $dateToCompare = mktime($dateParts['hour'], $dateParts['minute'], 0, $dateParts['month'], $dateParts['day'], $dateParts['year']);
        return ($dateToCompare < time());
    }
    
    function isLater($fullDate) {
        $dateParts = date_parse($fullDate);
        $dateToCompare = mktime($dateParts['hour'], $dateParts['minute'], 0, $dateParts['month'], $dateParts['day'], $dateParts['year']);
        return ($dateToCompare > time());
    }
    
    $bar = $_POST['bar'];
    $comandName = $_POST['team'];
    $players = $_POST['players'];
    $capName = $_POST['cap'];
    $phone = $_POST['phone'];
    $comment = $_POST['comment'];
    $teamName = $comandName;
    
    if (isLater($dateGame) && isEarlier($dateOpen)) {
        if (!in_array($bar, explode(',', $barIds))) {
            sendAnswer(400, "В данном баре игра не проводится");
            die();
        }
        $comandName = mysql_real_escape_string($comandName);
        $capName = mysql_real_escape_string($capName);
        $social = mysql_real_escape_string($social);
        $comment = mysql_real_escape_string($comment);
            
        $getBarInfoQuery = mysql_query("
        SELECT 
            ba.max_teams,
            COUNT(gd.id) as registred_teams, 
            (SELECT COUNT(gd2.players)
            FROM games_data as gd2 
            WHERE gd2.game_info_id='".$gameId."' AND gd2.team_name='".$comandName."') as teams_with_this_name, 
            (SELECT b.name 
            FROM bars as b 
            WHERE b.id='".$bar."') as bar_name 
        FROM bars as ba 
        LEFT JOIN games_data as gd ON gd.bar_id = ba.id 
        WHERE ba.id = '".$bar."' AND gd.game_info_id='".$gameId."'
        ",$db);
        $barInfoResult = mysql_fetch_array($getBarInfoQuery);
        $maxTeams = $barInfoResult['max_teams'];
        $registredTeams = $barInfoResult['registred_teams'];
        $difference = (int)$maxTeams - (int)$registredTeams;
        $playersAlready = (int)$barInfoResult['teams_with_this_name'];
        $barName = $barInfoResult['bar_name'];
            
        if ($playersAlready == 0) {
            if ($difference > 0)
                $reserve = 0;
            else
                $reserve = 1;
            
            if (mysql_query("
                INSERT INTO 
                    `games_data`(
                        `game_info_id`, 
                        `bar_id`, 
                        `players`, 
                        `team_name`, 
                        `captain_name`, 
                        `phone`, 
                        `comment`, 
                        `is_reserve`
                    ) 
                VALUES (
                    '".$gameId."',
                    '".$bar."',
                    '".$players."',
                    '".$comandName."',
                    '".$capName."',
                    '".$phone."',
                    '".$comment."',
                    '".$reserve."'
                )
            ", $db)) {
                if ($reserve)
                    sendAnswer(200, "Регистрация в <b>резерв</b> прошла успешно!<br>Обновите страницу, чтобы увидеть себя в списке зарегистрированных команд");
                else
                    sendAnswer(200, "Регистрация прошла успешно!<br>Обновите страницу, чтобы увидеть себя в списке зарегистрированных команд");
                
                if (!TESTING) {
                    //write to tg
                    $tg_key = "...";
                    $tg_id = "...";
                    $leftPlaces = $difference - 1;
                    $registredTeams++;
                    
                    $tgMessageToSend = "
                        *".trim($teamName)."* ".($reserve ? "в *резерв* " : "")."в $barName ($registredTeams/$maxTeams)\n".
                        "Игроков: *".$players."*\n".
                        "Капитан: ".$capName."\n".
                        "Телефон: ".$phone."\n".
                        "Комментарий: ".$comment."\n\n".
                        getLeftPlacesText($leftPlaces);
                    
                    $urlTelegram = "https://api.telegram.org/bot".$tg_key."/sendMessage";
                    $fieldsTg = [
                        'chat_id' => $tg_id,
                        'text' => $tgMessageToSend,
                        'parse_mode' => 'Markdown'
                    ];
                    $fields_string_tg = http_build_query($fieldsTg);
                    $ch = curl_init();
                    
                    curl_setopt($ch, CURLOPT_URL, $urlTelegram);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string_tg);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                }
                
            } else
                sendAnswer(400, "Произошла непредвиденная ошибка");
        } else
            sendAnswer(400, "Команда с таким названием уже существует");
    } else
        sendAnswer(400, "Регистрация на игру $number закрыта");
        
    function sendAnswer(
        $code,
        $text
    ) {
        echo json_encode(
            [
                "code" => $code,
                "text" => $text
            ]
        );
    }

    function getLeftPlacesText($leftPlaces) {
        $result = "";
        if ($leftPlaces > 0) {
            if (($leftPlaces >= 2 and $leftPlaces <= 4) or ($leftPlaces >= 22 and $leftPlaces <= 24))
                $result = "Осталось *".$leftPlaces."* свободных места";
            else if ($leftPlaces == 1)
                $result = "Осталось *1* свободное место";
            else if ($leftPlaces == 21)
                $result = "Осталось *21* свободное место";else
                $result = "Осталось *".$leftPlaces."* свободных мест";
        } else
            $result = "Свободных мест не осталось, команд в резерве: *".abs($leftPlaces)."*";
        
        return $result;
    }
?>
