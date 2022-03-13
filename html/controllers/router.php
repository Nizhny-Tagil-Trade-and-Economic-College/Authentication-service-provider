<?php
  /*
    Контроллер Router.php.
    Предназначен для обработки HTTP-путей. Все пути попадают в $_GET['path_controller'].
  */

  class Pathfinder {
    private $directory = __DIR__ . '/../routes';
    private $finder = '';

    public function __construct() {
      $this -> finder = !empty($_GET['path_controller']) ? $_GET['path_controller'] : '';
      if (!empty($this -> finder)) {
        if (file_exists($this -> get_path())) {
          if (stripos($this -> finder, 'frontend') === false)
            $this -> set_content_type();
          else {
            $this -> set_content_type(
              mime_content_type($this -> get_path())
            );
            if (in_array(
              mime_content_type($this -> get_path()),
              ['text/x-php']
            )) $this -> set_content_type('text/html');
          }
          require $this -> get_path();
        } else $this -> show_code();
      } else {
        $this -> finder = 'index';
        require $this -> get_path();
        $this -> set_content_type();
      }
    }

    private function get_path() {
      if (!empty(pathinfo($this -> finder)['extension'])) {
        $ext = pathinfo($this -> finder);
        return "{$this -> directory}/{$ext['dirname']}/{$ext['basename']}";
      } else return "{$this -> directory}/{$this -> finder}.php";
    }

    private function show_code(int $code = 404) {
      $this -> set_content_type();
      http_response_code($code);
      print(json_encode([
        'answer' => 'Смотри в HTTP Response Code.',
        'code' => $code,
      ]));
    }

    private function set_content_type(string $type = 'application/json') {
      header("Content-Type: {$type}");
    }
  }
