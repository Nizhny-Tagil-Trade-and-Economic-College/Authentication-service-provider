<?php
    $identity;
    $redirect;
    if (!empty($_GET['redirect']) && !empty($_GET['identity'])) {
        $identity = is_numeric($_GET['identity']) ? intval($_GET['identity']) : strval($_GET['identity']);
        $redirect = strval($_GET['redirect']);
    } else {
        print('<h3>Некорректный запрос!</h3>');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=no">
    <title>ГАПОУ СО НТТЭК | Войти</title>
    <meta name="twitter:description" content="Колледж с многолетней историей побед и ярчайших выпускников!">
    <meta name="twitter:card" content="summary">
    <meta name="description" content="Колледж с многолетней историей побед и ярчайших выпускников!">
    <meta property="og:type" content="website">
    <meta name="twitter:title" content="ГАПОУ СО НТТЭК">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/logo/32px.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/logo/32px.png">
    <link rel="icon" type="image/png" sizes="500x499" href="assets/img/logo/500px.png">
    <link rel="icon" type="image/png" sizes="500x499" href="assets/img/logo/500px.png">
    <link rel="icon" type="image/png" sizes="800x799" href="assets/img/logo/800px.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&amp;subset=cyrillic&amp;display=swap">
    <style>
        body {
            font-family: 'Montserrat';
        }

        #nttek-auth-skew {
            background: #6F78CC;
            height: 100px;
            position: relative;
        }

        #nttek-auth-logo {
            margin-top: 40px;
            border-radius: 100px;
            background: url("assets/img/logo/800px.png"), #fff;
            background-position: 50% 50%;
            background-repeat: no-repeat;
            background-size: 85%;
            width: 150px;
            height: 150px;
            z-index: 2;
            margin-left: 40px;
        }
        #nttek-alerts-area {
            position: fixed;
            top: 85px;
            right: 25px;
            min-width: 25vw;
            max-width: 50vw;
            max-height: calc(100vh - 50px);
            z-index: 1000000;
            overflow: hidden; 
        }
    </style>
</head>

<body>
    <div id="nttek-alerts-area"></div>
    <div class="container d-flex justify-content-center align-items-center" style="min-width: 100vw;min-height: 100vh;">
        <div class="card border-light shadow-lg" style="overflow: hidden;border-radius: 15px;overflow: hidden;max-width: 800px;">
            <div class="card-body d-flex flex-column" style="padding: 0;">
                <div id="nttek-auth-skew">
                    <div id="nttek-auth-logo"></div>
                </div>
                <div class="d-flex justify-content-center align-items-center" style="z-index: 1;">
                    <form class="needs-validation" style="margin: 50px;padding: 50px;" id="nttek-form-login">
                        <div class="row">
                            <div class="col" style="padding: 20px;">
                                <input class="form-control" type="email" name="email" placeholder="Адрес почты" autofocus="" required>
                                <div class="invalid-feedback">Введите свою почту!</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col" style="padding: 20px;">
                                <input class="form-control" type="password" placeholder="Пароль" name="password" required>
                                <div class="invalid-feedback">Введите пароль!</div>
                            </div>
                            <?php print("<input type='hidden' name='" . (is_int($identity) ? 'is_admin' : 'service_token') . "' value='{$identity}'>"); ?>
                        </div>
                        <div class="row">
                            <div class="col d-flex flex-grow-1 flex-shrink-1 flex-fill justify-content-center" style="padding: 20px;"><button id="nttek-login" class="btn btn-primary" type="button" style="width: 100%;filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));padding: 12px;background: linear-gradient(96.17deg, #6F78CC 4.88%, #3240BC 217.6%);boder:none;">Далее</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>

</html>