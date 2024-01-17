# API TRANSACTION

## Tech Stack
- PHP 8.1 or higher : https://www.php.net/downloads.php
- Mysql (Database) : https://www.mysql.com/downloads/
- Composer : https://getcomposer.org/download/

please, all tech stack has been installed in your local 

## Configuration
All configuration is in `.env` file. running query scripts inside file sql/database.sql.
Import file API_Transaction_Detik.json in your Postman.

### Step by step run Project

```Shell
git clone git@github.com:humamalamin/4a0cf4faad59b6d0c9bc250ce780446473a7b456.git
cd project
composer install
config file .env
php -S localhost:8000
```

if finish to step on top, please test API use POSTMAN

### Running PHP CLI

```Shell
php transaction.php {references_id} {status}
```

### Check Standart Code PSR 12

```bash
vendor/bin/phpcs --standard=PSR12 app/

or 

composer style-code
```