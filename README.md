# Bookbase

REST API для создания и получения книг и авторов из базы данных в формате JSON.
Сделано в качестве тестового задания (постановка задания в файле TASK.md)

## Настройка и сборка приложения
### Необходимые компоненты
- docker
- docker-compose

### Настройка
1. Склонировать ветку main данного репозитория на компьютер, где будет происходить сборка и запуск
2. В корне рабочего каталога проекта скопировать файл `.env.dist` в `.env`
3. Настроить окружение проекта в файле `.env`. Смысл переменных:
- *PUBLIC_PORT* - порт, по которому будет доступен API по протоколу HTTP (по умолчанию 8080, чтобы не конфликтовать с установленным локально вебсервером)
- *PMA_PORT* - порт, по которому будет доступен PHPMyAdmin (если будет включен, по умолчанию 8081, чтобы не конфликтовать с установленным локально вебсервером и основным контейнером)
- *MYSQL_PORT* - порт, по которому будет доступен сервер mysql (по умолчанию 4306, чтобы не конфликтовать с установленным локально сервером mysql)
- *MYSQL_DATABASE* - имя базы данных проекта
- *MYSQL_ROOT_PASSWORD* - пароль администратора СУБД mysql
- *MYSQL_USER* - имя пользователя СУБД mysql для БД проекта
- *MYSQL_PASSWORD* - пароль пользователя СУБД mysql для БД проекта
- *APP_ENV* - окружение symfony (`dev` для разработки, `prod` для продакшена)
- *APP_SECRET* - необходимо сгенерировать новый секрет (шестнадцатеричное число, 32 символа)
4. Дать права на выполнение всеми пользователями файлу `./install/install.sh`

### Сборка
1. Перейти в рабочий каталог проекта
2. Собрать контейнеры командой `docker-compose build`
3. Запустить контейнеры командой `docker-compose up` и убедиться по логу на экране, что сборка и разворачивание миграций прошли успешно (будет выведено сообщение `bookbase-install exited with code 0`)
4. Нажать Ctrl+C
5. В случае возникновения в логах ошибки `SQLSTATE[HY000] [2002] Connection refused` и сообщения `bookbase-install exited with code 1`:
    - отредактируйте файл `./install/install.sh` - необходимо увеличить время задержки (указано в секундах) в команде `sleep 120`
    - повторите пп. 3 и 4
6. Запустить контейнеры в фоновом режиме командой `docker-compose start`

## Управление 
- Для выключения контейнеров используется команда `docker-compose stop`
- Для последующего запуска контейнеров используется команда `docker-compose start`
- Для удаления контейнеров **(осторожно, это приведёт к очистке БД!)** используется команда `docker-compose down`
- Для запуска с дополнительным контейнером PHPMyAdmin (доступен через порт, указанный в переменной PMA_PORT) используется команда `docker-compose --profile dev up -d`

## Справка по API
### GET /author/search/{name}
Поиск по имени автора. Возвращает список авторов (до 100), имя которых содержит подстроку {name}, или [], если ничего не найдено.

### GET /author/{id}
Поиск по ID автора. Возвращает данные об авторе или {}, если ничего не найдено.

### POST /author/create
Добавляет нового автора в базу. Принимает параметр `name` с именем автора. Возвращает созданного автора в случае успеха и `{'error' => 'описание ошибки'}` в случае ошибки.

### GET /book/search/{name}
Поиск по названию книги. Возвращает список книг (до 100), русское или английское название которых содержит подстроку {name}, или [], если ничего не найдено.

### GET /book/{id}
Поиск по ID книги. Возвращает данные о книге или {}, если ничего не найдено.

### POST /book/create
Добавляет новую книгк в базу. Принимает параметры `name_en` и `name_ru` с английским и русским названием книги и параметр `authors` с ID автора (или списком ID авторов через запятую). Возвращает созданную книгу в случае успеха и `{'error' => 'описание ошибки'}` в случае ошибки.
