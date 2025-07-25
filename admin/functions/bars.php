<?php
    
    function addBar() {
        ?>
            <a href="?page=bars">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveBarInfo(){
                    var barName = $('input[name=barName]').val();
                    var maxTeams = $('input[name=maxTeams]').val();
                    var location = $('input[name=location]').val();
                    var barHref = $('input[name=barHref]').val();
                    var barImg = $('input[name=img]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addBar', barName: barName, maxTeams: maxTeams, location: location, barHref: barHref, barImg: barImg},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=bars';
                            } else {
                                alert(data)
                            }
                    	}
                    });
                }
                function selectImg(filename, idElem){
                    $('#img_name').val(filename);
                    $('.select_img.selected').each(function(){
                        $(this).removeClass('selected');
                    });
                    $('#'+idElem).addClass('selected');
                }
            </script>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Название бара:</b>
                    <input type="text" name="barName" class="form-control is-valid" value="" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Максимальное количество команд:</b>
                    <input type="number" name="maxTeams" class="form-control is-valid" min="1" step="1" value="" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Местоположение текстом:</b>
                    <input type="text" name="location" class="form-control is-valid" value="" placeholder="Пример: пл. Нахимова, 1" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Ссылка на Яндекс.Карты:</b> <small>zoom выставить на 15</small>
                    <input type="text" name="barHref" class="form-control is-valid" value="">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Выбор изображения:</b><br>
                    <a href="?page=img&dir=bars&ret=create_bar" class="btn btn-primary">Добавить новое</a>
                    <input type="hidden" name="img" id="img_name" value="">
                    <br>
                    <div class="row d-flex justify-content-center">
                    <?
                        $directory = '../img/bars';
                        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                        $counter = 0;
                        foreach ($scanned_directory as $file_name) {
                            ?>
                                <img src="<?= $directory."/".$file_name ?>" class="select_img" id="img_<?= ++$counter ?>" onclick="selectImg('<?= $file_name ?>', 'img_<?= $counter ?>')">
                            <?
                        }
                    ?>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
            <style>
                .select_img {
                    border: 10px solid grey;
                    width: 200px;
                    height: 150px;
                    margin: 5px 10px;
                }
                .selected {
                    border: 10px solid blue;
                }
            </style>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button class="btn btn-success" onclick="saveBarInfo()">Создать</button>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    function redactBar() {
        global $db;
        
        $idBar = $_GET['id_bar'];
        
        $res = mysql_query("
            SELECT 
                b.name, 
                b.location, 
                b.max_teams, 
                b.icon, 
                b.href
            FROM bars as b
            WHERE b.id=".$idBar."
        ",$db);
        
        $firstTime = true;
        $row = mysql_fetch_array($res);
        $nameBar = $row['name'];
        $location = $row['location'];
        $maxTeams = $row['max_teams'];
        $barImg = $row['icon'];
        $href = $row['href'];
        
        ?>
            <a href="?page=bars">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveBarInfo(){
                    var barName = $('input[name=barName]').val();
                    var maxTeams = $('input[name=maxTeams]').val();
                    var location = $('input[name=location]').val();
                    var barHref = $('input[name=barHref]').val();
                    var barImg = $('input[name=img]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'updateBar', idBar: <?= $idBar ?>, barName: barName, maxTeams: maxTeams, location: location, barHref: barHref, barImg: barImg},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=bars';
                            } else {
                                alert(data)
                            }
                    	}
                    });
                }
                function selectImg(filename, idElem){
                    $('#img_name').val(filename);
                    $('.select_img.selected').each(function(){
                        $(this).removeClass('selected');
                    });
                    $('#'+idElem).addClass('selected');
                }
            </script>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Название бара:</b>
                    <input type="text" name="barName" class="form-control is-valid" value="<?= $nameBar ?>" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Максимальное количество команд:</b>
                    <input type="number" name="maxTeams" class="form-control is-valid" min="1" step="1" value="<?= $maxTeams ?>" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Местоположение текстом:</b>
                    <input type="text" name="location" class="form-control is-valid" value="<?= $location ?>" placeholder="Пример: пл. Нахимова, 1" required>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Ссылка на Яндекс.Карты:</b> <small>zoom выставить на 15</small>
                    <input type="text" name="barHref" class="form-control is-valid" value="<?= $href ?>">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Выбор изображения:</b><br>
                    <a href="?page=img&dir=bars&ret=bars" class="btn btn-primary">Добавить новое</a>
                    <input type="hidden" name="img" id="img_name" value="<?= $img ?>">
                    <br>
                    <div class="row d-flex justify-content-center">
                    <?
                        $directory = '../img/bars';
                        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                        $counter = 0;
                        foreach ($scanned_directory as $file_name) {
                            ?>
                                <img src="<?= $directory."/".$file_name ?>" class="select_img <?= ($barImg == $file_name ? 'selected' : '') ?>" id="img_<?= ++$counter ?>" onclick="selectImg('<?= $file_name ?>', 'img_<?= $counter ?>')">
                            <?
                        }
                    ?>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
            <style>
                .select_img {
                    border: 10px solid grey;
                    width: 200px;
                    height: 150px;
                    margin: 5px 10px;
                }
                .selected {
                    border: 10px solid blue;
                }
            </style>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button type="button" class="btn btn-success" onclick="saveBarInfo()">Сохранить</button>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    
    function getBars() {
        global $db;
        ?>
            <script>
                function changeArchiveStatus(id, status) {
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'barStatus', idBar: id, status: status},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=bars';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                }
            </script>
            <h5><center>Игровые бары</center></h5><hr>
            <a href="?page=create_bar" class="btn btn-success">Добавить бар</a>
            <div class="row d-flex justify-content-center">
        <?
        
        $res = mysql_query("
            SELECT 
                b.id, 
                b.name, 
                b.location, 
                b.max_teams, 
                b.icon, 
                (SELECT l.link FROM links as l WHERE l.category='system' AND l.name='bar_img') as link, 
                b.href
            FROM bars as b
            WHERE b.is_active=1
        ",$db);
        
        while ($row = mysql_fetch_array($res)) {
            $idBar = $row['id'];
            $nameBar = $row['name'];
            $location = $row['location'];
            $maxTeams = $row['max_teams'];
            $barImgLink = $row['link'].$row['icon'];
            $href = $row['href'];
            
            $firstTime = false;
            ?>
                <div class="card" style="max-width: 16rem;margin:5px;">
                    <center>
                        <img src="<?= $barImgLink ?>" class="card-img-top" style="height:100px;width:100px;" alt="<?= $nameBar ?>">
                    </center>
                    <div class="card-body">
                        <h5 class="card-title"><center><?= $nameBar ?></center></h5>
                        <p class="card-text">
                            <b>Максимум команд:</b> <?= $maxTeams ?><br>
                            <b>Адрес:</b> <a href="<?= $href ?>" target="_blank"><?= $location ?></a><br>
                        </p>
                    </div>
                    <div class="card-footer" style="background:white;">
                        <center>
                          <a href="?page=bar&id_bar=<?= $idBar ?>" class="btn btn-primary">Изменить</a>
                          <button class="btn btn-danger" onclick="changeArchiveStatus(<?= $idBar ?>, 0)">В архив</button>
                        </center>
                    </div>
                </div>
            <?
        }
        ?>
            </div>
            <br>
            
            <h5><center>Архивные бары</center></h5><hr>
            <div class="row d-flex justify-content-center">
        <?
        
        $res = mysql_query("
            SELECT 
                b.id, 
                b.name, 
                b.location, 
                b.max_teams, 
                b.icon, 
                (SELECT l.link FROM links as l WHERE l.category='system' AND l.name='bar_img') as link, 
                b.href
            FROM bars as b
            WHERE b.is_active=0
        ",$db);
        
        while ($row = mysql_fetch_array($res)){
            $idBar = $row['id'];
            $nameBar = $row['name'];
            $location = $row['location'];
            $maxTeams = $row['max_teams'];
            $barImgLink = $row['link'].$row['icon'];
            $href = $row['href'];
            ?>
                <div class="card" style="max-width: 16rem;margin:5px;">
                    <center>
                        <img src="<?= $barImgLink ?>" class="card-img-top" style="height:100px;width:100px;" alt="<?= $nameBar ?>">
                    </center>
                  
                    <div class="card-body">
                        <h5 class="card-title"><center><?= $nameBar ?></center></h5>
                        <p class="card-text">
                            <b>Максимум команд:</b> <?= $maxTeams ?><br>
                            <b>Адрес:</b> <?= $location ?>
                        </p>
                    </div>
                    <div class="card-footer" style="background:white;">
                        <center>
                          <button class="btn btn-success" onclick="changeArchiveStatus(<?= $idBar ?>, 1)">Деархивировать</button>
                        </center>
                    </div>
                </div>
            <?
        }
        ?>
            </div>
        <?
    }