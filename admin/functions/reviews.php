<?php
    
    function createReview() {
        ?>
            <a href="?page=reviews">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a>  
            <script>
                function saveReview(){
                    var textOtzyv = $('textarea[name=textOtzyv]').val();
                    var author = $('input[name=author]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'addReview', textOtzyv: textOtzyv, author: author},
                    	success: function(data){
                            if (data == "ok")
                                document.location.href = '?page=reviews'
                            else
                                alert(data)
                    	}
                    });
                }
            </script>
            <h5 style="margin-top:-27px;"><center>Создание отзыва</center></h5>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Текст:</b>
                    <textarea type="text" name="textOtzyv" class="form-control is-valid" rows="3" placeholder="Текст отзыва"></textarea>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Автор:</b>
                    <input type="text" name="author" class="form-control is-valid" placeholder="Автор отзыва (только имя)">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button class="btn btn-success" onclick="saveReview()">Сохранить</button>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    function redactReview() {
        global $db;
        
        $idOtzyv = $_GET['id_review'];
        
        $result20 = mysql_query("
            SELECT 
                `text`, 
                `author` 
            FROM `reviews` 
            WHERE `id`='".$idOtzyv."'
        ",$db);
        $row6 = mysql_fetch_array($result20);
        $textOtzyv=$row6['text'];
        $authorOtzyv=$row6['author'];
        ?>
            <a href="?page=reviews">
                <i class="fas fa-arrow-circle-left fa-2x" style="color:#6c757d;"></i>
            </a> 
            <script>
                function saveReview(){
                    var textOtzyv = $('textarea[name=textOtzyv]').val();
                    var author = $('input[name=author]').val();
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'redactReview', textOtzyv: textOtzyv, author: author, idOtzyv: <? echo $idOtzyv; ?>},
                    	success: function(data){
                            if (data == "ok")
                                document.location.href = "?page=reviews"
                            else
                                alert(data)
                    	}
                    });
                }
            </script>
            <h5 style="margin-top:-27px;"><center>Редактирование отзыва</center></h5>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Текст:</b>
                    <textarea type="text" name="textOtzyv" class="form-control is-valid" rows="3" placeholder="Текст отзыва"><?= $textOtzyv ?></textarea>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <b>Автор:</b>
                    <input type="text" name="author" class="form-control is-valid" placeholder="Автор отзыва (только имя)" value="<?= $authorOtzyv ?>">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <button class="btn btn-success" onclick="saveReview()">Сохранить</button>
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        <?
    }
    
    function reviews() {
        global $db;
        ?>
            <script>
                function setReviewState(id, state){
                    $.ajax({
                    	url: 'ajax.php',
                    	method: 'post',
                    	dataType: 'text',
                    	data: {activity: 'setReviewState', idReview: id, state: state},
                    	success: function(data){
                            if (data == "ok")
                                document.location.href = '?page=reviews'
                            else
                                alert(data)
                    	}
                    });
                }
            </script>
            
            <center><h5>Отзывы</h5></center>
            <center><p>После создания отзыв неактивен и не отображается на странице</p></center>
            <hr>
            <a class="btn btn-success" href="?page=create_review">Создать</a>
            <h6><center>Отображаемые на сайте отзывы</center></h6>
            <div class="row d-flex justify-content-center">
                <?
                    $result20 = mysql_query("
                        SELECT * 
                        FROM `reviews` 
                        WHERE `is_active`='1'
                    ",$db);
                    while ($row6=mysql_fetch_array($result20)) {
                        $idOtzyv=$row6['id'];
                        $textOtzyv=$row6['text'];
                        $authorOtzyv=$row6['author'];
                        $activeOtzyv=$row6['is_active'];
                        ?>
                            <div class="card" style="max-width: 16rem;margin:5px;">
                                <div class="card-body">
                                    <p class="card-text">
                                        <?= $textOtzyv ?><br>
                                        <b>— <?= $authorOtzyv ?></b>
                                    </p>
                                </div>
                                <div class="card-footer" style="background:white;">
                                    <center>
                                      <a href="?page=review&id_review=<?= $idOtzyv ?>" class="btn btn-primary">Изменить</a>
                                      <button class="btn btn-success" onclick="setReviewState('<?= $idOtzyv ?>', 0)">Скрыть</button>
                                    </center>
                                </div>
                            </div>
                        <?
                    }
                ?>
            </div>
            <br>
            <h6><center>Не отображаемые на сайте отзывы</center></h6>
            <div class="row d-flex justify-content-center">
                <?
                    $result20 = mysql_query("
                        SELECT * 
                        FROM `reviews` 
                        WHERE `is_active`='0'
                    ",$db);
                    while ($row6=mysql_fetch_array($result20)) {
                        $idOtzyv=$row6['id'];
                        $textOtzyv=$row6['text'];
                        $authorOtzyv=$row6['author'];
                        $activeOtzyv=$row6['is_active'];
                        ?>
                            <div class="card" style="max-width: 16rem;margin:5px;">
                                <div class="card-body">
                                    <p class="card-text">
                                        <?= $textOtzyv ?><br>
                                        <b>— <?= $authorOtzyv ?></b>
                                    </p>
                                </div>
                                <div class="card-footer" style="background:white;">
                                    <center>
                                      <a href="?page=review&id_review=<?= $idOtzyv ?>" class="btn btn-primary">Изменить</a>
                                      <button class="btn btn-success" onclick="setReviewState('<?= $idOtzyv ?>', 1)">Показать</button>
                                    </center>
                                </div>
                            </div>
                        <?
                    }
                ?>
            </div>
            <br>
        <?
    }