# Document Management

Linux:

cd /project/utvonal
composer update

mkdir -p module/Document/src/assets/files
irasi es olvasasi jog kell a letrehozott mappanak

Szukseges egy MySql adatbazis, amibe be kell tolteni a module/Document/config/database.sql tartalmat
Letre kell hozni egy config/autoload/local.php fajlt a kovetkezo tartalommal:

use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host' => 'host ex. 127.0.0.1',
                    'user' => 'username',
                    'password' => 'password',
                    'dbname' => 'databaseName'
                ]
            ],
        ],
    ],
];

futtathato:
php -S 127.0.0.1:8080 -t public
