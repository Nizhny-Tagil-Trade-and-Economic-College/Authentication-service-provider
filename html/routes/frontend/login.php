<?php
    require __DIR__ . '/../../controllers/autoload.php';
    require __DIR__ . '/../../assets/php/configuration.php';

    session_start([
        'cookie_httponly' => $CNF['cookies']['httponly'],
        'cookie_secure' => $CNF['cookies']['secure'],
        'cookie_lifetime' => $CNF['cookies']['lifetime']
    ]);
    $_SESSION['is_ok'] = !empty($_SESSION['uuid']);

    $database;
    $tokens;
    $system_is_ready = false;
    try {
      $database = new connect();
      $tokens = new tokens();
      $system_is_ready = true;
    } catch (ErrorException $e) {
      system::create_message('Ошибка подключения к базе данных!', [], 503);
    }
    if (!$system_is_ready) {
        print('<h3>Система не готова для работы!</h3>');
        $database -> close();
        exit();
    }

    $identity;
    $redirect;
    if (
      !empty($_GET['redirect']) &&
      !empty($_GET['identity'])
    ) {
        $identity = is_numeric($_GET['identity']) ? intval($_GET['identity']) : strval($_GET['identity']);
        $redirect = strval($_GET['redirect']);
    } else {
        print('<h3>Некорректный запрос!</h3>');
        $database -> close();
        exit();
    }

    $service = $database -> list_of_services($identity);
    if (empty($service)) {
        print('<h3>Некорректный запрос!</h3>');
        $database -> close();
        exit();
    }
    $service = $service[0];

    if (
        !empty($_POST['email']) &&
        !empty($_POST['password']) &&
        empty($_SESSION['uuid'])
    ) {
        if ($uuid = $database -> user_login_in_form(
            $_POST['email'],
            $_POST['password']
        )) {
            $_SESSION['uuid'] = $uuid;
            $_SESSION['is_ok'] = true;
        } else {
            $_SESSION['is_ok'] = false;
            $_SESSION['not_auth'] = true;
        }
    }

    if (
        isset($_POST['grant']) &&
        !empty($_SESSION['uuid']) &&
        $_SESSION['is_ok']
    ) {
        if ($_POST['grant'] === '1') {
            if ($database -> user_login(
                '',
                '',
                getallheaders()['User-Agent'],
                system::get_ip_address(),
                $service['id'],
                $_SESSION['uuid']
            )) {
                $jwt = $tokens -> create_jwt_token($database -> get_user($_SESSION['uuid']), $_SESSION['uuid'], $service['name']);
                $refresh = $tokens -> create_refresh_token($jwt);
                $database -> save_refresh_token($_SESSION['uuid'], $refresh, getallheaders()['User-Agent'], $service['id']);
                header("Location: {$redirect}?status=1&jwt={$jwt}&refresh={$refresh}");
                $database -> close();
                exit();
            }
        } elseif ($_POST['grant'] === '0') {
            header("Location: {$redirect}?status=0");
            $database -> close();
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=no">
    <title>ГАПОУ СО НТТЭК | <?php print($_SESSION['is_ok'] ? 'Передать доступ' : 'Войти'); ?></title>
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
    <div id="nttek-alerts-area">
        <?php if (!empty($_SESSION['not_auth'])) { ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <b>Внимание!</b> <span>Вы ввели неправильные логин и (или) пароль. Попробуйте ещё раз.</span>
            </div>
            <?php unset($_SESSION['not_auth']); ?>
        <?php } ?>
    </div>
    <div class="container d-flex justify-content-center align-items-center" style="min-width: 100vw;min-height: 100vh;">
        <div class="card border-light shadow-lg" style="overflow: hidden;border-radius: 15px;overflow: hidden;max-width: 800px;">
            <div class="card-body d-flex flex-column" style="padding: 0;">
                <div id="nttek-auth-skew">
                    <div id="nttek-auth-logo"></div>
                </div>
                <div class="d-flex justify-content-center align-items-center" style="z-index: 1;">
                    <?php if (!$_SESSION['is_ok']) { ?>
                        <form class="needs-validation" style="margin: 50px;padding: 50px;" id="nttek-form-login" method="POST">
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
                                <div class="col d-flex flex-grow-1 flex-shrink-1 flex-fill justify-content-center" style="padding: 20px;"><button id="nttek-login" class="btn btn-primary" type="submit" style="width: 100%;filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));padding: 12px;background: linear-gradient(96.17deg, #6F78CC 4.88%, #3240BC 217.6%);border:none;">Войти</button></div>
                            </div>
                        </form>
                    <?php } else { ?>
                        <form style="margin: 50px;padding: 50px;" method="POST">
                            <div class="row">
                                <div class="col" style="padding: 20px;">
                                    <h1>Предоставить доступ...</h1>
                                    <p>Вы действительно хотите предоставить доступ сервису <b>"<?php print($service['name']); ?>"</b> к вашей учетной записи? <?php print($service['can_edit_user'] ? 'Этот сервис имеет возможность изменять ваши данные в системе.' : ''); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex flex-grow-1 flex-shrink-1 flex-fill justify-content-center" style="padding: 20px;">
                                    <button class="btn btn-primary" type="submit" name="grant" value="1" style="width: 100%;filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));padding: 12px;background: linear-gradient(96.17deg, #6F78CC 4.88%, #3240BC 217.6%);border: none;margin: 5px;">Да</button>
                                    <button class="btn btn-primary" type="submit" name="grant" value="0" style="width: 100%;filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));padding: 12px;background: linear-gradient(96.17deg, #6F78CC 4.88%, #3240BC 217.6%), var(--bs-red);border: none;margin: 5px;">Нет</button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>

</html>

<?php $database -> close(); ?>