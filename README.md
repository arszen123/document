# Document Management

Linux:
```bash
cd /project/utvonal
composer update

mkdir -p module/Document/src/assets/files
```
A létrehozott mappának irási és olvasási jog kell.
Valamint a data mappának is rekurzivan irás/olvasás jogot kell adni.

Szükséges egy MySql adatbázis, amibe be kell tölteni a module/Document/config/database.sql tartalmát.
Létre kell hozni egy config/autoload/local.php fájlt a következő tartalommal (Szükséges pdo_mysql driver telepitése és engedélyezése a php.ini fájlban):
```php
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
```
## Futtathato:
#### php paranccsal:
```bash
php -S 127.0.0.1:8080 -t public
```

#### Apache virtualhost:
Feltételezem  a szükséges modulok includolva vannak. (vhosts, mod_rewrite)
```xml
<VirtualHost *:80>
        ServerName document.localhost
        DocumentRoot /project/utvonal/public
        <Directory /project/utvonal/public>
	        Options All
	        DirectoryIndex index.php
	        AllowOverride ALL
	        Require all granted
        </Directory>
</VirtualHost>
```

#### Felhasználó váltható a Module.php fájlban. (Az sql fájl betöltése után 1-es és 2-es id-vel rendelkező felhasználók vannak)

Feltételeztem, minden felhasználó csak a saját maga által készitett kategóriákhoz fér hozzá.
