<?php
    function getRank() {
        global $db;
        
        ?>
            <h5><center>Все рейтинги</center></h5><hr>
            <a href="?page=create_rank" class="btn btn-success">Новый рейтинг</a>
            <div class="row d-flex justify-content-center">
        <?
        
        $res = mysql_query("
            SELECT * 
            FROM ranks as r 
            ORDER BY r.last_game DESC
        ",$db);
        while ($row = mysql_fetch_array($res)) {
            $rankId = $row['id'];
            $rankName = $row['season_name'];
            $firstGame = $row['first_game'];
            $lastGame = $row['last_game'];
            ?>
                <div class="card" style="max-width: 19rem;margin:5px;">
                    <div class="card-body">
                        <h5 class="card-title"><center><?= $rankName ?></center></h5>
                        <p class="card-text">
                            <b>Первая игра:</b> #<?= $firstGame ?><br>
                            <b>Последняя игра:</b> #<?= $lastGame ?><br>
                        </p>
                    </div>
                    <div class="card-footer" style="background:white;">
                        <center>
                            <a href="?page=rank&id_rank=<?= $rankId ?>" class="btn btn-primary">Изменить данные</a>
                        </center>
                    </div>
                </div>
            <?
        }
        ?> 
            </div><br>
        <?
    }
    
    function getRankInfo() {
        global $db;
        
        $idRank = $_GET['id_rank'];
        
        $res = mysql_query("
            SELECT 
                r.season_name, 
                r.first_game, 
                r.last_game, 
                (SELECT gi.number FROM games_info as gi ORDER BY abs(gi.date_game-now()) LIMIT 1) as last_game_number 
            FROM ranks as r 
            WHERE r.id=".$idRank
        ,$db);
        $row = mysql_fetch_array($res);
        $rankName = $row['season_name'];
        $firstGame = $row['first_game'];
        $lastGame = $row['last_game'];
        $lightedNumber = explode("#", $row['last_game_number'])[1];
        
        ?>
            <a href="?page=ranks">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                $(function(){
                    $('.redactTeam').click(function(){
                        var idTeam = $(this).data('id');
                        var score = "";
                        var tot = 0;
                        var tot_sorted = "";
                        $('.teamScore-' + idTeam).each(function(){
                            var thisval = $(this).val();
                            score += thisval + ";";
                            tot = tot + Number(thisval);
                            if (Number(thisval) !== 0)
                                tot_sorted = tot_sorted + Number(thisval) + "^" + Number($(this).data('pow')) + ";";
                            
                        });
                        var newName = $('.teamName-' + idTeam).val();
                        $.ajax({
                        	url: 'ajax.php',
                        	method: 'post',
                        	dataType: 'text',
                        	data: {activity: 'redactRank', idTeam: idTeam, rankId: <?= $idRank ?>, scoreGames: score, total: tot, total_sort_sum: tot_sorted, newName: newName},
                        	success: function(data){
                                if (data == "ok") {
                                    document.location.href = '?page=rank&id_rank=<?= $idRank ?>';
                                } else {
                                    alert(data);
                                }
                        	}
                        });
                    });
                    
                    $('.deleteTeam').click(function(){
                        var idTeam = $(this).data('id');
                        $.ajax({
                        	url: 'ajax.php',
                        	method: 'post',
                        	dataType: 'text',
                        	data: {activity: 'deleteRankTeam', idTeam: idTeam, rankId: <?= $idRank ?>},
                        	success: function(data){
                                if (data == "ok") {
                                    document.location.href = '?page=rank&id_rank=<?= $idRank ?>';
                                } else {
                                    alert(data);
                                }
                        	}
                        });
                    });
                
                    function onlyDigits(elem) {
                        elem.value = elem.value.replace(/[^\d\.\-]/g, "");//разрешаем ввод только цифр 0-9, запятой и минуса
    
              			if(elem.value.lastIndexOf("-")> 0) {//если пользователь вводит тире (минус) не самым первым символом...
                			elem.value = elem.value.substr(0, elem.value.lastIndexOf("-"));//то удаляем этот минус
              			}
              			
              			if(elem.value.lastIndexOf(".")== 0) {//если пользователь вводит точку самым первым символом...
                			elem.value = elem.value.substr(0, 0);//то удаляем её
              			}
              			
              			if(elem.value[0]== "-") {
                			if(elem.value.length>5) elem.value = elem.value.substr(0, 5);
            			}else{
                			if(elem.value.length>4) elem.value = elem.value.substr(0, 4);
            			}
          	            if(elem.value.match(/\./g)){
            			    if(elem.value.match(/\./g).length > 1) {//не даём ввести больше одной точки
                				elem.value = elem.value.substr(0, elem.value.lastIndexOf("."));
            			    }
          	            }
                    }
                });
            </script>
            <h5 style="margin-top:-27px;">
                <center>
                    Данные рейтинга "<?= $rankName ?>"
                </center>
            </h5>
            <hr>
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <?
                    $j=0;
                    $result2 = mysql_query("
                        SELECT * 
                        FROM `ranks_data` 
                        WHERE `rank_id`='$idRank' 
                        ORDER BY `total` DESC
                    ",$db);
                    while ($row2=mysql_fetch_array($result2)) {
                        $teamId = $row2['id'];
                        $teamName = $row2['team_name'];
                        $games = $row2['score_games'];
                        $total = $row2['total'];
                        $j++;
                        $games = explode(";", $games);
                        $tableRow = "";
                        $countGames = $games;
                        $cg = 0;
                        $gamesInRank = (int)count($countGames)-2;
                        for ($m = 0; $m < count($countGames); $m++)
                            if ($countGames[$m] != 0) 
                                $cg++;
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-heading-<?= $j ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $j ?>" aria-expanded="false" aria-controls="flush-collapse-<?= $j ?>">
                                        <div>
                                            <?= $j . "<b>" . $teamName . "</b> - " . $total . " (игр:&nbsp;" . $cg . ")" ?>
                                        </div>
                                    </button>
                                </h2>
                                <div id="flush-collapse-<?= $j; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading-<?= $j ?>" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <table class="table table-sm ">
                                            <thead>
                                                <tr>
                                                    <th scope="col-6">
                                                        <center>
                                                            Игра
                                                        </center>
                                                    </th>
                                                    <th scope="col-6">
                                                        <center>
                                                            Баллы
                                                        </center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody> 
                                                <?
                                                    $k = 0;
                                                    for ($i = $firstGame; $i <= $lastGame; $i++) {
                                                        ?>
                                                            <tr>
                                                                <th scope="row">
                                                                    <center>
                                                                        #<?= $i ?>
                                                                    </center>
                                                                </th>
                                                                <td>
                                                                    <input type="text" class="form-control teamScore-<?= $teamId ?>" onkeyup="onlyDigits(this)" value="<?= $games[$k] ?>" data-position="<?= $k ?>" data-pow="<?= 1/pow(10, $gamesInRank-$k) ?>">
                                                                </td>
                                                            </tr>
                                                        <?
                                                        $k++;
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                        <p>Изменить название:</p>
                                        <input type="text" class="form-control teamName-<?= $teamId ?>" value="<?= $teamName ?>"><br>
                                        <a type="button" data-id="<?= $teamId ?>" class="btn btn-primary redactTeam">
                                            Изменить
                                        </a>
                                        <a type="button" data-id="<?= $teamId ?>" class="btn btn-danger deleteTeam">
                                            Удалить
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?
                    }
                    $j = "new";
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-heading-<?= $j ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?= $j ?>" aria-expanded="false" aria-controls="flush-collapse-<?= $j ?>" style="background-color:green; color: white;">
                            <div>
                                <b>Добавить команду</b>
                            </div>
                        </button>
                    </h2>
                    <div id="flush-collapse-<?= $j ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading-<?= $j ?>" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col-6">
                                            <center>
                                                Игра
                                            </center>
                                        </th>
                                        <th scope="col-6">
                                            <center>
                                                Баллы
                                            </center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <?
                                        $k = 0;
                                        for ($i = $firstGame; $i <= $lastGame; $i++) {
                                            ?>
                                                <tr>
                                                    <th scope="row">
                                                        <center>
                                                            #<?= $i ?>
                                                        </center>
                                                    </th>
                                                    <td>
                                                        <input type="text" class="form-control teamScore-new" onkeyup="onlyDigits(this)" value="0" data-position="<?= $k ?>">
                                                    </td>
                                                </tr>
                                            <?
                                            $k++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <p>Название команды:</p>
                            <input type="text" class="form-control teamName-new" value=""><br>
                            <a type="button" data-id="new" class="btn btn-primary redactTeam">
                                Сохранить
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?
    }
    
    function createRank() {
        global $db;
        ?>
            <a href="?page=ranks">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveRankInfo(){
                    var seasonName = $('input[name=seasonName]').val();
                    var seasonTime = $('input[name=seasonTime]').val();
                    var firstGame = $('input[name=firstGame]').val();
                    var lastGame = $('input[name=lastGame]').val();
                    var is_season_first = ($('input[name=is_season_first]').is(':checked')) ? 1 : 0;
                    
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addNewRank', seasonName: seasonName, seasonTime: seasonTime, firstGame: firstGame, lastGame: lastGame, is_season_first: is_season_first},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=ranks';
                            } else {
                                alert(data)
                            }
                    	}
                    });
                }
            </script>
            <h5 style="margin-top:-27px;"><center>Создание нового рейтинга</center></h5>
            <hr>
            <b>Название сезона </b><small>без кавычек</small>
            <br>
            <input type="text" name="seasonName" class="form-control is-valid" placeholder="Например: Зимне-весенний" required>
            <?
                $firstGameNum = ((int)(mysql_fetch_array(mysql_query("SELECT `last_game` FROM `ranks` ORDER BY `id` DESC",$db))['last_game']) + 1);
                $lastGameNum = $firstGameNum + 9;
            ?>
            
            <br>
            <b>Временные рамки сезона: </b>
            <br>
            <input type="text" name="seasonTime" class="form-control is-valid" value="" placeholder="Например: лето 2022" required>
                
            <br>
            <b>Первая игра сезона: </b>
            <br>
            <input type="number" name="firstGame" class="form-control is-valid" value="<?= $firstGameNum ?>" required>
            
            <br>
            <b>Последняя игра сезона: </b>
            <br>
            <input type="number" name="lastGame" class="form-control is-valid" value="<?= $lastGameNum ?>" required>
            <br>
            <b>Слово "сезон" на странице до или после названия? </b><br>
            <small>Примеры<br>Отмечено: Сезон Достижений, Сезон Открытий<br>Не отмечено: Летний сезон, Круизный сезон</small>
            <br>
            <label><input type="checkbox" name="is_season_first" class="form-check-input" value="1" required> Да</label>
            <br>
            <br>
            <div class="form-row">
                <div class="col">
                    <button class="btn btn-success btn-lg btn-block" onclick="saveRankInfo()">
                        Создать
                    </button> 
                </div>
            </div>
            <br>
        <?
    }