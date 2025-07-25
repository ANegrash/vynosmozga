<!DOCTYPE html> 
<html>
    <head>
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
            
            ym(92871414, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
            });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/92871414" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
        <?
            $gameURL = $_GET['gametype'];
            $gameNum = $_GET['gamenum'];
            
            include("../include/connection.php");
            include("../include/links.php");
            
            header('Content-Type: text/html; charset=utf-8');
            date_default_timezone_set('Europe/Moscow');
            
            function textMonth(
                $month
            ) {
                $nameMonthsArray = ["", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
                return $nameMonthsArray[(int)$month];
            }
            
            function textDayOfWeek(
                $day, 
                $month, 
                $year
            ) {
                $weekDay = date("N", mktime(0, 0, 0, $month, $day, $year));
                $weekDaysArray = ["", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота", "воскресенье"];
                return $weekDaysArray[$weekDay];
            }
            
            function displayCardDate(
                $date
            ) {
                $dateParts = getDateParts($date);
                return (int)$dateParts['day']." ".textMonth($dateParts['month'])." (".textDayOfWeek($dateParts['day'], $dateParts['month'], $dateParts['year']).") в ".$dateParts['hour'].":".$dateParts['minute'];
            }
            
            //array(day, month, year, hour, minute)
            function getDateParts(
                $date
            ) {
                $return = array();
                $partsOne = explode(' ', $date);
                $days = $partsOne[0];
                $time = $partsOne[1];
                
                $dateG = explode('-', $days);
                $return['day'] = $dateG[2];
                $return['month'] = $dateG[1];
                $return['year'] = $dateG[0];
                
                $timeG = explode(':', $time);
                $return['hour'] = $timeG[0];
                $return['minute'] = $timeG[1];
                
                return $return;
            }
            
            function isMoreThanNow(
                $fullDate
            ) {
                $date = explode(" ", $fullDate)[0];
                $time = explode(" ", $fullDate)[1];
                    
                $date = explode('-',$date);
                $day = (string)$date[2];
                $month = (string)$date[1];
                $year = (string)$date[0];
                $dateToCompare = $year.$month.$day;
                
                $time = explode(':',$time);
                $hour = (string)$time[0];
                $minute = (string)$time[1];
                $timeToCompare = $hour.$minute;
                
                
                $dayNow = (string)date(d);
                $monthNow = (string)date(m);
                $yearNow = (string)date(Y);
                $hourNow = date(G);
                $minuteNow = date(i);
                $dateNow = $yearNow.$monthNow.$dayNow;
                $timeNow = $hourNow.$minuteNow;
                
                return (($dateToCompare == $dateNow and $timeToCompare >= $timeNow) or ($dateToCompare > $dateNow));
            }
            
            $getClosestGameInfoQuery = mysql_query("
                SELECT 
                    gt.friendly_url, 
                    gi.number FROM games_info as gi 
                LEFT JOIN 
                    game_types as gt 
                ON 
                    gt.id = gi.game_type 
                WHERE 
                    gi.date_game > now() 
                ORDER BY 
                    ABS(now() - gi.date_game) ASC 
                LIMIT 1
            ", $db);
            
            $closestGameInfoResult = mysql_fetch_array($getClosestGameInfoQuery);
            $friendlyUrl = $closestGameInfoResult['friendly_url'];
            $nextNumber = $closestGameInfoResult['number'];
            $regLink = $friendlyUrl."-".explode("#", $nextNumber)[1];
            
            
            $regLink = $links['navigation']['reg'].$regLink;
            
            if (is_null($gameURL) || is_null($gameNum)) {
                header('Location: '.$regLink);
                die();
            }
        
            //gameTypes
            $getCurrentGameTypeQuery = mysql_query("
                SELECT 
                    gt.id, 
                    gt.name,
                    gt.back_img, 
                    gt.friendly_url 
                FROM 
                    game_types as gt 
                WHERE 
                    `friendly_url`='".$gameURL."'
            ", $db);
            $currentGameTypeResult = mysql_fetch_array($getCurrentGameTypeQuery);
            $idGameType = $currentGameTypeResult['id'];
            $nameGame = $currentGameTypeResult['name'];
            $backImg = $currentGameTypeResult['back_img'];
            $nameGame = str_replace("{number}", "#".$gameNum, $nameGame);
            
            //gamesInfo
            $getCurrentGameInfoQuery = mysql_query("
                SELECT 
                    `id`,
                    `date_open`, 
                    `date_game`,
                    `bar_ids`, 
                    `cost`, 
                    `min_people`, 
                    `max_people` 
                FROM 
                    `games_info` 
                WHERE 
                    `number`='#".$gameNum."' 
                    AND `game_type`='".$idGameType."' 
                LIMIT 1
            ", $db);
            $currentGameInfoResult = mysql_fetch_array($getCurrentGameInfoQuery);
            $idGameInfo = $currentGameInfoResult['id'];
            $dateGame = $currentGameInfoResult['date_game'];
            $dateOpen = $currentGameInfoResult['date_open'];
            $barIds = $currentGameInfoResult['bar_ids'];
            $cost = $currentGameInfoResult['cost'];
            $minPeople = $currentGameInfoResult['min_people'];
            $maxPeople = $currentGameInfoResult['max_people'];
            $barIds = str_replace("/", ",", $barIds);
            
            //bars
            $barsCountInGame = count(explode(",", $barIds));
            $barField = "";
            $getBarsInfoQuery = mysql_query("
                SELECT 
                    b.id, 
                    b.name,
                    b.location, 
                    b.max_teams, 
                    b.icon, 
                    b.href, 
                    (b.max_teams - (
                        SELECT 
                            COUNT(gd.team_name) 
                        FROM 
                            games_data as gd 
                        WHERE 
                            gd.bar_id = b.id 
                            AND gd.game_info_id = ".$idGameInfo."
                        )
                    ) as free_teams 
                FROM 
                    bars b 
                WHERE 
                    b.id IN (".$barIds.")
            ", $db);
            while ($itemBarsInfoResult = mysql_fetch_array($getBarsInfoQuery)) {
                $currentBarId = $itemBarsInfoResult['id'];
                $nameBar = $itemBarsInfoResult['name'];
                $location = $itemBarsInfoResult['location'];
                $maxTeams = $itemBarsInfoResult['max_teams'];
                $barImage = $itemBarsInfoResult['icon'];
                $href = $itemBarsInfoResult['href'];
                $freeTeams = $itemBarsInfoResult['free_teams'];
                $barField .= ($barsCountInGame == 1 ? "<input name=\"barSelect\" type=\"hidden\" value=\"".$currentBarId."\">" : (empty($barField) ? "<input name=\"barSelect\" type=\"hidden\" value=\"0\">" : ""));
                $mest = ($freeTeams <= 0 ? "<font color='red'>Только в резерв!</font>" : "свободно: ".$freeTeams."/".$maxTeams);
                $barField .= "
                    <div class=\"bar-item\">
                        <button class=\"btn btn-bar".($barsCountInGame == 1 ? " selected" : "")."\" type=\"button\" data-barid=\"".$currentBarId."\">
                            <img src=\"".$links['system']['bar_img'].$barImage."\" width=\"150px\">
                            <br>
                            ".$mest."
                        </button>
                        <br>
                        <a href=\"".$href."\" target=\"_blank\" title=\"".$location."\" class=\"btn btn-outline-secondary\" width=\"200px\">Где это?</a>
                    </div>
                ";
            }
            
            if (!empty($dateGame)) {
                if (isMoreThanNow($dateGame)) {
                    if (isMoreThanNow($dateOpen)) {
                        //Регистрация ещё закрыта
                        $open_block_img = "../img/lock.png";
                        $open_block_h3 = "Регистрация закрыта";
                        $open_block_h4 = "Откроется ".displayCardDate($dateOpen);
                        $footer_color = "light-back";
                    } else {
                        //Регистрация открыта
                        $open_block_img = "../img/confety.png";
                        $open_block_h3 = "Регистрация открыта";
                        $open_block_h4 = "Игра состоится ".displayCardDate($dateGame);
                        $footer_color = "blue-back";
                    }
                } else {
                    //Игра уже прошла
                    $open_block_img = "../img/finish-flag.png";
                    $open_block_h3 = "Игра уже прошла";
                    $open_block_h4 = "Вы можете перейти <a href='".$regLink."'>к ближайшей игре</a>";
                    $footer_color = "light-back";
                }
            } else {
                //Игра не найдена
                $open_block_img = "../img/tea.png";
                $open_block_h3 = "Игра не найдена";
                $open_block_h4 = "Вы можете перейти <a href='".$regLink."'>к ближайшей игре</a>";
                $footer_color = "light-back";
            }
            
            $headerTitle = $nameGame;
            $headerURL = $regLink;
            
            include("../include/header.php");
        ?>
        
        <!-- CSS -->
        <link rel="stylesheet" href="../style/reg.css">
        
        <!-- JS -->
        <script src="../scripts/reg.js"></script>
    </head>
    <body>
        <div class="form-row">
            <div class="form-row wrapper_image_hero">
                <div class="col-12 d-flex align-items-center justify-content-end gradient">
                    <div class="form-row">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="slideRight d-flex justify-content-center align-items-center flex-wrap">
                                <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['main'] ?>" role="button" data-toggle="tooltip" data-placement="bottom" title="Главная страница">
                                    <h3>
                                        Главная
                                    </h3>
                                </a>
                                <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['rank'] ?>" role="button" data-toggle="tooltip" data-placement="bottom" title="Регистрация на игру">
                                    <h3>
                                        Рейтинг
                                    </h3>
                                </a>
                                <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['main'] ?>#faq" role="button" data-toggle="tooltip" data-placement="bottom" title="Правила и FAQ">
                                    <h3>
                                        FAQ
                                    </h3>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="social_wrapper">
                <div class="social_block">
                    <a href="<?= $links['social']['VK'] ?>" target="_blank"><i class="fab fa-vk"></i></a>
                </div>
            </div>
        </div>
 
        <div class="form-row whatIs">
            <div class="col-12">
                <div class="box">
                    <div class="top-text">
                        <div class="open-info">
                            <img src="<?= $open_block_img ?>">
                            <h3>
                                <?= $open_block_h3 ?>
                            </h3> 
                            <h4>
                                <?= $open_block_h4 ?>
                            </h4>
                        </div>
                        <? 
                            if (!empty($dateGame) and isMoreThanNow($dateGame) and !isMoreThanNow($dateOpen)) {
                                ?>
                                    <div class="form-row">
                                        <div class="col"></div>
                                        <div class="content" id="form">
                                            <div class="alert alert-success d-flex align-items-center hidden" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                                <div class="alert-success-text text-left pl-4">
                                                    Вы успешно зарегистрировались на игру!
                                                </div>
                                            </div>
                                            <div class="alert alert-danger d-flex align-items-center hidden" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                <div class="alert-danger-text text-left pl-4">
                                                    Неверно заполнены поля
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <h6 class="mb-0">
                                                        Бар игры<span class="required">*</span>
                                                    </h6>
                                                    <div class="bar-container form-row">
                                                        <?= $barField ?>
                                                    </div>
                                                    <span class="required error hidden" id="validation_error_barSelect">Выберите бар игры</span>
                                                </div>
                                            </div>
                                            <br>
                                
                                            <div class="form-row">
                                                <div class="col">
                                                    <h6>
                                                        Название команды<span class="required">*</span>
                                                    </h6>
                                                    <input type="text" name="teamName" class="form-control" placeholder="Название команды" required>
                                                    <span class="required error hidden" id="validation_error_teamName">Введите название команды</span>
                                                </div>
                                                <div class="ots-l"></div>
                                                <div class="col">
                                                    <h6>
                                                        Количество игроков<span class="required">*</span>
                                                    </h6>
                                                    <input name="players" type="hidden" value="5">
                                                    <div class="form-row">
                                                        <? 
                                                            for ($k = $minPeople; $k <= $maxPeople; $k++) {
                                                                ?>
                                                                    <button class="btn-people count-people<?= ($k == 5 ? ' selected': '') ?>" data-players="<?= $k ?>">
                                                                        <p class="mt-1"><?= $k ?></p>
                                                                    </button>
                                                                <?
                                                            }
                                                        ?>
                                                    </div>
                                                    <span class="required error hidden" id="validation_error_players">Выберите количество игроков в команде</span>
                                                </div>
                                            </div>
                                            <br>
                            
                                            <div class="form-row">
                                                <div class="col">
                                                    <h6>
                                                        Имя капитана<span class="required">*</span>
                                                    </h6>
                                                    <input type="text" name="capName" id="cap" class="form-control" placeholder="Имя капитана" required>
                                                    <span class="required error hidden" id="validation_error_capName">Введите имя капитана</span>
                                                </div>
                                                <div class="ots-l"></div>
                                                <div class="col">
                                                    <h6>
                                                        Номер телефона<span class="required">*</span>
                                                    </h6>
                                                    <input type="phone" name="phone" id="phone" class="form-control" placeholder="Телефон капитана" required>
                                                    <span class="required error hidden" id="validation_error_phone">Введите корректный номер телефона</span>
                                                </div>
                                            </div>
                                            <br>
                
                                            <div class="form-row">
                                                <div class="col">
                                                    <h6>
                                                        Комментарий
                                                    </h6>
                                                    <textarea type="text" name="comment" class="form-control" rows="3" placeholder="Например: просим столик возле ведущего" width="100%"></textarea>
                                                </div>
                                            </div>
                                            <br>
                            
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="policyAgree" class="form-check-input" id="exampleCheck1" checked required>
                                                        <label class="form-check-label" for="exampleCheck1">
                                                            Я согласен на обработку Персональных данных согласно 
                                                            <a href="../vynosmozga_policy.pdf" target="_blank">
                                                                Политики конфиденциальности
                                                            </a>
                                                            <span class="required">*</span>
                                                        </label>
                                                    </div>
                                                    <span class="required error hidden" id="validation_error_policyAgree">Подтвердите согласие</span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-row"> 
                                                <div class="col">
                                                    <input type="submit" name="submit" value="Зарегистрироваться!" id="reg-submit" data-game="<?= $idGameInfo ?>" class="btn btn-block"></input>
                                                    <div class="spinner-border hidden" id="loading" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            
                                            <div class="form-row sponsors">
                                                <div class="col">
                                                    <h4>Партнёры игры:</h4>
                                                    <div class="sponsors_container">
                                                        <?
                                                            $getSponsorsQuery = mysql_query("
                                                                SELECT 
                                                                    s.link, 
                                                                    s.img,
                                                                    s.name
                                                                FROM 
                                                                    sponsors as s 
                                                                WHERE 
                                                                    s.is_active = 1 
                                                            ", $db);
                                                            while ($itemSponsorResult = mysql_fetch_array($getSponsorsQuery)) {
                                                                ?>
                                                                    <div class="sponsor">
                                                                        <a href="<?= $itemSponsorResult['link'] ?>" target="_blank" title='<?= $itemSponsorResult['name'] ?>'>
                                                                            <img src="../img/sponsors/<?= $itemSponsorResult['img'] ?>">
                                                                        </a>
                                                                    </div>
                                                                <?
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col"></div>
                                    </div>
                                    <br>
                                    
                            
                                    <div class="form-row reviews_wrapper"></div>
                                    <div class="form-row reviews_block">
                                        <div class="content">
                                            <div class="form-row">
                                                <?
                                                    $teamList = [];
                                                    $barList = [];
                                                    $getRegistrationInfoQuery = mysql_query("
                                                        SELECT 
                                                            gd.bar_id, 
                                                            b.name as bar_name, 
                                                            gd.team_name, 
                                                            gd.is_reserve 
                                                        FROM 
                                                            games_data as gd 
                                                        LEFT JOIN 
                                                            bars as b 
                                                        ON 
                                                            b.id = gd.bar_id 
                                                        WHERE 
                                                            gd.game_info_id = '".$idGameInfo."' 
                                                        ORDER BY 
                                                            gd.bar_id, 
                                                            gd.is_reserve, 
                                                            gd.id
                                                    ", $db);
                                                    while ($itemRegistrationInfoResult = mysql_fetch_array($getRegistrationInfoQuery)) {
                                                        $teamList[$itemRegistrationInfoResult['bar_id']][] = $itemRegistrationInfoResult['team_name'] . 
                                                                (
                                                                    $itemRegistrationInfoResult['is_reserve'] == 1 ? 
                                                                    " <font class=\"reserve\"><b>(резерв)</b></font>" : 
                                                                    ""
                                                                );
                                                        $barList[$itemRegistrationInfoResult['bar_id']] = $itemRegistrationInfoResult['bar_name'];
                                                    }
                                                    
                                                    foreach ($teamList as $barid => $listTeams) {
                                                        ?>
                                                            <div class="col-0 col-lg-2"></div>
                                                                    <div class="col-12 col-lg-8">
                                                                        <div class="review_wrapper">
                                                                            <table class="table table-hover table_rank">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2">
                                                                                            <center><h4><?= $barList[$barid] ?></h4></center>
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody> 
                                                                                    <?
                                                                                        foreach ($listTeams as $key => $team){
                                                                                            ?>
                                                                                                <tr>
                                                                                                    <th scope="row">
                                                                                                        <?= ($key+1) ?>
                                                                                                    </th>
                                                                                                    <td>
                                                                                                        <?= $team ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            <?
                                                                                        }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                            <div class="col-0 col-lg-2"></div>
                                                        <?
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?
                            }
                        ?>
                        <div class="form-row footer_start <?= $footer_color ?>"></div>
                    </div>  
                </div>
            </div>
        </div>
    
        <? include("../include/footer.php"); ?>
        
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </symbol>
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </symbol>
        </svg>  
    </body>
</html>