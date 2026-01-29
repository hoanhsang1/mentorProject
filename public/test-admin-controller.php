<?php
// test-admin-controller.php
session_start();
$_SESSION['user_id'] = 'test';
$_SESSION['role'] = 'admin';

echo "Testing AdminController...<br>";

// Test Web AdminController
$webControllerFile = __DIR__ . '/../app/controllers/Web/AdminController.php';
echo "Web Controller file exists: " . (file_exists($webControllerFile) ? 'YES' : 'NO') . "<br>";

// Test Api AdminController
$apiControllerFile = __DIR__ . '/../app/controllers/Api/AdminController.php';
echo "API Controller file exists: " . (file_exists($apiControllerFile) ? 'YES' : 'NO') . "<br>";

if (file_exists($apiControllerFile)) {
    require_once __DIR__ . '/../app/Core/Controller.php';
    require_once $apiControllerFile;
    
    echo "Class exists: " . (class_exists('App\Controllers\Api\AdminController') ? 'YES' : 'NO') . "<br>";
    
    $controller = new App\Controllers\Api\AdminController();
    echo "Controller created: " . ($controller ? 'YES' : 'NO') . "<br>";
    echo "Method handle exists: " . (method_exists($controller, 'handle') ? 'YES' : 'NO') . "<br>";
}
?>