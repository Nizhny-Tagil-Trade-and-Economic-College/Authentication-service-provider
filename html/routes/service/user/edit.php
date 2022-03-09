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
                'uuid'
              ]);
              if (empty($check_payload)) {
                $continue = true;
                if (!empty($_POST['group'])) {
                  if (
                    stripos($_POST['group'], 'students') === true ||
                    !in_array($_POST['group'], ['system', 'teachers', 'users'])
                  ) $continue = false;
                }
                if ($continue) {
                  $uuid = $_POST['uuid'];
                  unset($_POST['uuid']);
                  if ($new_auth = $database -> edit_user(
                    $uuid,
                    $_POST
                  )) system::create_message('Данные пользователя успешно изменены!');
                  else system::create_message(
                    'Нет возможности изменить данные пользователя.',
                    [],
                    400
                  );
                } else system::create_message(
                  'Некорректно составлен group.',
                  [],
                  400
                );
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