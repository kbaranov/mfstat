Поднять docker контейнеры:
`docker-compose up`

Зайти в контейнер php:
`docker exec -it mfstat_php bash`

Внутри контейнера установить зависимости:
`composer install`

Внутри контейнера создать проект на Symfony:
