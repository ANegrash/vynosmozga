<?php
    
    function getInfoByGameId($paramIdGame = null){
        global $db;
        
        $gameid = $paramIdGame ?: $_GET["id_game"];
        
        $sortedType = $_GET['sort_type'];
        $sortingQuery = "";
        
        if ($sortedType == 'up'){
            $sortedIcon = '<i class="fas fa-sort-up"></i>';
            $sortingQuery = "ORDER BY gd.players DESC";
        } else if ($sortedType == 'down') {
            $sortedIcon = '<i class="fas fa-sort-down"></i>';
            $sortingQuery = "ORDER BY gd.players ASC";
        } else {
            $sortedIcon = '<i class="fas fa-sort"></i>';
        }
        
        $res = mysql_query("
        SELECT 
            gi.number, 
            gi.date_open, 
            gi.date_game, 
            gi.cost, 
            gi.min_people, 
            gi.max_people, 
            gt.name, 
            (SELECT l.link FROM links as l WHERE l.category = 'navigation' AND l.name = 'reg') as url, 
            gt.friendly_url,
            (SELECT SUM(gd.players) FROM games_data as gd WHERE gd.game_info_id=gi.id) as sum
        FROM games_info as gi 
        LEFT JOIN game_types as gt 
            ON gt.id = gi.game_type 
        WHERE 
            gi.id=".$gameid
        ,$db);
        
        $row = mysql_fetch_array($res);
        $date_open = $row['date_open']."";
        $date_game = $row['date_game']."";
        $cost = $row['cost'];
        $min_people = $row['min_people'];
        $max_people = $row['max_people'];
        $gameName = str_replace("{number}", $row['number'], $row['name']);
        $gameUrl = $row['url'].$row['friendly_url']."-".explode("#", $row['number'])[1];
        $sumPeople = $row['sum'];
        $profit = $sumPeople*$cost;
        if($cost == 0) $cost = "бесплатно";
        if($min_people == 0 and $min_people==$max_people) $people = "не ограничено";
        else $people = "от ".$min_people." до ".$max_people;
        $barTeamsInfo = '
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Бар</th>
                    <th scope="col">Команд</th>
                    <th scope="col">Игроков</th>
                    <th scope="col">Прибыль</th>
                </tr>
            </thead>
            <tbody>';
        $isBarTeams = false;
        $res = mysql_query("
            SELECT 
                b.name as bar_name, 
                COUNT(gd.id) as count, 
                SUM(gd.players) as pl 
            FROM games_data as gd 
            LEFT JOIN bars as b 
            ON gd.bar_id=b.id 
            WHERE gd.game_info_id='".$gameid."' 
            GROUP BY gd.bar_id
        ",$db);
        while ($row2 = mysql_fetch_array($res)){
            $isBarTeams = true;
            $teams = "никого";
            if ($row2['count'] == 1)
                $teams = "команда";
            else if ($row2['count'] > 1 && $row2['count'] < 5)
                $teams = "команды";
            else 
                $teams = "команд";

            $peopleBar = $row2['pl'];
            $prof = $peopleBar * $cost;
            $barTeamsInfo .= '
                <tr>
                    <td>'.$row2['bar_name'].'</td>
                    <td>'.$row2['count'].'</td>
                    <td>'.$peopleBar.'</td>
                    <td>'.goodPrice($prof).'₽</td>
                </tr>
            ';
        }
        $barTeamsInfo .= '
            </tbody>
        </table>
        <br>
        <a class="btn btn-primary" href="?page=csv_data&type=rank_list&id_game='.$gameid.'">
            Рейтинг команд
        </a>
        <a class="btn btn-primary" href="?page=csv_data&type=reg_list&id_game='.$gameid.'">
            Список команд в csv
        </a>
        ';
            ?>
                <a href="?page=registrations">
                    <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
                </a> 
            
            <style>
                #sort-by-people {
                    cursor: pointer;
                }
            </style>
            <script>
                function sortByPeople(currentTypeSort){
                    var url = '?page=registration&id_game=<?= $gameid ?>';
                    if (currentTypeSort == 'up'){
                        url += '&sort_by=people&sort_type=down';
                    } else if (currentTypeSort == 'down'){
                        url += '';
                    } else {
                        url += '&sort_by=people&sort_type=up';
                    }
                    document.location.href = url;
                }
        		
        		function getTeamInfo(teamId){
                    document.location.href = '?page=team&id_team='+teamId+'&id_game=<?= $gameid ?>';
                }
            </script>
            
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            <? echo $gameName; ?>
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <b>Открытие регистрации:</b> <? echo goodDate($date_open); ?><br>
                            <b>Игра:</b> <? echo goodDate($date_game); ?><br>
                            <b>Стоимость:</b> <? echo $cost; ?>₽<br>
                            <b>Количество игроков:</b> <? echo $people; ?><br>
                            <b>Ссылка на регистрацию:</b> <a href="<? echo $gameUrl; ?>" target="_blank"><? echo $gameUrl; ?></a><br>
                            <b>Ожидаемая прибыль:</b> ≈ <? echo goodPrice($profit); ?>₽<br>
                            <? if ($isBarTeams){ ?><b>Статистика:</b><br><? echo $barTeamsInfo; }?>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5><center>Список команд</center></h5>
            <div class="table-responsive">
                <table class='table table-striped table-hover table-sm align-middle' width='90%'>
                    <thead>
                        <tr>
                            <th scope="col">Бар</th>
                            <th scope="col" onclick="sortByPeople('<?= $sortedType ?>')">Чел <? echo $sortedIcon; ?></th>
                            <th scope="col">Название</th>
                            <th scope="col" class="not_mobile">Капитан</th>
                            <th scope="col" class="not_mobile">Телефон</th>
                            <th scope="col" class="not_mobile">Комментарий</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $j = 0;
                            $res2 = mysql_query("
                                SELECT 
                                    gd.id, 
                                    b.id as id_bar, 
                                    b.name as bar_name, 
                                    gd.players, 
                                    gd.team_name, 
                                    gd.captain_name, 
                                    gd.phone,
                                    gd.comment, 
                                    gd.is_reserve 
                                FROM games_data as gd 
                                LEFT JOIN bars as b 
                                    ON b.id = gd.bar_id 
                                WHERE gd.game_info_id='".$gameid."' ".$sortingQuery."
                            ",$db);
                            while ($row = mysql_fetch_array($res2)){
                                $aj_id_allGames = $row['id'];
                                $aj_idBar = $row['id_bar'];
                                $aj_nameBar = $row['bar_name'];
                                $aj_players = $row['players'];
                                $aj_teamName = $row['team_name'];
                                $aj_capName = $row['captain_name'];
                                $aj_phone = $row['phone'];
                                $aj_comment = $row['comment'];
                                $aj_reserve = $row['is_reserve'];
                                $aj_comandName = $aj_teamName;
                                if ($aj_reserve == 1) 
                                    $aj_comandName .= " <font color=\"red\">(резерв)</font>";
                                
                                $j++;
                                ?>
                                    <tr onclick="getTeamInfo(<? echo $aj_id_allGames; ?>);">
                                        <td><? echo $aj_nameBar; ?></td>
                                        <td><center><? echo $aj_players; ?></center></td>
                                        <td><? echo $aj_comandName; ?></td>
                                        <td class="not_mobile"><? echo $aj_capName; ?></td>
                                        <td class="not_mobile"><? echo (empty($aj_phone) ? "не указан" : $aj_phone); ?></td>
                                        <td class="not_mobile"><? echo $aj_comment; ?></td>
                                    </tr>
                                   
                                    <style> 
                                        @media screen and (max-width: 767px) {
                                            .not_mobile {
                                                display: none;
                                            }
                                        }
                                    </style>
                                <?
                            }
                            if($j == 0){
                                ?>
                                    <tr><td colspan="9"><center>Ни одной команды</center></td></tr>
                                <?
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="?page=create_team&id_game=<?= $gameid ?>" class="btn btn-success">Добавить команду</a>
        <?
    }
    
    function getRegistrationInfo(){
        global $db;
        
        $currentGamesId = array();
        $res = mysql_query("
            SELECT `id` 
            FROM `games_info` 
            WHERE `date_open`<now() 
            AND `date_game`>now()
        ",$db);
        while ($row2 = mysql_fetch_array($res))
            array_push($currentGamesId, $row2['id']);
        
        if (count($currentGamesId) == 1)
            getInfoByGameId($currentGamesId[0]);
        
        ?>
            <script>
        		function getGameInfo(gameId){
                    document.location.href = '?page=registration&id_game='+gameId;
                }
            </script>
            
            <h5><center>Будущие регистрации</center></h5>
            <hr>
            <div class="table-responsive">
                <table class='table table-striped table-hover table-sm align-middle' width='90%'>
                    <thead>
                        <tr>
                            <th scope="row">Игра</th>
                            <th scope="row">Открытие регистрации</th>
                        </tr>
                    </thead>
                    <tbody>
            <?
                $res = mysql_query("
                    SELECT 
                        gi.id,
                        gi.number, 
                        gi.date_open, 
                        gi.date_game,
                        gt.name
                    FROM games_info as gi 
                    LEFT JOIN game_types as gt 
                        ON gt.id = gi.game_type 
                    WHERE gi.date_open>now()
                    ORDER BY gi.date_open
                ",$db);
                while ($row = mysql_fetch_array($res)){
                    $idGameInfo = $row['id'];
                    $number = $row['number'];
                    $gameDate = $row['date_open'];
                    $gameName = str_replace("{number}", $number, $row['name']);
                    ?>
                        <tr onclick="getGameInfo(<?= $idGameInfo ?>);">
                            <td><?= $gameName ?></td>
                            <td><?= goodDate($gameDate) ?></td>
                        </tr>
                    <?
                }
            ?>
                    </tbody>
                </table>
            </div>
            
            <h5><center>Прошедшие игры</center></h5>
            <hr>
            <div class="table-responsive">
                <table class='table table-striped table-hover table-sm align-middle' width='90%'>
                    <thead>
                        <tr>
                            <th scope="row">Игра</th>
                            <th scope="row">Дата игры</th>
                        </tr>
                    </thead>
                    <tbody>
            <?
                $res = mysql_query("
                    SELECT 
                        gi.id,
                        gi.number, 
                        gi.date_open, 
                        gi.date_game,
                        gt.name
                    FROM games_info as gi 
                    LEFT JOIN game_types as gt 
                        ON gt.id = gi.game_type 
                    WHERE gi.date_game<now() 
                    ORDER BY gi.date_game DESC
                ",$db);
                
                while ($row = mysql_fetch_array($res)){
                    $idGameInfo = $row['id'];
                    $number = $row['number'];
                    $gameDate = $row['date_game'];
                    $gameName = str_replace("{number}", $number, $row['name']);
                    ?>
                        <tr onclick="getGameInfo(<? echo $idGameInfo; ?>);">
                            <td><? echo $gameName; ?></td>
                            <td><? echo goodDate($gameDate); ?></td>
                        </tr>
                    <?
                }
                ?>
                    </tbody>
                </table>
            </div>
        <?
    }
    
    function addTeam(){
        global $db;
        
        $gameId = $_GET['id_game'];
        
        $res = mysql_query("
            SELECT 
                `bar_ids`, 
                `min_people`, 
                `max_people` 
            FROM `games_info` 
            WHERE `id`='".$gameId."'
        ",$db);
        $row = mysql_fetch_array($res);
        $barIds = $row['bar_ids'];
        $minPeople = $row['min_people'];
        $maxPeople = $row['max_people'];
        ?>
            <a href="?page=registration&id_game=<?= $gameId ?>">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a> 
            <script>
                function saveTeamInfo(){
                    var barId = $('input[name=bars]:checked').val();
                    var team = $('input[name=teamName]').val();
                    var players = $('input[name=players]').val();
                    var cap = $('input[name=capName]').val();
                    var email = $('input[name=email]').val();
                    var phone = $('input[name=phone]').val();
                    var social = $('input[name=social]').val();
                    var comment = $('textarea[name=comment]').val();
                    var reserve = $('input[name=reserve]:checked').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addTeam', gameId: <?= $gameId ?>, bar: barId, team: team, players: players, cap: cap, emailTeam: email, phone: phone, social: social, comment: comment, reserve: reserve},
                    	success: function(teamId){
                            document.location.href = '?page=team&id_team='+teamId+'&id_game=<?= $gameId ?>';
                    	},
                    	error: function (jqXHR, exception) {
                    		$('#main-panel').empty();
                    		$('#main-panel').html("<h2><center>Произошла ошибка. Попробуйте позже</center></h2>");
                    	}
                    });
                }
            </script>
            <div class="form-row">
                <div class="col">
                    <h6>Бар игры:</h6>
                    <?
                        $j=1;
                        $result20 = mysql_query("
                            SELECT * 
                            FROM `bars` 
                            WHERE `id` IN (".str_replace("/", ",", $barIds).")
                        ",$db);
                        while ($row6 = mysql_fetch_array($result20)){
                            $idBar=$row6['id'];
                            $nameBar=$row6['name'];
                            $maxTeam=$row6['max_teams'];
                            $isCheck = ($j == 1) ? "checked" : "";
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bars" id="<?= $idBar ?>" value="<?= $idBar ?>" <?= $isCheck ?>>
                                    <label class="form-check-label" for="<?= $idBar ?>">
                                        <?= $nameBar ?>
                                    </label>
                                </div>
                            <?
                            $j++;
                        }
                    ?>
                </div>
            </div>
            <br>

            <div class="form-row">
                <div class="col">
                    <h6>Название команды:</h6>
                    <input type="text" name="teamName" class="form-control is-valid" placeholder="Название команды" required>
                </div>
            </div>
            <br>
            
            <div class="form-row">
                <div class="col">
                    <h6>Количество игроков: <small>от <?= $minPeople ?> до <?= $maxPeople ?></small></h6>
                    <input class="form-control is-valid" id="players" type="number" name="players" min="<?= $minPeople ?>" max="<?= $maxPeople ?>" step="1" value="<?= $minPeople ?>" style="width:100%" required>
                </div>
            </div>
            <br>
    
            <div class="form-row">
                <div class="col">
                    <h6>Имя капитана:</h6>
                    <input type="text" name="capName" id="cap" class="form-control is-valid" id="validationServer02" placeholder="Имя капитана" required>
                </div>
            </div>
            <br>
            
            <div class="form-row">
                <div class="col">
                    <h6>E-mail:</h6>
                    <input type="email" name="email" class="form-control is-valid" placeholder="Электронная почта капитана">
                </div>
            </div>
            <br>

            <div class="form-row">
                <div class="col">
                    <h6>Номер телефона:</h6>
                    <input type="phone" name="phone" id="phone" class="form-control is-valid" placeholder="Телефон капитана">
                    <script>
                        window.addEventListener("DOMContentLoaded", function() {
                            function setCursorPosition(pos, elem) {
                                elem.focus();
                                if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
                                else if (elem.createTextRange) {
                                    var range = elem.createTextRange();
                                    range.collapse(true);
                                    range.moveEnd("character", pos);
                                    range.moveStart("character", pos);
                                    range.select()
                                }
                            }
                            function mask(event) {
                                var matrix = "+7(___)___-__-__",
                                    i = 0,
                                    def = matrix.replace(/\D/g, ""),
                                    val = this.value.replace(/\D/g, "");
                                if (def.length >= val.length) val = def;
                                this.value = matrix.replace(/./g, function(a) {
                                    return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
                                });
                                if (event.type == "blur") {
                                } else setCursorPosition(this.value.length, this)
                            };
                                var input = document.querySelector("#phone");
                                input.addEventListener("input", mask, false);
                                input.addEventListener("focus", mask, false);
                                input.addEventListener("blur", mask, false);
                            });

                    </script>
                </div>
            </div>
            <br>
            
            <div class="form-row">
                <div class="col">
                    <h6>Соц. сеть:</h6>
                    <input type="social" name="social" class="form-control" placeholder="Вк, Instagram, Facebook" >
                </div>
            </div>
            <br>

            <div class="form-row">
                <div class="col">
                    <h6>Комментарий:</h6>
                    <textarea type="text" name="comment" class="form-control" rows="3" placeholder="Стол или именинник обычно" width="100%"></textarea>
                </div>
            </div>
            <br>

            <div class="form-row">
                <div class="col">
                    <h6>Резерв:</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reserve" id="reserve1" value="1">
                        <label class="form-check-label" for="reserve1">
                            Да
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reserve" id="reserve0" value="0" checked>
                        <label class="form-check-label" for="reserve0">
                            Нет
                        </label>
                    </div>
                </div>
            </div>
            <br>
            
            <div class="form-row">
                <div class="col">
                    <button class="btn btn-success" onclick="saveTeamInfo()">
                        Сохранить
                    </button> 
                </div>
            </div>
            <br>
        <?
    }
    
    function getTeamInfo(){
        global $db;
        
        $idTeam = $_GET['id_team'];
        $returnGameId = $_GET['id_game'];
        
        $res = mysql_query("
        SELECT 
            gd.team_name, 
            b.name as bar_name, 
            gd.players, 
            gd.captain_name, 
            gd.phone,
            gd.comment, 
            gd.is_reserve, 
            rd.total, 
            rd.score_games, 
            (SELECT gi.number FROM games_data as gd2 LEFT JOIN games_info as gi ON gi.id = gd2.game_info_id WHERE gd2.team_name = gd.team_name AND gd2.game_info_id<>gd.game_info_id ORDER BY gd2.game_info_id DESC LIMIT 1) as lastRegNum, 
            (SELECT gt.name FROM games_data as gd2 LEFT JOIN games_info as gi ON gi.id = gd2.game_info_id LEFT JOIN game_types as gt ON gt.id = gi.game_type WHERE gd2.team_name = gd.team_name AND gd2.game_info_id<>gd.game_info_id ORDER BY gd2.game_info_id DESC LIMIT 1) as lastRegName, 
            (SELECT gi.min_people FROM games_data as gd2 LEFT JOIN games_info as gi ON gi.id = gd2.game_info_id WHERE gd2.team_name = gd.team_name AND gd2.game_info_id<>gd.game_info_id ORDER BY gd2.game_info_id DESC LIMIT 1) as minPeople, 
            (SELECT gi.max_people FROM games_data as gd2 LEFT JOIN games_info as gi ON gi.id = gd2.game_info_id WHERE gd2.team_name = gd.team_name AND gd2.game_info_id<>gd.game_info_id ORDER BY gd2.game_info_id DESC LIMIT 1) as maxPeople, 
            (SELECT avg(gd2.players) FROM games_data as gd2 WHERE gd2.team_name = gd.team_name) as averagePlayers, 
            (SELECT gi.bar_ids FROM games_data as gd2 LEFT JOIN games_info as gi ON gd2.game_info_id=gi.id WHERE gd2.id=gd.id) as bar_ids,
            (SELECT count(gd2.id) FROM games_data as gd2 WHERE gd2.team_name = gd.team_name) as countGames 
        FROM games_data as gd 
        LEFT JOIN bars as b 
            ON b.id = gd.bar_id 
        LEFT JOIN ranks_data as rd 
            ON rd.team_name = gd.team_name 
        WHERE 
            gd.id='".$idTeam."' 
        ORDER BY 
            rd.id DESC LIMIT 1
        ",$db);
        $row = mysql_fetch_array($res);
        $teamName = $row['team_name'];
        $barName = $row['bar_name'];
        $countPlayers = $row['players'];
        $capName = $row['captain_name'];
        $phone = $row['phone'];
        $comment = $row['comment'];
        $reserve = $row['is_reserve'];
        $totalScore = $row['total'];
        $scoresInGames = $row['score_games'];
        $lastRegNum = $row['lastRegNum'];
        $lastRegName = str_replace("{number}", $lastRegNum, $row['lastRegName']);
        $minPeople = $row['minPeople'];
        $maxPeople = $row['maxPeople'];
        $avgPlayers = $row['averagePlayers'];
        $countGames = $row['countGames'];
        $barIds = $row['bar_ids'];
        
        $allScores = explode(";", $scoresInGames);
        $countGamesInSeason = 0;
        for ($i = 0; $i < count($allScores); $i++)
            if ($allScores[$i]!=0) 
                $countGamesInSeason++;
        
        
        $phoneLink = "tel:".$phone;
        $emailLink = "mailto:".$email;
        if (strpos($social, "http") === false && !empty($social))
            $social = "https://".$social;
        ?>
            <a href="?page=registration&id_game=<?= $returnGameId ?>">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveTeamInfo(){
                    var bar = $('input[name=bars]:checked').val();
                    var nameTeam = $('input[name=teamName]').val();
                    var players = $('input[name=players]').val();
                    var cap = $('input[name=capName]').val();
                    var phone = $('input[name=phone]').val();
                    var comment = $('textarea[name=comment]').val();
                    var reserve = $('input[name=reserve]:checked').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'saveTeam', teamId: <?= $idTeam ?>, barId: bar, nameTeam: nameTeam, players: players, captain: cap, phone: phone, emailCap: "", social: "", comment: comment, reserve: reserve,},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=team&id_team='+<?= $idTeam ?>+'&id_game=<?= $returnGameId ?>';
                            }
                    	}
                    });
                }
                
                function deleteTeam(){
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'deleteTeam', teamId: <?= $idTeam ?>},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=registration&id_game=<?= $returnGameId ?>';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                };
            </script>
            
            <nav class="nav nav-pills nav-fill d-flex justify-content-center">
                <button class="nav-link active" id="view-team-tab" data-bs-toggle="tab" data-bs-target="#view-team" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Просмотр</button>
                <button class="nav-link" id="edit-team-tab" data-bs-toggle="tab" data-bs-target="#edit-team" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Изменение</button>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="view-team" role="tabpanel" aria-labelledby="view-team-tab">
                    <center>
                        <br>
                        <h3>
                            <?= $teamName ?>
                        </h3>
                        <b>Бар игры:</b> <?= $barName ?><br>
                        <b>Игроков:</b> <?= $countPlayers ?><br>
                        <b>Капитан:</b> <?= $capName ?><br>
                        <b>Контактный телефон:</b> <a href="<?= $phoneLink ?>"><?= $phone ?></a><br>
                        <b>Комментарий:</b> <?= (empty($comment) ? "<i>[пусто]</i>" : $comment) ?><br>
                        <button onclick="deleteTeam()" class="btn btn-danger">Удалить команду</button>
                        <hr>
                        <h5>
                            Статистика
                        </h5>
                        <b>В текущем сезоне:*</b> <?= ($totalScore == 0 ? "не принимала участия или не найдена" : $totalScore." баллов (".$countGamesInSeason." игр)") ?><br>
                        <b>Последняя регистрация:*</b> <?= $lastRegName ?><br>
                        <b>Среднее количество игроков:*</b> <?= $avgPlayers ?><br>
                        <b>Принималось участие:*</b> <?= $countGames ?> игр<br>
                        <br>
                        <i>* - команда могла менять название или записываться похожим образом (Breakin' bud и Breakin'BUD воспринимаются как различные команды)</i>
                    </center>
                </div>
                <div class="tab-pane fade" id="edit-team" role="tabpanel" aria-labelledby="edit-team-tab">
                    <div class="form-row">
                        <div class="col">
                            <h6>Бар игры:</h6>
                            <?
                                $j=1;
                                $result20 = mysql_query("
                                    SELECT * 
                                    FROM `bars` 
                                    WHERE `id` IN (".str_replace("/", ",", $barIds).")
                                ",$db);
                                while ($row6 = mysql_fetch_array($result20)) {
                                    $idBar=$row6['id'];
                                    $nameBar=$row6['name'];
                                    $maxTeam=$row6['max_teams'];
                                    $isCheck = ($j == 1) ? "checked" : "";
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bars" id="<?= $idBar ?>" value="<?= $idBar ?>" <?= $isCheck ?>>
                                            <label class="form-check-label" for="<?= $idBar ?>">
                                                <?= $nameBar ?>
                                            </label>
                                        </div>
                                    <?
                                    $j++;
                                }
                            ?>
                        </div>
                    </div>
                    <br>
        
                    <div class="form-row">
                        <div class="col">
                            <h6>Название команды:</h6>
                            <input type="text" name="teamName" class="form-control is-valid" placeholder="Название команды" value="<?= $teamName ?>" required>
                        </div>
                    </div>
                    <br>
                    
                    <div class="form-row">
                        <div class="col">
                            <h6>Количество игроков: <small>от <?= $minPeople ?> до <?= $maxPeople ?></small></h6>
                            <input class="form-control is-valid" id="players" type="number" name="players" min="<?= $minPeople ?>" max="<?= $maxPeople ?>" step="1" value="<?= $countPlayers ?>" style="width:100%" required>
                        </div>
                    </div>
                    <br>
            
                    <div class="form-row">
                        <div class="col">
                            <h6>Имя капитана:</h6>
                            <input type="text" name="capName" id="cap" class="form-control is-valid" id="validationServer02" placeholder="Имя капитана"  value="<?= $capName ?>" required>
                        </div>
                    </div>
                    <br>
        
                    <div class="form-row">
                        <div class="col">
                            <h6>Номер телефона:</h6>
                            <input type="phone" name="phone" id="phone" class="form-control is-valid" id="validationServer02" placeholder="Телефон капитана" value="<?= $phone ?>" required>
                            <script>
                                window.addEventListener("DOMContentLoaded", function() {
                                    function setCursorPosition(pos, elem) {
                                        elem.focus();
                                        if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
                                        else if (elem.createTextRange) {
                                            var range = elem.createTextRange();
                                            range.collapse(true);
                                            range.moveEnd("character", pos);
                                            range.moveStart("character", pos);
                                            range.select()
                                        }
                                    }
                                    function mask(event) {
                                        var matrix = "+7(___)___-__-__",
                                            i = 0,
                                            def = matrix.replace(/\D/g, ""),
                                            val = this.value.replace(/\D/g, "");
                                        if (def.length >= val.length) val = def;
                                        this.value = matrix.replace(/./g, function(a) {
                                            return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
                                        });
                                        if (event.type == "blur") {
                                        } else setCursorPosition(this.value.length, this)
                                    };
                                        var input = document.querySelector("#phone");
                                        input.addEventListener("input", mask, false);
                                        input.addEventListener("focus", mask, false);
                                        input.addEventListener("blur", mask, false);
                                    });

                            </script>
                        </div>
                    </div>
                    <br>
                    
                    <div class="form-row">
                        <div class="col">
                            <h6>Комментарий:</h6>
                            <textarea type="text" name="comment" class="form-control" rows="3" placeholder="Стол или именинник обычно" width="100%" ><?= $comment ?></textarea>
                        </div>
                    </div>
                    <br>
        
                    <div class="form-row">
                        <div class="col">
                            <h6>Резерв:</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reserve" id="reserve1" value="1" <?= ($reserve == 1 ? "checked" : "") ?>>
                                <label class="form-check-label" for="reserve1">
                                    Да
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reserve" id="reserve0" value="0" <?= ($reserve == 0 ? "checked" : "") ?>>
                                <label class="form-check-label" for="reserve0">
                                    Нет
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="form-row">
                        <div class="col">
                            <button class="btn btn-success" onclick="saveTeamInfo()">
                                Сохранить
                            </button> 
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        <?
    }