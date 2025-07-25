<?
    
    include("../include/connection.php");

    $activity = $_POST['activity'];
    
    switch ($activity) {
        case 'addTeam':         addTeam();          break;
        case 'saveTeam':        saveTeam();         break;
        case 'deleteTeam':      deleteTeam();       break;
        case 'redactRank':      redactRank();       break;
        case 'deleteRankTeam':  deleteRankTeam();   break;
        case 'addNewRank':      addNewRank();       break;
        case 'updateGame':      updateGame();       break;
        case 'addNewGame':      addNewGame();       break;
        case 'updateBar':       updateBar();        break;
        case 'addBar':          addBar();           break;
        case 'barStatus':       barStatus();        break;
        case 'addSponsor':      addSponsor();       break;
        case 'updateSponsor':   updateSponsor();    break;
        case 'deleteSponsor':   deleteSponsor();    break;
        case 'setSponsorState': setSponsorState();  break;
        case 'redactImg':       redactImg();        break;
        case 'deleteImg':       deleteImg();        break;
        case 'addReview':       addReview();        break;
        case 'redactReview':    redactReview();     break;
        case 'setReviewState':  setReviewState();   break;
    }
    
    function addTeam() {
        global $db;
        if (isset($_POST['bar']) && isset($_POST['team'])){
            if(mysql_query("
                INSERT INTO `games_data`(`game_info_id`, `bar_id`, `players`, `team_name`, `captain_name`, `phone`, `email`, `social`, `comment`, `is_reserve`)
                VALUES ('".$_POST['gameId']."','".$_POST['bar']."','".$_POST['players']."','".mysql_real_escape_string($_POST['team'])."','".$_POST['cap']."','".$_POST['phone']."','".$_POST['emailTeam']."','".$_POST['social']."','".$_POST['comment']."','".$_POST['reserve']."')"
            ,$db)){
                echo mysql_insert_id();
            }else{
                echo "Произошла ошибка. Попробуйте позже";
            }
        }
    }
    
    function saveTeam() {
        global $db;
        
        $idTeam = $_POST['teamId'];
        $teamName = mysql_real_escape_string($_POST['nameTeam']);
        $barId = $_POST['barId'];
        $players = $_POST['players'];
        $capName = mysql_real_escape_string($_POST['captain']);
        $phone = $_POST['phone'];
        $email = $_POST['emailCap'];
        $social = mysql_real_escape_string($_POST['social']);
        $comment = mysql_real_escape_string($_POST['comment']);
        $reserve = $_POST['reserve'];
        
        if (mysql_query("
            UPDATE `games_data` 
            SET 
                `bar_id`='".$barId."',
                `players`='".$players."',
                `team_name`='".$teamName."',
                `captain_name`='".$capName."',
                `phone`='".$phone."',
                `email`='".$email."',
                `social`='".$social."',
                `comment`='".$comment."',
                `is_reserve`='".$reserve."' 
            WHERE `id`='".$idTeam."
            LIMIT 1'
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function deleteTeam() {
        global $db;
        
        $idTeam = $_POST['teamId'];
        
        if (mysql_query("
            DELETE FROM `games_data` 
            WHERE `id`='".$idTeam."'
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function redactRank() {
        global $db;
        
        $idRank = $_POST['rankId'];
        
        if (isset($_POST['scoreGames']) and isset($_POST['total']) and isset($_POST['newName']) and isset($_POST['idTeam'])){
            if ($_POST['idTeam'] == 'new'){
                if(mysql_query("
                    INSERT INTO `ranks_data` (`rank_id`, `team_name`, `score_games`, `total`) 
                    VALUES ('".$idRank."','".mysql_real_escape_string($_POST['newName'])."','".$_POST['scoreGames']."','".$_POST['total']."')"
                ,$db)){
                    echo "ok";
                }else{
                    echo "Произошла ошибка. Попробуйте позже";
                }
            } else {
                $var_string = $_POST['total_sort_sum'];
                $operations_array = explode(";", $var_string);
                $res_sort = (float)0;
                for ($t = 0; $t < count($operations_array); $t++) {
                    $res_sort += (float)explode('^', $operations_array[$t])[0]*(float)explode('^', $operations_array[$t])[1];
                }
                
                if (mysql_query("
                    UPDATE `ranks_data` 
                    SET `team_name`='".mysql_real_escape_string($_POST['newName'])."',
                        `score_games`='".$_POST['scoreGames']."',
                        `total`='".$_POST['total']."',
                        `total_sort_sum`='".(float)$res_sort."'
                    WHERE `id`=".$_POST['idTeam']
                ,$db)){
                    echo "ok";
                }else{
                    echo "Произошла ошибка. Попробуйте позже";
                }
            }
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function deleteRankTeam() {
        global $db;
        
        $idTeam = $_POST['idTeam'];
        
        if (mysql_query("
            DELETE FROM `ranks_data` 
            WHERE `id`='".$idTeam."' 
            LIMIT 1
        ",$db)) {
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function addNewRank() {
        global $db;
        
        $seasonName = $_POST['seasonName'];
        $seasonTime = $_POST['seasonTime'];
        $firstGame = $_POST['firstGame'];
        $lastGame = $_POST['lastGame'];
        $is_season_first = $_POST['is_season_first'];
        
        if (mysql_query("
            INSERT INTO `ranks`(`season_name`, `season_time`, `first_game`, `last_game`, `is_season_word_first`) 
            VALUES ('".$seasonName."','".$seasonTime."','".$firstGame."','".$lastGame."', '".$is_season_first."')
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function updateGame() {
        global $db;
        
        $bars = $_POST['bars'];
        $gameType = $_POST['gameType'];
        $gameNumber = $_POST['gameNumber'];
        $cost = $_POST['cost'];
        $minPeople = $_POST['minPeople'];
        $maxPeople = $_POST['maxPeople'];
        $openDate = $_POST['openDate'];
        $gameDate = $_POST['gameDate'];
        $gameId = $_POST['gameId'];
        
        if (mysql_query("
            UPDATE `games_info` 
            SET `number`='".$gameNumber."',
                `date_open`='".str_replace("T", " ", $openDate)."',
                `date_game`='".str_replace("T", " ", $gameDate)."',
                `game_type`='".$gameType."',
                `bar_ids`='".$bars."',
                `cost`='".$cost."',
                `min_people`='".$minPeople."',
                `max_people`='".$maxPeople."' 
            WHERE `id`='".$gameId."' 
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function addNewGame() {
        global $db;
        
        $bars = $_POST['bars'];
        $gameType = $_POST['gameType'];
        $gameNumber = $_POST['gameNumber'];
        $cost = $_POST['cost'];
        $minPeople = $_POST['minPeople'];
        $maxPeople = $_POST['maxPeople'];
        $openDate = $_POST['openDate'];
        $gameDate = $_POST['gameDate'];
        
        if (mysql_query("
            INSERT INTO `games_info`
            (`number`, `date_open`, `date_game`, `game_type`, `bar_ids`, `cost`, `min_people`, `max_people`) 
            VALUES 
            ('".$gameNumber."','".str_replace("T", " ", $openDate)."','".str_replace("T", " ", $gameDate)."','".$gameType."','".$bars."','".$cost."','".$minPeople."','".$maxPeople."')
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function updateBar() {
        global $db;
        
        $idBar = $_POST['idBar'];
        $newName = $_POST['barName'];
        $newLoc = $_POST['location'];
        $newMax = $_POST['maxTeams'];
        $newHref = $_POST['barHref'];
        $newImg = $_POST['barImg'];
        if (mysql_query("
            UPDATE `bars` 
            SET 
                `name`='".mysql_real_escape_string($newName)."', 
                `location`='".$newLoc."', 
                `max_teams`='".$newMax."', 
                `href`='".$newHref."', 
                `icon`='".$newImg."'
            WHERE `id`='".$idBar."'
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function addBar() {
        global $db;
        
        $newName = $_POST['barName'];
        $newLoc = $_POST['location'];
        $newMax = $_POST['maxTeams'];
        $newHref = $_POST['barHref'];
        $newImg = $_POST['barImg'];
        if (mysql_query("
            INSERT INTO `bars`(`name`, `location`, `max_teams`, `href`, `icon`, `is_active`) 
            VALUES ('".mysql_real_escape_string($newName)."','".$newLoc."','".$newMax."','".$newHref."','".$newImg."','1')
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function barStatus() {
        global $db;
        
        $idBar = $_POST['idBar'];
        $status = $_POST['status'];
        if (mysql_query("
            UPDATE `bars` SET `is_active`='".$status."' WHERE `id`='".$idBar."' LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function addSponsor() {
        global $db;
        
        $name_sponsor = $_POST['name'];
        $link_sponsor = $_POST['link'];
        $img_sponsor = $_POST['img'];
        if (mysql_query("
            INSERT INTO `sponsors` (`link`, `img`, `name`, `is_active`) 
            VALUES ('".$link_sponsor."','".$img_sponsor."','".$name_sponsor."',0)
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function updateSponsor() {
        global $db;
        
        $id_sponsor = $_POST['id'];
        $name_sponsor = $_POST['name'];
        $link_sponsor = $_POST['link'];
        $img_sponsor = $_POST['img'];
        if (mysql_query("
            UPDATE `sponsors` 
            SET 
                `link`='".$link_sponsor."', 
                `img`='".$img_sponsor."', 
                `name`='".$name_sponsor."' 
            WHERE `id`='".$id_sponsor."'
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function deleteSponsor() {
        global $db;
        
        $id_sponsor = $_POST['id'];
        if (mysql_query("
            DELETE FROM `sponsors` 
            WHERE `id`='".$id_sponsor."'
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function setSponsorState() {
        global $db;
        
        $id_sponsor = $_POST['id'];
        $is_active = $_POST['is_active'];
        if (mysql_query("
            UPDATE `sponsors` 
            SET `is_active`='".$is_active."' 
            WHERE `id`='".$id_sponsor."'
            LIMIT 1
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function redactImg() {
        $from = $_POST['nameFrom'];
        $to = $_POST['nameTo'];
        if (rename($from, $to)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function deleteImg() {
        $from = $_POST['nameFrom'];
        if (unlink($from)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function addReview() {
        global $db;
        $textOtzyv = $_POST['textOtzyv'];
        $author = $_POST['author'];
        if (mysql_query("
            INSERT INTO `reviews` (`text`, `author`, `is_active`) VALUES ('".$textOtzyv."','".$author."','0')
        ",$db)){
            echo "ok";
        }else{
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function redactReview() {
        global $db;
        
        $idOtzyv = $_POST['idOtzyv'];
        $textOtzyv = $_POST['textOtzyv'];
        $author = $_POST['author'];
        if (mysql_query("
            UPDATE `reviews` SET `text`='".$textOtzyv."', `author`='".$author."' WHERE `id`='".$idOtzyv."'
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }
    
    function setReviewState() {
        global $db;
        
        $idReview = $_POST['idReview'];
        $status = $_POST['state'];
        if (mysql_query("
            UPDATE `reviews` SET `is_active`='".$status."' WHERE `id`='".$idReview."'
        ",$db)){
            echo "ok";
        } else {
            echo "Произошла ошибка. Попробуйте позже";
        }
    }