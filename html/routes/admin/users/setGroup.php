<?php
  require __DIR__ . '/../../../controllers/autoload.php';
  if (system::check_method()) {
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
    if ($system_is_ready) {
      if (!empty(getallheaders()['Authorization'])) {
        $token = getallheaders()['Authorization'];
        if (stripos($token, 'Bearer ') !== false) {
          $token = explode(' ', $token)[1];
          $token = $tokens -> decode_jwt_token($token);
          if ($token[0]) {
            if ($token[1] -> user_data -> group == 'system') {
              $check_payload = system::check_required_payload([
                'uuid',
                'identity',
                'group'
              ]);
              if (empty($check_payload)) {
                if (is_numeric($_POST['group'])) {
                  $service = $database -> list_of_services($_POST['identity']);
                  if (!empty($service)) {
                    $service = $service[0];
                    if ($database -> check_uuid_exsist($_POST['uuid'])) {
                      if (in_array(intval($_POST['group']), $service['groups'] -> list)) {
                        $database -> set_services_group($_POST['uuid'], $_POST['identity'], intval($_POST['group']));
                        system::create_message(
                          'Группа для сервиса установлена!',
                          [
                            'service_name' => $service['name']
                          ]
                        );
                      } else system::create_message('Неправильный номер группы пользователей!', [], 403);
                    } else system::create_message('Не найден пользователь!', [], 404);
                  } else system::create_message('Не найден сервис!', [], 404);
                } else system::create_message('group должен быть числовым значением!', [], 403);
              } else system::create_message(
                'Не хватает некоторых данных!',
                [
                  'not_transferred' => $check_payload
                ],
                400
              );
            } else system::create_message('Недостаточно прав для совершения данного действия!', [], 403);
          } else system::create_message(
            'Проблема с авторизацией',
            [
              'addition' => $token[1],
            ],
            401
          );
        } else system::create_message('Требуется Bearer-представление!', [], 401);
      } else system::create_message('Не предоставлены данные для идентификации!', [], 401);
      $database -> close();
    }
  } else system::create_message('Неподдерживаемый метод! Поддерживаемые методы: POST.', [], 405);