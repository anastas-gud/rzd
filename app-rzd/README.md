## Настройка БД:
### Запускаем композник с постгрей и пгадмином: docker compose up --build -d
### Для доступа к pgAdmin в вебе (логин пароль в pgadmin.env): http://localhost:5051/
### После входа создаем новый сервер (имя любое, в connections в первое поле hostname/address вписываем rzd_db, пароль и юзер из postgres.env)
## Запуск приложения
1. установка всех зависимостей: composer install
2. запуск миграций: php artisan migrate
3. запуск проекта: php artisan serve
### Доступ к приложению: http://127.0.0.1:8000

## ! Контейнеры не удаляем, volums не чистим, иначе потом нужно снова создавать сервер и запускать миграции.
### подключение: docker compose up -d
### выключение: docker compose stop