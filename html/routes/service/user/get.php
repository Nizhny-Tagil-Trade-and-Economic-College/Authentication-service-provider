<?php
  require __DIR__ . '/../../../controllers/autoload.php';
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
            $payload = empty($_GET['uuid']) ?
            $database -> get_user(
                '',
                '',
                (!empty($_GET['start']) ? intval($_GET['start']) : 0),
                (!empty($_GET['length']) ? intval($_GET['length']) : 10),
            )
            :
            $database -> get_user($_GET['uuid'], $service_token);
            if ($service['can_get_list_of_users']) {
              if ($payload) 
                system::create_message('Список готов!', $payload);
              else
                system::create_message('Список пуст!');
            } else system::create_message('Представленному сервису запрещено получать список пользователей!', [], 403);
          } else system::create_message('Требуется аутентификация сервиса!', [], 401);
        } else system::create_message('Требуется Bearer-представление!', [], 401);
      } else system::create_message('Не предоставлены данные для идентификации!', [], 401);
      $database -> close();
    }
  } else system::create_message('Неподдерживаемый метод! Поддерживаемые методы: GET.', [], 405);