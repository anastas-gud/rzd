крч запускаем бд: docker compose up --build -d

pgAdmin: http://localhost:5051/

логин пароль в pgadmin.env

после входа создаем новый сервер (имя любое, в connections в первое поле hostname/address вписываем rzd_db, пароль и юзер из postgres.env)

запуск миграций: php artisan migrate

запуск проекта: php artisan serve

приложение: http://127.0.0.1:8000

Контейнеры не удаляем, volums не чистим, иначе потом нужно снова создавать сервер и запускать миграции

docker compose up -d

docker compose stop
