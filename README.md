# Провайдер аутинтификации колледжа
Провайдер сервиса аутентификации служит сервисом авторизаии пользователей в микросервисах ГАПОУ СО "Нижнетагильского торгово-экономического колледжа". Базовая идентифтикация пользователей состоит из двух токенов:
 - JWT-токен (*токен короткой сессии*). Этот токен, с помощью которого производится основная идентификация токена. Токен выдается только тем сервисам, которые афилированны с провайдером аутентификации. Время жизни JWT-токена: 30 минут. В JWT-токене представлена основная информация о пользователе и сервисе, которому выдан токен.
 - Refresh-токен (*токен долгосрочной сессии*). Этот токен служит токеном, с помощью которого пролонгируется действие JWT-токена. В случае пролонгации токенов, провайдер аутентификации генерирует новую пару JWT и Refresh токенов.
# Установка
### 1. Docker
Для проверки и работы можно воспользоваться Docker. Для этого скопируйте содержимое репозитория себе на устройство, и запустите sh-файл `start.sh`:
```bash
$ sudo sh start.sh
```
### 2. Apache2
Для установки сервиса при использовании Apache2, просто скопируйте содержимое папки `html` в ваш сервер, проведите настройку сервера Apache2. У сервера должны быть установлены следующие модули:
 - php-xml
 - php-json
 - php-mysqli
 - composer
 - php-mbstring
# Постнастройка
После установки сервиса, вам нужно сделать запрос на постинсталляционную настроку сервиса:
