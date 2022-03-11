<?php
  require __DIR__ . '/../../../controllers/autoload.php';
  if (system::check_method()) {
    $database;
    $system_is_ready = false;
    try {
      $database = new connect();
      $system_is_ready = true;
    } catch (ErrorException $e) {
      system::create_message('Ошибка подключения к базе данных!', [], 503);
    }
    if ($system_is_ready) {
      if (!empty(getallheaders()['Authorization'])) {
        $service_token = getallheaders()['Authorization'];
        if (stripos($service_token, 'Bearer ') !== false) {
          $service_token = explode(' ', $service_token)[1];
          $service = $database -> list_of_services($service_token);
          if (!empty($service)) {
            $service = $service[0];
            if ($service['can_edit_user']) {
              $check_payload = system::check_required_payload([
                'jwt',
                'refresh'
              ]);
              if (empty($check_payload)) {
                if ($database -> purge_refresh_token(
                  $_POST['refresh'],
                  getallheaders()['User-Agent']
                ))
                  system::create_message('Вышли удачно!');
                else system::create_message('Подложный токен авторизации!', [], 401);
              } else system::create_message(
                'Не хватает некоторых данных!',
                [
                  'not_transferred' => $check_payload
                ],
                400
              );
            } else system::create_message('Представленному сервису запрещено изменять данные пользователей!', [], 403);
          } else system::create_message('Требуется аутентификация сервиса!', [], 401);
        } else system::create_message('Требуется Bearer-представление!', [], 401);
      } else system::create_message('Не предоставлены данные для идентификации!', [], 401);
      $database -> close();
    }
  } else system::create_message('Неподдерживаемый метод! Поддерживаемые методы: POST.', [], 405);