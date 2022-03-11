<?php
  require __DIR__ . '/../../../../controllers/autoload.php';
  if (system::check_method(['GET'])) {
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
            if ($service['payload']) {
              $check_payload = system::check_required_payload([
                'uuid'
              ], 'GET');
              if (empty($check_payload)) {
                if ($payload = $database -> get_users_payload(
                  $_GET['uuid'],
                  $service['id']
                ))
                  system::create_message(
                    'Полезная нагрузка готова!',
                    $payload
                  );
                else system::create_message(
                  'Не найдена полезная нагрузка!',
                  [],
                  404
                );
              } else system::create_message(
                'Не хватает некоторых данных!',
                [
                  'not_transferred' => $check_payload
                ],
                400
              );
            } else system::create_message('Представленному сервису запрещено получать полезную нагрузку!', [], 403);
          } else system::create_message('Требуется аутентификация сервиса!', [], 401);
        } else system::create_message('Требуется Bearer-представление!', [], 401);
      } else system::create_message('Не предоставлены данные для идентификации!', [], 401);
      $database -> close();
    }
  } else system::create_message('Неподдерживаемый метод! Поддерживаемые методы: GET.', [], 405);