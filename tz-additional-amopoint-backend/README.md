# Amopoint Page Visit Counter

Backend-сервис для учета посещений страниц. Приложение принимает события от JavaScript-трекера, сохраняет визиты в SQLite и показывает закрытую статистику по часам, уникальным посетителям и городам.

## Стек

- Laravel 13;
- Laravel Sanctum;
- SQLite;
- Chart.js;
- Docker Compose;
- Nginx + PHP-FPM;
- Swagger UI.

## Возможности

- публичный трекер `/tracker.js` для подключения на сайт;
- публичный endpoint `POST /api/visits` для записи визитов;
- авторизация администратора через Sanctum;
- страница статистики с графиком посещений по часам и диаграммой городов;
- Swagger/OpenAPI спецификация;
- миграции и сидеры;
- Postman-коллекция для проверки записи визитов;
- хранение SQLite-базы внутри Docker volume;
- опциональное определение города по IP через GeoLite2 City.

Endpoints сброса и смены пароля намеренно не реализованы по условию задания.

## Требования

- Docker;
- Docker Compose.

## Переменные окружения

Создать `.env`:

```bash
cp .env.example .env
```

В `.env` должны быть заданы данные администратора:

```dotenv
ADMIN_NAME=Admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=strong-password-here
```

`ADMIN_PASSWORD` обязателен до запуска сидера. Пароль не хранится в коде и сохраняется в базе только как hash.

## Первый запуск через Docker

Собрать PHP-образ:

```bash
docker compose build app
```

Запустить контейнеры:

```bash
docker compose up -d
```

Установить Composer-зависимости внутри контейнера:

```bash
docker compose exec app composer install
```

Сгенерировать ключ приложения, если `APP_KEY` еще пустой:

```bash
docker compose exec app php artisan key:generate
```

Применить миграции и заполнить базу:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

После этого приложение доступно по адресу:

```text
http://127.0.0.1:8877/
```

## Docker-сервисы

`app` - PHP-FPM контейнер с Laravel.

`nginx` - веб-сервер, публикует приложение на порту `8877`.

`swagger` - Swagger UI на порту `8088`.

## SQLite в Docker

База хранится не в файле проекта, а внутри Docker named volume:

```text
amopoint-counter_app-database
```

Путь внутри контейнера:

```text
/var/www/storage/database/database.sqlite
```

Контейнер `app` создает директорию и SQLite-файл при старте. Поэтому для Docker-запуска не нужно выполнять `touch database/database.sqlite` на хосте.

Посмотреть список volume:

```bash
docker volume ls
```

Полностью удалить контейнеры и базу:

```bash
docker compose down -v
```

Остановить контейнеры без удаления базы:

```bash
docker compose down
```

## Ссылки

Приложение и статистика:

```text
http://127.0.0.1:8877/
```

Тестовая HTML-страница трекера:

```text
http://127.0.0.1:8877/tracker-test.html
```

Сам JS-трекер:

```text
http://127.0.0.1:8877/tracker.js
```

Swagger UI:

```text
http://127.0.0.1:8088/
```

OpenAPI-файл:

```text
docs/openapi.yaml
```

Postman-коллекция:

```text
docs/postman/amopoint-counter.postman_collection.json
```

## Подключение трекера

Минимальный вариант:

```html
<script async src="http://127.0.0.1:8877/tracker.js"></script>
```

При загрузке страницы скрипт отправляет событие на `POST /api/visits`.

Трекер передает:

- стабильный `visitor_id` из `localStorage`;
- host сайта;
- title страницы;
- URL страницы без query string и hash;
- referrer без query string и hash;
- тип устройства;
- браузер;
- платформу;
- размер экрана;
- язык браузера;
- timezone.

IP не собирается в браузере. Сервер получает IP из HTTP-запроса.

## Проверка записи визитов

Вариант через тестовую страницу:

1. Открыть `http://127.0.0.1:8877/tracker-test.html`.
2. Открыть DevTools -> Network.
3. Обновить страницу.
4. Проверить запрос `POST /api/visits`.
5. Успешный ответ: статус `201`, `data.accepted = true`.

Вариант через Postman:

1. Импортировать `docs/postman/amopoint-counter.postman_collection.json`.
2. Проверить переменную `base_url`: `http://127.0.0.1:8877`.
3. Запустить запросы из папки `Запись визитов в БД`.

Postman-запросы передают разные города, устройства и повторного посетителя, чтобы проверить запись и агрегацию уникальных визитов.

## GeoLite2 City

Определение города по IP реализовано как опциональная интеграция с файлом `GeoLite2-City.mmdb`.

Файл базы не входит в репозиторий. Доступ к GeoLite2 оформляется отдельно через MaxMind: требуется регистрация аккаунта, заполнение данных, принятие условий использования и получение license key. Нельзя считать, что файл можно получить без этих шагов; этот вопрос нужно закрывать отдельно от запуска проекта.

Официальная страница: [GeoLite2 Free Geolocation Data](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data/).

Если файл получен, положить его сюда:

```text
storage/app/geoip/GeoLite2-City.mmdb
```

Переменные:

```dotenv
GEOIP_DATABASE_PATH=storage/app/geoip/GeoLite2-City.mmdb
GEOIP_LOCALES=en,ru
```

Если GeoLite2 не подключена, IP приватный или IP не найден в базе, сервис использует fallback `city` и `country` из тела запроса. Если fallback не передан, город сохраняется как `Unknown`.

## API

```text
POST /api/visits
POST /api/auth/login
GET  /api/auth/me
POST /api/auth/logout
GET  /api/statistics/summary
GET  /tracker.js
```

Swagger-описание API доступно в `docs/openapi.yaml` и в Swagger UI.

## Безопасность

- Пароль администратора приходит из `ADMIN_PASSWORD`.
- Пароли хешируются через `Hash::make`.
- Статистика защищена Sanctum Bearer-токеном.
- Токен статистики выпускается с ability `statistics:read`.
- Срок жизни токена задается через `SANCTUM_EXPIRATION`.
- Логин ограничен rate limit: 5 попыток в минуту на email + IP.
- Запись визитов ограничена rate limit: 60 запросов в минуту на IP.
- IP хранится зашифрованным, дополнительно сохраняется HMAC-хеш.
- `visitor_id` и `user_agent` сохраняются только как HMAC-хеши.
- Query string и hash удаляются из `page_url` и `referrer`.
- Входные данные валидируются по типу, длине и допустимым значениям.
- Чувствительные ответы отдаются с `Cache-Control: no-store`.
- Nginx и Laravel middleware выставляют базовые security headers.
- CORS открыт для работы трекера на внешних сайтах. Для production-окружения список origins нужно ограничивать или разделять CORS-политику для трекера и административного интерфейса.

## Проверки

Проверить Laravel-тесты внутри контейнера:

```bash
docker compose exec app php artisan test
```

Проверить Docker Compose конфигурацию:

```bash
docker compose config --quiet
```

Пересобрать PHP-образ:

```bash
docker compose build app
```

## Troubleshooting

Если сидер падает с ошибкой `ADMIN_PASSWORD must be set before seeding`, проверь `ADMIN_PASSWORD` в `.env`.

Если сайт открывается, но зависимостей нет, выполни:

```bash
docker compose exec app composer install
```

Если город сохраняется как `Unknown`, проверь наличие `GeoLite2-City.mmdb`, путь `GEOIP_DATABASE_PATH` и тип IP-адреса.

Если нужно пересоздать базу с нуля:

```bash
docker compose down -v
docker compose up -d
docker compose exec app php artisan migrate:fresh --seed
```

