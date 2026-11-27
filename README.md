1. Запустить контейнеры
```
docker compose up -d --build
```
2. Загрузить дамп данных в бд
```
docker exec -i yii2-mysql-db mysql -uyii2 -p'secret' yii2 < test_db_data.sql
```
