<?php

    function createGame() {
        global $db;
        
        ?>
            <a href="?page=games">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveGameInfo(){
                    var isFirst = true;
                    var barList = "";
                    $('.bar-check:checkbox').each(function () {
                        barList += (isFirst ? (this.checked ? $(this).val() : "") : (this.checked ? "/"+$(this).val() : ""));
                        isFirst = false;
                    });
                    var gameType = $('input[name=game_type]:checked').val();
                    var gameNumber = "#"+$('input[name=gameNumber]').val();
                    var cost = $('input[name=cost]').val();
                    var minPeople = $('input[name=minPeople]').val();
                    var maxPeople = $('input[name=maxPeople]').val();
                    var openDate = $('input[name=openDateTime]').val();
                    var gameDate = $('input[name=gameDateTime]').val();
                    
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addNewGame', bars: barList, gameType: gameType, gameNumber: gameNumber, cost: cost, minPeople: minPeople, maxPeople: maxPeople, openDate: openDate, gameDate: gameDate,},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=games';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                }
            </script>
            <h5 style="margin-top:-27px;"><center>Создание игры</center></h5>
            <hr>
            <b>Тип игры:</b>
            <br>
        <?
        $res = mysql_query("
            SELECT 
                `id`, 
                `name` 
            FROM `game_types`
        ",$db);
        
        $isFirst = true;
        while ($row = mysql_fetch_array($res)) {
            $gameTypeId = $row['id'];
            $gameName = str_replace("{number}", "", $row['name']);
            ?>
                <input type="radio" name="game_type" class="form-check-input" value="<?= $gameTypeId ?>" id="game_type_<?= $gameTypeId ?>" required<?= ($isFirst ? " checked" : "") ?>>
                <label class="form-check-label" for="game_type_<?= $gameTypeId ?>"><?= $gameName ?></label>
                <br>
            <?
            $isFirst = false;
        }
        
        ?>
            <br>
            <b>Бары игры:</b>
            <br>
        <?
        
        $lastGameBarsIds = explode("/", mysql_fetch_array(mysql_query("
            SELECT `bar_ids`
            FROM `games_info` 
            ORDER BY `date_game` 
            DESC LIMIT 1
        ",$db))['bar_ids']);
        
        $res = mysql_query("
            SELECT 
                `id`, 
                `name` 
            FROM `bars` 
            WHERE `is_active`=1
        ",$db);
        
        while ($row = mysql_fetch_array($res)){
            $idBar = $row['id'];
            $nameBar = $row['name'];
            ?>
                <input type="checkbox" name="newBar[]" class="form-check-input bar-check" value="<?= $idBar ?>" id="barCheck<?= $idBar ?>"<?= (in_array($idBar, $lastGameBarsIds) ? " checked" : "") ?>>
                <label class="form-check-label" for="barCheck<?= $idBar ?>"><?= $nameBar ?></label>
                <br>
            <?
        }
        
        ?>
            <br>
            <b>Номер игры: </b><small>знак # будет подставлен автоматически</small>
            <br>
        <?
        $res = mysql_query("
            SELECT 
                `number`, 
                `cost`, 
                `min_people`, 
                `max_people`, 
                `date_open`, 
                `date_game`
            FROM `games_info` 
            ORDER BY `date_game` 
            DESC LIMIT 1
        ",$db);
        
        while ($row = mysql_fetch_array($res)){
            $gameNumber = $row['number'];
            $cost = $row['cost'];
            $minPeople = $row['min_people'];
            $maxPeople = $row['max_people'];
            $dateOpen = $row['date_open'];
            $dateGame = $row['date_game'];
            $nextNumber = ((int)explode("#", $gameNumber)[1])+1;
            $dateOpen = date('Y-m-d H:i', strtotime($dateOpen . ' +2 week'));
            $dateGame = date('Y-m-d H:i', strtotime($dateGame . ' +2 week'));
            
            ?>
            <input type="number" name="gameNumber" class="form-control is-valid" value="<?= $nextNumber ?>" required>
                
            <br>
            <b>Стоимость (₽): </b>
            <br>
            <input type="number" name="cost" class="form-control is-valid" value="<?= $cost ?>" required>
            
            <br>
            <b>Минимум ироков: </b>
            <br>
            <input type="number" name="minPeople" class="form-control is-valid" value="<?= $minPeople ?>" required>
            
            <br>
            <b>Максимум игроков: </b>
            <br>
            <input type="number" name="maxPeople" class="form-control is-valid" value="<?= $maxPeople ?>" required>
            
            <br>
            <b>Открытие регистрации: </b>
            <br>
            <input type="datetime-local" name="openDateTime" class="form-control is-valid" value="<?= "".datetimeValue("".$dateOpen) ?>" required>
            
            <br>
            <b>Дата игры: </b>
            <br>
            <input type="datetime-local" name="gameDateTime" class="form-control is-valid" value="<?= "".datetimeValue("".$dateGame) ?>" required>
            <br>
            <?
        }
        ?>
            <div class="form-row">
                <div class="col">
                    <button class="btn btn-success btn-lg btn-block" onclick="saveGameInfo()">
                        Создать
                    </button> 
                </div>
            </div>
            <br>
        <?
    }
    
    function getGames(){
        global $db;
        
        $res = mysql_query("
            SELECT 
                gi.id, 
                gi.number, 
                gi.date_open, 
                gi.date_game, 
                gi.cost, 
                gi.bar_ids, 
                gi.min_people, 
                gi.max_people, 
                gt.name, 
                gt.back_img, 
                gt.friendly_url,
                (SELECT l.link FROM links as l WHERE l.category='navigation' AND l.name='reg') as link
            FROM games_info as gi 
            LEFT JOIN game_types as gt 
                ON gt.id = gi.game_type 
            WHERE gi.date_game > now() 
            ORDER BY gi.date_open
        ",$db);
        
        ?>
            <h5><center>Предстоящие игры</center></h5><hr>
            <a href="?page=create_game" class="btn btn-success">Добавить игру</a>
            <div class="row d-flex justify-content-center">
        <?
        
        while ($row = mysql_fetch_array($res)){
            $gameId = $row['id'];
            $gameNumber = $row['number'];
            $dateOpen = $row['date_open'];
            $dateGame = $row['date_game'];
            $cost = $row['cost'];
            $barIds = $row['bar_ids'];
            $minPeople = $row['min_people'];
            $maxPeople = $row['max_people'];
            $nameGame = $row['name'];
            $backImg = $row['back_img'];
            $urlName = $row['friendly_url'];
            $link = $row['link'];
            
            $nameGame = str_replace("{number}", $gameNumber, $nameGame);
            $gameUrl = $link.$urlName."-".explode("#", $gameNumber)[1];
            $peopleRow = "от ".$minPeople." до ".$maxPeople;
            $state = ($dateOpen < date() ? "регистрация открыта" : "регистрация закрыта");
            ?>
                <div class="card" style="max-width: 19rem;margin:5px;">
                    <center>
                        <img src="<?= $backImg ?>" class="card-img-top" style="height:50px;width:100%;" alt="<?= $nameGame ?>">
                    </center>
                  
                    <div class="card-body">
                        <h5 class="card-title"><center><?= $nameGame ?></center></h5>
                        <p class="card-text">
                            <b>Игроков:</b> <?= $peopleRow ?><br>
                            <b>Ссылка:</b> <a href="<?= $gameUrl ?>" target="_blank"><?= $gameUrl ?></a><br>
                            <b>Дата открытия:</b> <?= goodDate($dateOpen."") ?><br>
                            <b>Дата игры:</b> <?= goodDate($dateGame."") ?><br>
                        </p>
                    </div>
                    <div class="card-footer" style="background:white;">
                        <center>
                            <a href="?page=game&id_game=<?= $gameId ?>" class="btn btn-primary">Просмотреть и изменить</a>
                        </center>
                    </div>
                </div>
            <?
        }
        ?>
            </div><br>
        <?
    }
    
    function getGameInfo() {
        global $db;
        
        $gameId = $_GET['id_game'];
        ?>
            <a href="?page=games">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a> 
            <script>
                function saveGameInfo(){
                    var isFirst = true;
                    var barList = "";
                    $('.bar-check:checkbox').each(function () {
                        barList += (isFirst ? (this.checked ? $(this).val() : "") : (this.checked ? "/"+$(this).val() : ""));
                        if (this.checked) {
                            isFirst = false;
                        }
                    });
                    var gameType = $('input[name=game_type]:checked').val();
                    var gameNumber = "#"+$('input[name=gameNumber]').val();
                    var cost = $('input[name=cost]').val();
                    var minPeople = $('input[name=minPeople]').val();
                    var maxPeople = $('input[name=maxPeople]').val();
                    var openDate = $('input[name=openDateTime]').val();
                    var gameDate = $('input[name=gameDateTime]').val();
                    
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'updateGame', gameId: <?= $gameId ?>, bars: barList, gameType: gameType, gameNumber: gameNumber, cost: cost, minPeople: minPeople, maxPeople: maxPeople, openDate: openDate, gameDate: gameDate},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=games';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                }
            </script>
            <h5 style="margin-top: -27px;">
                <center>Изменение данных игры</center>
            </h5>
            <hr>
            <b>Тип игры:</b>
            <br>
        <?
        $gameType = mysql_fetch_array(mysql_query("
            SELECT `game_type`
            FROM `games_info`
            WHERE `id`='".$gameId."'
        ",$db))['game_type'];
        
        $res = mysql_query("
            SELECT 
                `id`, 
                `name` 
            FROM `game_types`
        ",$db);
        
        while ($row = mysql_fetch_array($res)) {
            $gameTypeId = $row['id'];
            $gameName = str_replace("{number}", "", $row['name']);
            ?>
                <input type="radio" name="game_type" class="form-check-input" value="<?= $gameTypeId ?>" id="game_type_<?= $gameTypeId ?>" <?= ($gameType == $gameTypeId ? "checked" : "") ?>>
                <label class="form-check-label" for="game_type_<? echo $gameTypeId; ?>"><?= $gameName ?></label>
                <br>
            <?
        }
        ?>
            <br>
            <b>Бары игры:</b>
            <br>
        <?
        
        $lastGameBarsIds = explode("/", mysql_fetch_array(mysql_query("
            SELECT `bar_ids`
            FROM `games_info` 
            WHERE `id`='".$gameId."'
        ",$db))['bar_ids']);
        
        $res = mysql_query("
            SELECT 
                `id`, 
                `name` 
            FROM `bars` 
            WHERE `is_active`=1
        ",$db);
        
        while ($row = mysql_fetch_array($res)) {
            $idBar = $row['id'];
            $nameBar = $row['name'];
            ?>
                <input type="checkbox" name="newBar[]" class="form-check-input bar-check" value="<?= $idBar ?>" id="barCheck<?= $idBar ?>" <?= (in_array($idBar, $lastGameBarsIds) ? "checked" : "") ?>>
                <label class="form-check-label" for="barCheck<?= $idBar ?>"><?= $nameBar ?></label>
                <br>
            <?
        }
        
        ?>
            <br>
            <b>Номер игры: </b><small>знак # будет подставлен автоматически</small>
            <br>
        <?
        $res = mysql_query("
            SELECT 
                `number`, 
                `cost`, 
                `min_people`, 
                `max_people`, 
                `date_open`, 
                `date_game`
            FROM `games_info` 
            WHERE `id`='".$gameId."'
        ",$db);
        
        while ($row = mysql_fetch_array($res)){
            $gameNumber = $row['number'];
            $cost = $row['cost'];
            $minPeople = $row['min_people'];
            $maxPeople = $row['max_people'];
            $dateOpen = $row['date_open'];
            $dateGame = $row['date_game'];
            $nextNumber = ((int)explode("#", $gameNumber)[1]);
            
            ?>
            <input type="number" name="gameNumber" class="form-control is-valid" value="<?= $nextNumber ?>" required>
                
            <br>
            <b>Стоимость (₽): </b>
            <br>
            <input type="number" name="cost" class="form-control is-valid" value="<?= $cost ?>" required>
            
            <br>
            <b>Минимум ироков: </b>
            <br>
            <input type="number" name="minPeople" class="form-control is-valid" value="<?= $minPeople ?>" required>
            
            <br>
            <b>Максимум игроков: </b>
            <br>
            <input type="number" name="maxPeople" class="form-control is-valid" value="<?= $maxPeople ?>" required>
            
            <br>
            <b>Открытие регистрации: </b>
            <br>
            <input type="datetime-local" name="openDateTime" class="form-control is-valid" value="<?= datetimeValue("".$dateOpen) ?>" required>
            
            <br>
            <b>Дата игры: </b>
            <br>
            <input type="datetime-local" name="gameDateTime" class="form-control is-valid" value="<?= datetimeValue("".$dateGame) ?>" required>
            <br>
            <?
        }
        ?>
            <div class="form-row">
                <div class="col">
                    <button class="btn btn-success btn-lg btn-block" onclick="saveGameInfo()">
                        Сохранить
                    </button> 
                </div>
            </div>
            <br>
        <?
    }
    