# logger
Mongodb log extension based on laravel logs


## config


Modify the following two configuration files

`config/logging.php`

```php
use CrCms\Log\MongoDBLogger;

'channels' => [
    'mongo' => [
        'driver' => 'custom',
        'via' => MongoDBLogger::class,
        'database' => [
            'driver' => 'mongodb',
            'collection' => 'logger',
        ],
    ],
]
```

`config/database.php`

```php
'connections' => [
    'mongodb' => [
        'driver' => 'mongodb',
        'host' => env('DB_MONGO_HOST', 'localhost'),
        'port' => env('DB_MONGO_PORT', 27017),
        'database' => env('DB_MONGO_DATABASE'),
        'username' => env('DB_MONGO_USERNAME'),
        'password' => env('DB_MONGO_PASSWORD'),
        'options' => [
            'database' => env('DB_MONGO_AUTH_DATABASE', 'admin')
        ]
    ],
]
```
