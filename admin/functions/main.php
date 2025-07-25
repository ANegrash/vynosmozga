<?php

    function exitProfile() {
        session_unset();
        ?>
            <script>
                document.location.href = "/admin/";
            </script>
        <?
    }

    function manageImg() {
        $dir = $_GET['dir'];
        $ret = $_GET['ret'];
        
        $base_directory = '../img/';
        
        $directory = $base_directory . $dir;
        ?>
            <a href="?page=<?= ($ret ? $ret : "registrations") ?>">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a> 
            <script>
                function redactImg(m, nameFrom){
                    var nameTo = "<?= $directory."/" ?>" + $('#input-'+m).val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'redactImg', nameFrom: nameFrom, nameTo: nameTo},
                    	success: function(data){
                            if (data == 'ok')
                                document.location.href = "?page=img&dir=<?= $dir ?>&ret=<?= $ret ?>";
                            else
                                alert(data)
                    	}
                    });
                }
                function deleteImg(nameFrom){
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'deleteImg', nameFrom: nameFrom},
                    	success: function(data){
                            if (data == 'ok')
                                document.location.href = "?page=img&dir=<?= $dir ?>&ret=<?= $ret ?>";
                            else
                                alert(data)
                    	}
                    });
                }
                function uploadFile(){
                    let file = document.getElementById('file').files[0];
                    let formData = new FormData();
                    formData.append('stringKeyToGetValue', file);
                    
                    $.ajax({
                        url: 'uploads.php?dir=<?= $dir ?>',
                        method: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: (response) => {
                            document.location.href = "?page=img&dir=<?= $dir ?>&ret=<?= $ret ?>";
                        }
                    })
                }
            </script>
            
            <?
                if (!empty($dir) && is_dir($directory)) {
            ?>
            <center><h5>Изображения в папке "<span class="lead"><?= $dir ?></span>"</h5></center>
            <hr>
                <h6>Добавить изображение</h6>
                <p><input type="file" name="files" id="file"></p>
                <button class="btn btn-success" onclick="uploadFile()">Загрузить</button>
                <hr>
                <h6>Изображения на сервере</h6>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-10">
                        <div class="row d-flex justify-content-center">
                        <?
                            $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                            $m = 0;
                            foreach ($scanned_directory as $file_name) {
                                ?>
                                <div class="card" style="max-width: 16rem;margin:5px;">
                                    <img src="<?= $directory."/".$file_name ?>" class="card-img-top" alt="Картинка не грузится...">
                                    <div class="card-body">
                                        <input type="text" name="img-name" id="input-<?= $m ?>" class="form-control" value="<?= $file_name ?>">
                                    </div>
                                    <div class="card-footer" style="background:white;">
                                        <center>
                                          <button class="btn btn-primary" onclick="redactImg('<?= $m ?>', '<?= $directory."/".$file_name ?>')">Изменить</button>
                                          <button class="btn btn-danger" onclick="deleteImg('<?= $directory."/".$file_name ?>')">Удалить</button>
                                        </center>
                                    </div>
                                </div>
                                <?
                                $m++;
                            }
                        ?>
                        </div>
                    </div>
                    <div class="col-1"></div>
                </div>
            <?
                } else {
                    ?>
            <center><h5>Изображения на сервере</h5></center>
            <hr>
                <h6>Добавить изображение</h6>
                <p><input type="file" name="files" id="file"></p>
                <button class="btn btn-success" onclick="uploadFile()">Загрузить</button>
                <hr>
                <h6>Изображения на сервере</h6>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-10">
                        <div class="row d-flex justify-content-center">
                        <?
                            $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                            
                            $dirsList = [];
                            $filesList = [];
                            foreach ($scanned_directory as $file) {
                                    if(is_file($directory."/".$file))
                                        array_push($filesList, $file);
                                    else
                                        array_push($dirsList, $file);
                            }
                            
                            ?>
                                <p>Папки</p><hr>
                            <?
                            
                            foreach ($dirsList as $dir_name) {
                                ?>
                                    <div class="card" style="max-width: 16rem;margin:5px;">
                                        <div class="card-body">
                                            <p class="lead"><?= $dir_name ?></p>
                                        </div>
                                        <div class="card-footer" style="background:white;">
                                            <center>
                                                <a class="btn btn-primary" href="?page=img&dir=<?= $dir_name ?>&ret=img">Открыть</a>
                                            </center>
                                        </div>
                                    </div>
                                <?
                            } 
                            ?>
                            <br>
                            <p>Файлы</p><hr>
                            <?
                                
                            $m = 0;
                            foreach ($filesList as $file_name) {
                                ?>
                                    <div class="card" style="max-width: 16rem;margin:5px;">
                                        <img src="<?= $directory."/".$file_name ?>" class="card-img-top" alt="Картинка не грузится...">
                                        <div class="card-body">
                                            <input type="text" name="img-name" id="input-<?= $m ?>" class="form-control" value="<?= $file_name ?>">
                                        </div>
                                        <div class="card-footer" style="background:white;">
                                            <center>
                                              <button class="btn btn-primary" onclick="redactImg('<?= $m ?>', '<?= $directory."/".$file_name ?>')">Изменить</button>
                                              <button class="btn btn-danger" onclick="deleteImg('<?= $directory."/".$file_name ?>')">Удалить</button>
                                            </center>
                                        </div>
                                    </div>
                                <?
                                $m++;
                            }
                        ?>
                        </div>
                    </div>
                    <div class="col-1"></div>
                </div>
                    <?
                }
    }
    
    function search() {
        global $db;
        $searchingInput = $_GET['query'];
        
        if (!empty($searchingInput)){
            $names = array('team_name', 'captain_name', 'phone', 'email');
            $toreturn = "
            <table class='table table-striped' width='90%'>
                <tr>
                    <td><strong>Игра</strong></td>
                    <td><strong>Бар</strong></td>
                    <td><strong>Игроков</strong></td>
                    <td><strong>Название команды</strong></td>
                    <td><strong>Капитан</strong></td>
                    <td><strong>Телефон</strong></td>
                    <td><strong>E-mail</strong></td>
                    <td><strong>Соц. сети</strong></td>
                    <td><strong>Комментарий</strong></td>
                </tr>
            ";
            $j = 0;
            for ($k = 0; $k < count($names); $k++){
                $res2 = mysql_query("
                SELECT gi.number, gt.name, b.name as bar_name, gd.players, gd.team_name, gd.captain_name, gd.phone, gd.email, gd.social, gd.comment, gd.is_reserve 
                FROM games_data as gd 
                LEFT JOIN bars as b ON b.id = gd.bar_id 
                LEFT JOIN games_info as gi ON gi.id = gd.game_info_id 
                LEFT JOIN game_types as gt ON gt.id = gi.game_type 
                WHERE gd.".$names[$k]."  LIKE '%".$searchingInput."%'
                ",$db);
                
                while ($row = mysql_fetch_array($res2)){
                    $ajax_nameGame = str_replace("{number}", $row['number'], $row['name']);
                    $ajax_nameBar = $row['bar_name'];
                    $ajax_players = $row['players'];
                    $ajax_teamName = ($row['is_reserve'] == 1 ? $row['team_name']." <font color=\"red\">(резерв)</font>" : $row['team_name']);
                    $ajax_capName = $row['captain_name'];
                    $ajax_phone = $row['phone'];
                    $ajax_email = $row['email'];
                    $ajax_social = $row['social'];
                    $ajax_comment = $row['comment'];
                    $ajax_reserve = $row['is_reserve'];
                    
                    switch ($names[$k]) {
                        case $names[0]: $ajax_teamName = "<b>".$ajax_teamName."</b>"; break;
                        case $names[1]: $ajax_capName = "<b>".$ajax_capName."</b>"; break;
                        case $names[2]: $ajax_phone = "<b>".$ajax_phone."</b>"; break;
                        case $names[3]: $ajax_email = "<b>".$ajax_email."</b>"; break;
                    }
                    $j++;
                    $toreturn .= "<tr><td>$ajax_nameGame</td><td>$ajax_nameBar</td><td>$ajax_players</td><td>$ajax_teamName</td><td>$ajax_capName</td><td>$ajax_phone</td><td>$ajax_email</td><td><a href=\"$ajax_social\" target=\"_blank\">$ajax_social</td><td>$ajax_comment</td></tr>";
                }
            }
            $toreturn .= "</table>";
            
            if ($j == 0) {
                echo "<center><p class=\"lead\">По данному запросу ничего не найдено</p></center>";
            } else {
                echo "<center><p class=\"lead\">Найдено результатов: ".$j."</p></center><br>".$toreturn;
            }
        } else {
            echo "<center><p class=\"lead\">Введённая строка оказалась пустой, попробуйте ещё раз</p></center>";
        }
    }
    
    function getCsv() {
        global $db;
        
        $type = $_GET['type'];
        $idGame = $_GET['id_game'];
        
        if ($type == 'reg_list') {
            $res = mysql_query("
                SELECT 
                    gd.team_name, 
                    gd.players,
                    gd.comment,
                    gd.is_reserve
                FROM games_data as gd 
                WHERE gd.game_info_id = '".$idGame."' 
                ORDER BY gd.id
            ",$db);
            $list = "№\tНазвание команды\tЧел\tКомментарий\n";
            $list = iconv(mb_detect_encoding($list, mb_detect_order(), true), "UTF-16", $list);
            $i = 1;
            while ($row = mysql_fetch_array($res)){
                $teamName = $row['team_name'];
                $players = $row['players'];
                $comment = $row['comment'];
                $reserve = (bool)($row['is_reserve'] == 0);
                $total = str_replace(".", ",", $row['total']);
                $tmpArray = "".$i."\t\"".$teamName.($reserve ? "" : " (резерв)")."\"\t\"".$players." чел.\"\t\"".$comment."\"\n";
                $tmpArray = iconv(mb_detect_encoding($tmpArray, mb_detect_order(), true), "UTF-16", $tmpArray);
                $list = $list.$tmpArray;
                $i++;
            }
        } else if ($type == 'rank_list'){
            $res = mysql_query("
                SELECT 
                    gd.team_name, 
                    b.name as bar_name, 
                    (SELECT 
                        rd.total 
                    FROM ranks_data as rd 
                    WHERE 
                        rd.team_name = gd.team_name 
                        AND rd.rank_id = (SELECT `id` FROM `ranks` ORDER BY `id` DESC LIMIT 1)
                    ) as total 
                FROM games_data as gd 
                LEFT JOIN bars as b 
                    ON b.id = gd.bar_id 
                WHERE gd.game_info_id = '".$idGame."' 
                ORDER BY b.name, `total` DESC
            ",$db);
            $list = "Бар;Название команды;Баллов рейтинга;\n";
            $list = iconv(mb_detect_encoding($list, mb_detect_order(), true), "Windows-1251", $list);
            while ($row = mysql_fetch_array($res)){
                $teamName = $row['team_name'];
                $nameBar = $row['bar_name'];
                $total = str_replace(".", ",", $row['total']);
                $tmpArray = "\"".$nameBar."\";\"".$teamName."\";\"".$total."\";\n";
                $tmpArray = iconv(mb_detect_encoding($tmpArray, mb_detect_order(), true), "Windows-1251", $tmpArray);
                $list = $list.$tmpArray;
            }
        }
        
        $dir = "csv/";
        $fileName = $dir.$type."_game-".$idGame."_".date('Ymd_His').".csv";
        $fp = fopen($fileName, 'w');
        file_put_contents($fileName, $list.PHP_EOL);
        
        fclose($fp);
        $file_link = "https://vynosmozga.ru/admin/".$fileName;
        
        ?>
        
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                window.open("<?= $file_link ?>", "_blank");
                document.location.href = '?page=registration&id_game=<?= $idGame ?>';
            });
        </script>
        
        <?
    }
    
    function about() {
        ?>
            <div class="wrapper">
                <div class="layer">
                    <h3>
                        <center>Админ-панель <br>"Вынос мозга"</center>
                    </h3>
                    <h6><center>версия 4.0</center></h6>
                    <center><b>Обновлено</b> <?= goodDate("2025-07-24 18:46") ?></center><br>
                    <center><a href="mailto:anegrash@nav-com.ru" target="_blank">Сообщить об ошибке</a></center>
                    <hr>
                    <center>Сделано с любовью ❤️</center>
                    <center><a type="button" class="btn btn-white" href="https://nav-com.ru/" target="_blank">
                        <img src="https://nav-com.ru/img/logo_full.png" width="70vw">
                    </a></center>
                </div>
            </div>
            
            <style>
                html, body, .row {
                    height: 100%;
                }
                .container-fluid {
                    height: 90%;
                    margin-top: 1.5%;
                }
                .wrapper {
                    display: flex;
                    height: 100%;
                    align-items: center;
                    padding-top: 40px;
                    padding-bottom: 40px;
                }
                
                .layer {
                    width: 100%;
                    max-width: 330px;
                    padding: 15px;
                    margin: auto;
                    border: 1px solid gray;
                    border-radius: 5px;
                    background-color: #f5f5f5;
                }
            </style>
        <?
    }
    
    function goodDate($date){
        $exp= explode(' ',$date);
        $dateG=$exp[0];
        $timeG=$exp[1];
        
        $dateG= explode('-',$dateG);
        $dayG="".$dateG[2];
        $monthG="".$dateG[1];
        $yearG="".$dateG[0];
        
        $timeG= explode(':',$timeG);
        $hourG="".$timeG[0];
        $minuteG="".$timeG[1];
        
        $dateThis = mktime(0, 0, 0, $monthG, $dayG, $yearG);
        $tomorrow  = mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
        $afterTomorrow  = mktime(0, 0, 0, date("m"), date("d")+2, date("Y"));
        
        if($yearG == date('Y') and $monthG == date('m') and $dayG == date('d')){
            return "сегодня в ".$hourG.":".$minuteG;
        }
        
        if($dateThis == $tomorrow){
            return "завтра в ".$hourG.":".$minuteG;
        }
        
        if($dateThis == $afterTomorrow){
            return "послезавтра в ".$hourG.":".$minuteG;
        }
        
        switch($monthG){
            case 1: $monthText="января"; break;
            case 2: $monthText="февраля"; break;
            case 3: $monthText="марта"; break;
            case 4: $monthText="апреля"; break;
            case 5: $monthText="мая"; break;
            case 6: $monthText="июня"; break;
            case 7: $monthText="июля"; break;
            case 8: $monthText="августа"; break;
            case 9: $monthText="сентября"; break;
            case 10: $monthText="октября"; break;
            case 11: $monthText="ноября"; break;
            case 12: $monthText="декабря"; break;
            
        }
    
        $weekDay = date("N", mktime(0, 0, 0, $monthG, $dayG, $yearG));
        switch($weekDay){
            case 1: $dayText="понедельник"; break;
            case 2: $dayText="вторник"; break;
            case 3: $dayText="среда"; break;
            case 4: $dayText="четверг"; break;
            case 5: $dayText="пятница"; break;
            case 6: $dayText="суббота"; break;
            case 7: $dayText="воскресенье"; break;
        }
        
        return (int)$dayG." ".$monthText.($yearG != date('Y') ? " ".$yearG." года" : "").", ".$dayText.", ".$hourG.":".$minuteG;
    }
    
    function goodPrice($price){
        $toreturn = "".$price;
        $value = str_split($toreturn);
        $toreturn = "";
        $j = 0;
        for ($i = count($value)-1; $i>=0; $i--){
            $toreturn = $value[$i].($j % 3 == 0 ? "&nbsp;": "").$toreturn;
            $j++;
        }
        return $toreturn;
    }
    
    function datetimeValue($date){
        $exp= explode(' ',$date);
        $dateG=$exp[0];
        $timeG=$exp[1];
        
        $dateG= explode('-',$dateG);
        $dayG="".$dateG[2];
        $monthG="".$dateG[1];
        $yearG="".$dateG[0];
        
        $timeG= explode(':',$timeG);
        $hourG="".$timeG[0];
        $minuteG="".$timeG[1];
        
        return $yearG."-".$monthG."-".$dayG."T".$hourG.":".$minuteG;
    }