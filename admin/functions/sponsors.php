<?php

    function createSponsor() {
        ?>
            <a href="?page=sponsors">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>
            <script>
                function saveSponsor(){
                    var name = $('input[name=name]').val();
                    var link = $('input[name=link]').val();
                    var img = $('input[name=img]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addSponsor', name: name, link: link, img: img},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=sponsors';
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
            
            <center><h5>Добавление партнёра</h5></center>
            <hr>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Название:</b><br>
                    <small>Будет всплывать в качестве подсказки при наведении</small>
                    <input type="text" name="name" class="form-control is-valid" placeholder="Например: Магазин настольных игр SevGames" value="">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Ссылка:</b><br>
                    <small>Адрес, куда будет вести нажатие на баннер</small>
                    <input type="text" name="link" class="form-control is-valid" placeholder="Например: https://vynosmozga.ru" value="">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Выбор изображения:</b><br>
                    <a class="btn btn-primary" href="?page=img&dir=sponsors&ret=create_sponsor">Добавить новое</a>
                    <input type="hidden" name="img" id="img_name" value="">
                    <br>
                    <div class="row d-flex justify-content-center">
                    <?
                        $directory = '../img/sponsors';
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
            <br>
            
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
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button class="btn btn-success" onclick="saveSponsor()">Создать</button>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    function redactSponsor() {
        global $db;
        
        $id_sponsor = $_GET['id_sponsor'];
        
        $res = mysql_query("
            SELECT 
                link,
                name, 
                img
            FROM sponsors
            WHERE id = '".$id_sponsor."'
        ",$db);
        $row = mysql_fetch_array($res);
        $link = $row['link'];
        $name = $row['name'];
        $img = $row['img'];
        ?>
            <a href="?page=sponsors">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a> 
            <script>
                function saveSponsor(){
                    var name = $('input[name=name]').val();
                    var link = $('input[name=link]').val();
                    var img = $('input[name=img]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'updateSponsor', id: <?= $id_sponsor ?>, name: name, link: link, img: img},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=sponsors';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                }
                function deleteSponsor(){
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'deleteSponsor', id: <?= $id_sponsor ?>},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=sponsors';
                            } else {
                                alert(data);
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
            
            <center><h5>Изменение данных партнёра</h5></center>
            <hr>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Название:</b><br>
                    <small>Будет всплывать в качестве подсказки при наведении</small>
                    <input type="text" name="name" class="form-control is-valid" placeholder="Например: Магазин настольных игр SevGames" value="<?= $name ?>">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Ссылка:</b><br>
                    <small>Адрес, куда будет вести нажатие на баннер</small>
                    <input type="text" name="link" class="form-control is-valid" placeholder="Например: https://vynosmozga.ru" value="<?= $link ?>">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Выбор изображения:</b><br>
                    <a href="?page=img&dir=sponsors&ret=create_sponsor" class="btn btn-primary">Добавить новое</a>
                    <input type="hidden" name="img" id="img_name" value="<?= $img ?>">
                    <br>
                    <div class="row d-flex justify-content-center">
                    <?
                        $directory = '../img/sponsors';
                        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                        $counter = 0;
                        foreach ($scanned_directory as $file_name) {
                            ?>
                                <img src="<?= $directory."/".$file_name ?>" class="select_img <?= ($img == $file_name ? "selected" : "") ?>" id="img_<?= ++$counter ?>" onclick="selectImg('<?= $file_name ?>', 'img_<?= $counter ?>')">
                            <?
                        }
                    ?>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            
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
            
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button class="btn btn-success" onclick="saveSponsor()">Сохранить</button>
                    <button class="btn btn-danger" onclick="deleteSponsor()">Удалить партнёра</a>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    function sponsors() {
        global $db;
        ?>
            <script>
                function setIsActiveSponsor(id, state){
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'setSponsorState', id: id, is_active: state},
                    	success: function(data){
                            if (data == "ok") {
                                document.location.href = '?page=sponsors';
                            } else {
                                alert(data);
                            }
                    	}
                    });
                }
            </script>
            
            <center><h5>Наши партнёры</h5></center>
            <hr>
            <a class="btn btn-success" href="?page=create_sponsor">Добавить партнёра</a>
            <a class="btn btn-primary" href="?page=img&dir=sponsors&ret=sponsors">Изображения партнёров</a>
            <h6><center>Отображаемые партнёры</center></h6>
            <div class="row d-flex justify-content-center">
                <?
                    $result20 = mysql_query("
                        SELECT * 
                        FROM `sponsors` 
                        WHERE `is_active`=1
                    ",$db);
                    while ($row6=mysql_fetch_array($result20)) {
                        $id_sponsor=$row6['id'];
                        $link=$row6['link'];
                        $img=$row6['img'];
                        $name_sponsor=$row6['name'];
                        ?>
                            <div class="card" style="max-width: 16rem;margin:5px;">
                                <img src="../img/sponsors/<?= $img ?>" class="card-img-top" alt="Картинка не грузится...">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $name_sponsor ?></h5>
                                    <p class="card-text">
                                        Ссылка: <a href="<?= $link ?>" target="_blank"><?= $link ?></a><br>
                                    </p>
                                </div>
                                <div class="card-footer" style="background:white;">
                                    <center>
                                      <a href="?page=sponsor&id_sponsor=<?= $id_sponsor ?>" class="btn btn-primary">Изменить</a>
                                      <button class="btn btn-danger" onclick="setIsActiveSponsor(<?= $id_sponsor ?>, 0)">Скрыть</button>
                                    </center>
                                </div>
                            </div>
                        <?
                    }
                ?>
            </div>
            <hr>
            <h6><center>Не отображаемые партнёры</center></h6>
            <div class="row d-flex justify-content-center">
                <?
                    $result20 = mysql_query("
                        SELECT * 
                        FROM `sponsors` 
                        WHERE `is_active`=0
                    ",$db);
                    while ($row6=mysql_fetch_array($result20)){
                        $id_sponsor=$row6['id'];
                        $link=$row6['link'];
                        $img=$row6['img'];
                        $name_sponsor=$row6['name'];
                        ?>
                            <div class="card" style="max-width: 16rem;margin:5px;">
                                <img src="../img/sponsors/<?= $img ?>" class="card-img-top" alt="Картинка не грузится...">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $name_sponsor ?></h5>
                                    <p class="card-text">
                                        Ссылка: <a href="<?= $link ?>" target="_blank"><?= $link ?></a><br>
                                    </p>
                                </div>
                                <div class="card-footer" style="background:white;">
                                    <center>
                                      <a href="?page=sponsor&id_sponsor=<?= $id_sponsor ?>" class="btn btn-primary">Изменить</a>
                                      <button class="btn btn-success" onclick="setIsActiveSponsor(<?= $id_sponsor ?>, 1)">Показать</button>
                                    </center>
                                </div>
                            </div>
                        <?
                    }
                ?>
            </div>
        <?
    }
    