<?php
session_start();

// spl_autoload_register(function ($class) {
//     if (str_starts_with($class, 'App\\')) {
//         $class = str_replace('App\\', '', $class);
//         $path = __DIR__ . '/app/' . str_replace('\\', '/', $class) . '.php';

//         if (file_exists($path)) {
//             require_once $path;
//         }
//     }
// });
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) {
        $class = str_replace('App\\', '', $class);
        $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $class) . '.php';

        if (file_exists($path)) {
            require_once $path;
        } else {
            die("Autoload failed: " . $path);
        }
    }
});

require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/App.php';

$app = new App();
$app->run();
