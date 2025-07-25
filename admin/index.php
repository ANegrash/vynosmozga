<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <title>Панель администрации | Вынос мозга</title>
        
        <!-- Bootstrap core CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
        <meta name="theme-color" content="#7952b3">
        <link rel="shortcut icon" href="https://vynosmozga.ru/img/logo/logo_xsmall.png" type="image/png">
        <link rel="stylesheet" href="./style/main.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.js"></script>
    </head>
<?
    session_start();
    $emailPost = $_POST['email'];
    $passPost = $_POST['password'];
    
    $email = $_SESSION["email"] ?: $emailPost;
    $pass = $_SESSION["password"] ?: $passPost;
    
    include("../include/connection.php");
    
    if (empty($email) or empty($pass)) {
        $alert = false;
        include("./signin.php");
    } else {
        $result = mysql_query("
            SELECT 
                `id`, 
                `name`,
                `email` 
            FROM `admin` 
            WHERE (`login`='".$email."' OR `email`='".$email."') AND `pass`='".$pass."'
        ", $db); 
        $res = mysql_fetch_array($result);
        $idUser = $res['id'];
        $nameUser = $res['name'];
        $emailUser = $res['email'];
        
        if ($idUser > 0) {
            $_SESSION["email"] = $emailUser;
            $_SESSION["password"] = $pass;
            $page = $_GET['page'];
            if (empty($page))
                $page = "registrations";
                
            $pagesList = [
                "registrations" => ["name" => "Регистрации", "subpages" => ["registrations", "registration", "team", "create_team"]],
                "ranks" => ["name" => "Рейтинг", "subpages" => ["ranks", "rank", "create_rank"]],
                "games" => ["name" => "Игры", "subpages" => ["games", "game", "create_game"]],
                "bars" => ["name" => "Бары", "subpages" => ["bars", "bar", "create_bar"]],
                "sponsors" => ["name" => "Партнёры", "subpages" => ["sponsors", "sponsor", "create_sponsor"]],
                "reviews" => ["name" => "Отзывы", "subpages" => ["reviews", "review", "create_review"]],
                "img" => ["name" => "Изображения", "subpages" => ["img"]],
                "about" => ["name" => "О системе", "subpages" => ["about"]]
            ];
                
            include("./functions/main.php");
            ?>
            <body style="overflow-x: hidden;">
                <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
                    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="https://vynosmozga.ru" target="_blank">Вынос мозга</a>
                    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <form method="GET" action="" style="display: contents">
                        <input type="hidden" name="page" value="search">
                        <input class="form-control form-control-dark" name="query" type="text" placeholder="Поиск команд" aria-label="Search">
                        <button class="btn" type="submit" z-index="256" style="margin-left: -60px;"><i class="fas fa-search"></i></button>
                    </form>
                </header>

                <div class="container-fluid">
                    <div class="row">
                        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                            <div class="position-sticky pt-1">
                                <ul class="nav flex-column" id="ul_nav">
                                    <li class="nav-item nav-link">
                                        <b><?= $nameUser ?></b>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="?page=exit" style="color:red;">
                                            Выйти 
                                        </a>
                                    </li>
                                    <hr>
                                    <?
                                        foreach ($pagesList as $pageHref => $info) {
                                            $isActiveTab = false;
                                            if (in_array($page, $info["subpages"])) {
                                                $isActiveTab = true;
                                                if (is_file("./functions/".$pageHref.".php"))
                                                    include("./functions/".$pageHref.".php");
                                            }
                                    ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= ($isActiveTab) ? "active" : "" ?>" href="?page=<?= $pageHref ?>">
                                                <?= $info["name"] ?>
                                            </a>
                                        </li>
                                    <?
                                        }
                                    ?>
                                </ul>
                            </div>
                        </nav>
                        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" id="main-panel">
                            <?
                                switch ($page) {
                                    //main.php
                                    case "csv_data": 
                                        getCsv(); 
                                        break;
                                    case "search": 
                                        search(); 
                                        break;
                                    case "img": 
                                        manageImg(); 
                                        break;
                                    case "about": 
                                        about(); 
                                        break;
                                    case "exit": 
                                        exitProfile(); 
                                        break;
                                    
                                    //registration.php
                                    case "registrations":
                                        getRegistrationInfo(); 
                                        break;
                                    case "registration":
                                        getInfoByGameId(); 
                                        break;
                                    case "team":
                                        getTeamInfo(); 
                                        break;
                                    case "create_team":
                                        addTeam(); 
                                        break;
                                        
                                    //ranks.php
                                    case "ranks":
                                        getRank(); 
                                        break;
                                    case "rank":
                                        getRankInfo(); 
                                        break;
                                    case "create_rank":
                                        createRank(); 
                                        break;
                                    
                                    //games.php
                                    case "games":
                                        getGames(); 
                                        break;
                                    case "game":
                                        getGameInfo(); 
                                        break;
                                    case "create_game":
                                        createGame(); 
                                        break;
                                    
                                    //bars.php
                                    case "bars":
                                        getBars(); 
                                        break;
                                    case "bar":
                                        redactBar(); 
                                        break;
                                    case "create_bar":
                                        addBar(); 
                                        break;
                                    
                                    //spponsors.php
                                    case "sponsors":
                                        sponsors(); 
                                        break;
                                    case "sponsor":
                                        redactSponsor(); 
                                        break;
                                    case "create_sponsor":
                                        createSponsor(); 
                                        break;
                                    
                                    //reviews.php
                                    case "reviews":
                                        reviews(); 
                                        break;
                                    case "review":
                                        redactReview(); 
                                        break;
                                    case "create_review":
                                        createReview(); 
                                        break;
                                }
                            ?>
                        </main>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
            </body>
            <?
        } else {
            $alert = true;
            
            include("./signin.php");
        }
    }
?>
</html>