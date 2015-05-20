Yii2 Database migration extension
===

It helps to migrate with multiple directories.


Installation
------------

add to the require section of your composer.json:

```json
"uzproger/migrator": "*",
```

add to the console.php:
```php
  ...
  'controllerMap' => [
    'migrate' => [
      'class' => 'uzproger\migrator\MigrateController',
      'additionalPaths' => [
        [
          'name' => 'First Migration Path Name',
          'path' => 'First Migration Full Path',
        ],
        [
          'name' => 'Second Migration Path Name',
          'path' => 'Second Migration Full Path',
        ],
        ...
      ],
    ],
    ...
  ],  
```