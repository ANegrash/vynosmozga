<body class="text-center">
    <link rel="stylesheet" href="./style/signin.css">
    <main class="form-signin">
        <?
            if ($alert) {
                ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <center>Неверный email или пароль</center>
                    </div>
                <?
            }
        ?>
        
        <form method="POST" action="">
            <img class="mb-4" src="https://vynosmozga.ru/img/logo/logo_xsmall.png" alt="" width="62" height="62">
            <h1 class="h3 mb-3 fw-normal">Вход</h1>
            
            <div class="form-floating">
                <input type="text" class="form-control" name="email" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Логин</label>
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Пароль">
                <label for="floatingPassword">Пароль</label>
            </div>
            
            <button class="w-100 btn btn-lg btn-primary" type="submit">Войти</button>
            
            <p class="mt-5 mb-3 text-muted">&copy; 2017-<?= date('Y') ?></p>
        </form>
    </main>
</body>