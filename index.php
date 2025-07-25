<!DOCTYPE html> 
<html>
    <head>
        <?
            include("./include/connection.php");
            include("./include/links.php");
            
            $getNextGameInfoQuery = mysql_query("
                SELECT 
                    gi.id,
                    gi.bar_ids, 
                    gi.date_game, 
                    gi.cost,
                    gi.min_people,
                    gi.max_people,
                    gt.friendly_url, 
                    gi.number 
                FROM 
                    games_info as gi 
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
            $nextGameInfoResult = mysql_fetch_array($getNextGameInfoQuery);
            $nextGameId = $nextGameInfoResult['id'];
            $barsId = $nextGameInfoResult['bar_ids'];
            $dateGame = $nextGameInfoResult['date_game'];
            $gameCost = $nextGameInfoResult['cost'];
            $gameMinPeople = $nextGameInfoResult['min_people'];
            $gameMaxPeople = $nextGameInfoResult['max_people'];
            $friendlyUrl = $nextGameInfoResult['friendly_url'];
            $nextNumber = $nextGameInfoResult['number'];
            $regLink = $friendlyUrl."-".explode("#", $nextNumber)[1];
            
            $reviewsArray = [];
            $countReviews = 0;
            $getReviewQuery = mysql_query("
                SELECT 
                    `id`,
                    `text`,
                    `author`
                FROM 
                    `reviews` 
                WHERE 
                    `is_active` = 1
            ", $db);
            while ($reviewRow = mysql_fetch_array($getReviewQuery)){
                $reviewItem = [
                    'id' => $reviewRow['id'],
                    'text' => $reviewRow['text'],
                    'author' => $reviewRow['author']
                ];
                array_push($reviewsArray, $reviewItem);
                $countReviews++;
            }
            
            function getGameHeading(
                $dateGame
            ) {
                $dateGameArray = explode(' ', $dateGame);
                $dateArray = explode('-', $dateGameArray[0]);
                $timeArray = explode(':', $dateGameArray[1]);
                
                $nameMonthsArray = ["", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
            
                $weekDay = (int)date("N", mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));
                $weekDaysArray = ["", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота", "воскресенье"];
                
                return (int)$dateArray[2] . " " . $nameMonthsArray[(int)$dateArray[1]] . ", " . $weekDaysArray[$weekDay] . ", " . $timeArray[0] . ":" . $timeArray[1];
            }
            
            function getMonthAndYear(
                $dateGame
            ) {
                $dateGameArray = explode('-',$dateGame);
                $nameMonthsArray = ["", "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
                
                return $nameMonthsArray[$dateGameArray[1]] . " " . $dateGameArray[0];
            }
            
            function getFirstDayOfWeek(
                $dateGame
            ) {
                $dateGameArray = explode('-',$dateGame);
                $firstDayWeek = date('w', strtotime($dateGameArray[0].'-'.$dateGameArray[1].'-01'));
                if ($firstDayWeek == 0)
                    $firstDayWeek = 6;
                else
                    $firstDayWeek = $firstDayWeek - 1;
                return $firstDayWeek;
            }
            
            function getLastDayOfWeek(
                $dateGame
            ) {
                $dateGameArray = explode('-',$dateGame);
                return date('t', strtotime($dateGameArray[0].'-'.$dateGameArray[1].'-01'));
            }
            
            function getGameCalendar(
                $dateGame
            ) {
                $calendar = [
                    ['0', '0', '0', '0', '0', '0', '0'],
                    ['0', '0', '0', '0', '0', '0', '0'],
                    ['0', '0', '0', '0', '0', '0', '0'],
                    ['0', '0', '0', '0', '0', '0', '0'],
                    ['0', '0', '0', '0', '0', '0', '0'],
                    ['0', '0', '0', '0', '0', '0', '0']
                ];
                
                $column = (int)getFirstDayOfWeek($dateGame);
                $count = 1;
                $lastDay = (int)getLastDayOfWeek($dateGame);
                for ($row = 0; $row <= 5; $row++) {
                    while ($column < 7) {
                        if ($count > $lastDay) break;
                        $calendar[$row][$column] = $count;
                        $count++;
                        $column++;
                    }
                    $column = 0;
                }
                return $calendar;
            }
            
            $headerTitle = "Главная | игра-квиз Вынос мозга";
            $headerURL = "https://vynosmozga.ru/";
            
            include("./include/header.php");
        ?>
        
        <!-- CSS -->
        <link rel="stylesheet" href="../style/main.css">
        
        <!-- JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.9/typed.min.js"></script>
        <script src="./scripts/main.js"></script>
    </head>
    <body itemscope itemtype="https://schema.org/WebPage">
        <div class="form-row wrapper_image_hero">
            <div class="col-12 d-flex align-items-center justify-content-end gradient">
                <div class="form-row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="d-flex justify-content-center align-items-center flex-wrap">
                            <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['reg'].$regLink ?>" role="button" data-toggle="tooltip" data-placement="bottom" title="Регистрация на игру">
                                <h3>
                                    Регистрация
                                </h3>
                            </a>
                            <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="#faq" role="button" data-toggle="tooltip" data-placement="bottom" title="Правила и FAQ">
                                <h3>
                                    FAQ
                                </h3>
                            </a>
                            <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['rank'] ?>" role="button" data-toggle="tooltip" data-placement="bottom" title="Рейтинг команд">
                                <h3>
                                    Рейтинг
                                </h3>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="social_wrapper">
            <div class="social_block">
                <a href="<?= $links['social']['VK']; ?>" target="_blank"><i class="fab fa-vk"></i></a>
            </div>
        </div>
 
        <div class="form-row whatIs">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="box">
                    <div class="top-text">
                        <h1 class="display-1">Игра-квиз<br> "Вынос мозга" - это<br><span id="typing"></span></h1>
                    </div>  
                </div>
            </div>
            <div class="col-1"></div>
        </div>
        <br>
        
        <div class="reviews_wrapper"></div>
        <div class="reviews_block">
            <div class="header_review">
                <h3>Отзывы игроков</h3>
            </div>
            <div class="review_wrapper">
                <div class="review_prev">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <?
                    $isFirstReview = true;
                    $reviewsCounter = 0;
                    foreach ($reviewsArray as $review) {
                        ?>
                            <div class="review review-<?= $reviewsCounter.($isFirstReview ? "" : " invisible") ?>" itemprop="review" itemscope itemtype="https://schema.org/Review">
                                <div class="content">
                                    <p class="author_review" itemprop="author"><?= $review['author'] ?></p>
                                    <div class="raiting">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p class="text_review" itemprop="reviewBody">
                                        <?= $review['text'] ?>
                                    </p>
                                </div>
                            </div>
                        <?
                        $isFirstReview = false;
                        $reviewsCounter++;
                    }
                ?>
                <div class="review_next">
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="invisible" id="dataReviews" data-current="0" data-max="<?= $countReviews ?>"></div>
            </div>
        </div>
        <div class="after_reviews_wrapper"></div>
        
        <div class="form-row" id="faq">
            <div class="col-1"></div>
            <div class="col-10">
                <h3 class="heading-dark">
                    Частые вопросы
                </h3>
            </div>
            <div class="col-1"></div>
            
            <div class="col-1"></div>
            <div class="col-10">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Как проходит игра?
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                Игра состоит из 5 раундов:
                                <ul>
                                    <li>Увлекательная <b>разминка</b></li>
                                    <li>Познавательный <b>блиц</b></li>
                                    <i>Перерыв №1</i>
                                    <li>Интригующий <b>секретный</b></li>
                                    <li>Мозговыносящий <b>медиа</b></li>
                                    <i>Перерыв №2</i>
                                    <li>Крутой <b>Что? Где? Когда?</b></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                Сколько человек в команде?
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p>
                                    <span class="fw-bold">От <?= $gameMinPeople ?> до <?= $gameMaxPeople ?></span><br>
                                    Полноценными участниками команды считаются взрослые и дети с 13 лет
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                Сколько стоит участие?
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p>
                                    <span class="fw-bold"><?= $gameCost ?>₽</span> с человека<br>
                                    Однако если сделать репост записи об открытии регистрации в нашей группе в Вконтакте, то есть шанс выиграть участие за 100 рублей с человека
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                                Принимается ли оплата картой?
                            </button>
                        </h2>
                        <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p>
                                    <span class="fw-bold">Нет</span><br>
                                    К оплате принимается только наличный расчёт
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
        
        <div class="sponsors_start"></div>
        <div class="form-row sponsors_block">
            <div class="col-1"></div>
            <div class="col-10">
                <h3 class="heading">
                    Наши партнёры
                </h3>
                <br>
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
                        while ($itemSponsorsResult = mysql_fetch_array($getSponsorsQuery)) {
                            ?>
                                <div class="sponsor">
                                    <a href="<?= $itemSponsorsResult['link'] ?>" target="_blank" title='<?= $itemSponsorsResult['name'] ?>'>
                                        <img src="../img/sponsors/<?= $itemSponsorsResult['img'] ?>">
                                    </a>
                                </div>
                            <?
                        }
                    ?>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="sponsors_end"></div>
        
        <div class="game_around_start"></div>
        <div class="form-row d-flex game-around-content justify-content-center">
            <div class="col-1"></div>
            <div class="col-10">
                <h3 class="heading">
                    Ближайшая игра
                </h3>
                
                <div class="form-row d-flex justify-content-center">
                    <?
                        if ($barsId != "0") {
                            $barsIdsArray = explode("/", $barsId);
                            $barsIds = implode(",", $barsIdsArray);
                            $getBarsQuery = mysql_query("SELECT `icon`, `href` FROM `bars` WHERE `id` IN (".$barsIds.")", $db);
                            while ($itemBarResult = mysql_fetch_array($getBarsQuery)) {
                                ?>
                                    <a class="btn rounded-circle rounded-btn-<?= count($barsIdsArray) ?>" role="button" href="<?= $itemBarResult['href'] ?>" target="_blank">
                                        <img src="<?= $links['system']['bar_img'] . $itemBarResult['icon'] ?>" class="rounded-circle">
                                    </a>
                                <?
                            }
                        }
                    ?>
                    <div class="cardGame d-flex align-items-center justify-content-center">
                        <div>
                            <p class="lead">
                                <?= getGameHeading($dateGame) ?>
                            </p>
                            
                            <div class="calendar-outer">
                                <table id="calendar"  border="0" cellspacing="0" cellpadding="1">
                                    <thead>
                                        <tr>
                                            <td colspan="7"><?= getMonthAndYear($dateGame) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Пн</td><td>Вт</td><td>Ср</td><td>Чт</td><td>Пт</td><td>Сб</td><td>Вс</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
                                            $calendar = getGameCalendar($dateGame);
                                            $gameDay = explode(' ', explode('-', $dateGame)[2])[0];
                                            for ($calenderRow = 0; $calenderRow <= 5; $calenderRow++) {
                                                if ($calenderRow < 5) {
                                                    echo "<tr>";
                                                    for ($dayOfWeekColumn = 0; $dayOfWeekColumn <= 6; $dayOfWeekColumn++) {
                                                        if ($calendar[$calenderRow][$dayOfWeekColumn] != 0) {
                                                            $today = false;
                                                            if ($calendar[$calenderRow][$dayOfWeekColumn] == $gameDay) $today = true;
                                                            echo "<td".($today ? " class=\"today\"" : "").">".$calendar[$calenderRow][$dayOfWeekColumn]."</td>";
                                                        } else
                                                            echo "<td></td>";
                                                    }
                                                    echo "</tr>";
                                                } else {
                                                    if ($calendar[5][0] != 0) {
                                                        echo "<tr>";
                                                        for ($dayOfWeekColumn = 0; $dayOfWeekColumn <= 6; $dayOfWeekColumn++) {
                                                            if ($calendar[$calenderRow][$dayOfWeekColumn] != 0) {
                                                                $today = false;
                                                                if ($calendar[$calenderRow][$dayOfWeekColumn] == $gameDay) $today = true;
                                                                echo "<td".($today ? " class=\"today\"" : "").">".$calendar[$calenderRow][$dayOfWeekColumn]."</td>";
                                                            } else
                                                                echo "<td></td>";
                                                        }
                                                        echo "</tr>";
                                                    }
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-row d-flex justify-content-center btn-cta-reg-outer">
                    <a href="<?= $links['navigation']['reg'].$regLink ?>" type="button" class="btn btn-lg btn-cta-reg">
                        <p>
                            Зарегистрироваться!
                        </p>
                    </a>
                </div>
                <br>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="game_around_end"></div>
        
        <div class="form-row corp_row">
            <div class="col-1"></div>
            <div class="col-10" >
                <br>
                <div class="form-row d-flex justify-content-center">
                    <h3 class="heading-dark">
                        Корпоративные мероприятия
                    </h3>
                </div>
                <br>
                <br>
                
                <div class="prefs_row">
                    <div class="prefs_do">
                        <div class="emoji_card">
                            <img src="img/party-popper.png">
                            <h4 class="prefs_header">Яркий корпоратив под&nbsp;ключ</h4>
                        </div>
                        <div class="emoji_card">
                            <img src="img/microphone.png">
                            <h4 class="prefs_header">Ведущий,<br>Dj и аниматоры</h4>
                        </div>
                        <div class="emoji_card">
                            <img src="img/fire.png">
                            <h4 class="prefs_header">Уникальные<br>тимбилдинг-конкурсы</h4>
                        </div>
                    </div>
                    <div class="prefs_order">
                        <button type="button" class="btn btn-lg btn-trigger-modal" data-bs-toggle="modal" data-bs-target="#orderModal">
                            Как заказать
                        </button>
                    </div>
                </div>
                
                <div class="modal fade" id="orderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="orderModalLabel">
                                    Заказать корпоратив
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Написать <a href="https://vk.com/im?sel=-131914722" target="_blank"><i class="fab fa-vk"></i></a><br>
                                Позвонить <a href="tel:+79787458485" target="_blank">+7(978)745-84-85</a><br>
                                Написать <a href="mailto:info@vynosmozga.ru" target="_blank">info@vynosmozga.ru</a>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
        
        <div class="footer_start"></div>
        
        <? include("include/footer.php"); ?>
                 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>