<!DOCTYPE html> 
<html>
    <head>
        <?
            $getRankId = $_GET['idRank'];
            
            include("../include/connection.php");
            include("../include/links.php");
            
            $getNextGameQuery = mysql_query("
                SELECT 
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
            $nextGameResult = mysql_fetch_array($getNextGameQuery);
            $friendlyUrl = $nextGameResult['friendly_url'];
            $nextNumber = $nextGameResult['number'];
            $regLink = $friendlyUrl."-".explode("#", $nextNumber)[1];
            
            
            $getCurrentRankInfoQuery = mysql_query("SELECT * FROM `ranks` "
                . ($getRankId == null ? "ORDER BY `last_game` DESC" : "WHERE `id`='".$getRankId."'").
                " LIMIT 1", $db);
            $currentRankInfoResult = mysql_fetch_array($getCurrentRankInfoQuery);
            $nameRank = $currentRankInfoResult['season_name'];
            $idRank = $currentRankInfoResult['id'];
            $firstGame = $currentRankInfoResult['first_game'];
            $lastGame = $currentRankInfoResult['last_game'];
            $seasonTime = $currentRankInfoResult['season_time'];
            $isSeasonWordFirst = $currentRankInfoResult['is_season_word_first'];
            $countGames = $lastGame - $firstGame + 1;
            
            $headerTitle = "Рейтинг | игра-квиз Вынос мозга";
            $headerURL = "https://vynosmozga.ru/rank/";
            
            include("../include/header.php");
        ?>
        
        <!-- CSS -->
        <link rel="stylesheet" href="../style/rank.css">
    </head>
    <body>
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
                            <a class="btn btn-lg btn-block d-flex align-items-center justify-content-center menuButton" href="<?= $links['navigation']['reg'].$regLink ?>" role="button" data-toggle="tooltip" data-placement="bottom" title="Регистрация на игру">
                                <h3>
                                    Регистрация
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
        
        <div class="form-row whatIs">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="box">
                    <div class="top-text">
                        <h3 class="heading-dark">О рейтинге "Выноса&nbsp;мозга"</h3>
                        <p>Рейтинг игры-квиза "Вынос мозга"&nbsp;- это сумма всех набраных баллов команды за текущий сезон.</p>
                        <p>За каждые 100 баллов в сезоне команда получает специальные призы.</p>
                        <p>По результатам всех игр сезона выявляется три команды-победителя, которые получают подарки. </p>
                        <p><?= ($isSeasonWordFirst ? "Сезон ".$nameRank : $nameRank." сезон") ?> состоит из <?= $countGames ?> игр&nbsp;- с #<?= $firstGame ?> по #<?= $lastGame ?></p>
                    </div>  
                </div>
            </div>
            <div class="col-1"></div>
        </div>
        <br>
        
        <div class="reviews_wrapper"></div>
        <div class="reviews_block">
            <div class="header_review">
                <h3><?= ($isSeasonWordFirst ? "Сезон ".$nameRank : $nameRank." сезон") ?></h3>
                <h5><?= $seasonTime ?></h5>
            </div>
            <div class="review_wrapper">
                <table class="table table-hover table_rank">
                    <thead>
                        <tr>
                            <th scope="col">
                                Место
                            </th>
                            <th scope="col">
                                Название
                            </th>
                            <?
                                for ($ratingCurrentGameNum = $firstGame; $ratingCurrentGameNum <= $lastGame; $ratingCurrentGameNum++) {
                                    ?>
                                        <th scope="col" class="game_num">
                                            <center>
                                                #<?= $ratingCurrentGameNum ?>
                                            </center>
                                        </th>
                                    <?
                                }
                            ?>
                            <th scope="col">
                                <center>
                                    Итого
                                </center>
                            </th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?
                            $placeInRank = 0;
                            $getRankDataQuery = mysql_query("
                                SELECT 
                                    * 
                                FROM 
                                    `ranks_data` 
                                WHERE 
                                    `rank_id`='$idRank' 
                                ORDER BY 
                                    `total` DESC, 
                                    `total_sort_sum` DESC,
                                    `team_name` ASC", $db);
                            while ($itemRankDataResult = mysql_fetch_array($getRankDataQuery)) {
                                $teamId = $itemRankDataResult['id'];
                                $teamName = $itemRankDataResult['team_name'];
                                $games = $itemRankDataResult['score_games'];
                                $total = $itemRankDataResult['total'];
                                
                                $placeInRank++;
                                
                                $games = explode(";", $games);
                                $tableRow = "<td>$teamName</td>";
                                for ($i = 0; $i < $countGames; $i++) {
                                    if ($games[$i] != 0)
                                      $tableRow = $tableRow."<td class=\"game_num\"><center>".str_replace(".", ",", $games[$i])."</center></td>";
                                    else
                                        $tableRow = $tableRow."<td class=\"game_num\"><center></center></td>";
                                }
                                $style = '';
                                switch ($placeInRank) {
                                    case 1: 
                                        $style = ' class="gold"';
                                        break;
                                    case 2:
                                        $style = ' class="silver"';
                                        break;
                                    case 3:
                                        $style = ' class="bronze"';
                                        break;
                                }
                                ?>
                                    <tr<?= $style ?>>
                                        <th scope="row">
                                            <?= $placeInRank ?>
                                        </th>
                                        <?= $tableRow ?>
                                        <td>
                                            <center>
                                                <b><?= str_replace(".", ",", $total) ?></b>
                                            </center>
                                        </td>
                                    </tr>
                                <?
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="after_reviews_wrapper"></div>
        
        <div class="form-row" id="faq">
            <div class="col-1"></div>
            <div class="col-10">
                <h3 class="heading-dark">
                    Другие сезоны
                </h3>
            </div>
            <div class="col-1"></div>
            
            <div class="col-1"></div>
            <div class="col-10">
                    <?
                        $counterRanks = 0;
                        $getSeasonsRatingsQuery = mysql_query("
                            SELECT 
                                r.*, 
                                (SELECT rd.team_name FROM ranks_data as rd WHERE rd.rank_id = r.id ORDER BY rd.total DESC LIMIT 1) as win,
                                (SELECT rd.team_name FROM ranks_data as rd WHERE rd.rank_id = r.id ORDER BY rd.total DESC LIMIT 1 OFFSET 1) as second,
                                (SELECT rd.team_name FROM ranks_data as rd WHERE rd.rank_id = r.id ORDER BY rd.total DESC LIMIT 1 OFFSET 2) as third
                            FROM ranks as r 
                            WHERE r.id <> ".$idRank." 
                            ORDER BY r.last_game DESC
                        ", $db);
                        while ($itemSeasonsRatingsResult = mysql_fetch_array($getSeasonsRatingsQuery)) {
                            $nameRankModal = $itemSeasonsRatingsResult['season_name'];
                            $timeRankModal = $itemSeasonsRatingsResult['season_time'];
                            $idRankModal = $itemSeasonsRatingsResult['id'];
                            $firstGameModal = $itemSeasonsRatingsResult['first_game'];
                            $lastGameModal = $itemSeasonsRatingsResult['last_game'];
                            $winnerModal = $itemSeasonsRatingsResult['win'];
                            $num2 = $itemSeasonsRatingsResult['second'];
                            $num3 = $itemSeasonsRatingsResult['third'];
                            $countGamesModal = $lastGameModal - $firstGameModal + 1;
                            if ($counterRanks == 0) 
                                echo "<div class=\"rank_wrapper\">";
                            $counterRanks++;
                            ?>
                            
                                <div class="rank_item">
                                    <div class="rank_head"><?= $nameRankModal ?><p><?= $timeRankModal ?></p></div>
                                    <div class="rank_info"><b><?= $countGamesModal ?></b> игр с <b>#<?= $firstGameModal ?></b> по <b>#<?= $lastGameModal ?></b></div>
                                    <div class="rank_chemp">
                                        Победитель: <b><?= $winnerModal ?></b><br>
                                        2 место: <b><?= $num2 ?></b><br>
                                        3 место: <b><?= $num3 ?></b>
                                    </div>
                                    <div class="rank_btn">
                                        <a class="btn btn-primary" href="<?= $links['navigation']['rank'].$idRankModal ?>">
                                            Таблица
                                        </a>
                                    </div>
                                </div>
                                
                                <?
                                if ($counterRanks == 3) {
                                    echo "</div><br>";
                                    $counterRanks = 0;
                                }
                        }
                        if ($counterRanks > 0) 
                            echo "</div><br>";
                    ?>
            </div>
            <div class="col-1"></div>
        </div>
        
        <div class="footer_start"></div>
        
        <? include('../include/footer.php'); ?>
    </body>
</html>