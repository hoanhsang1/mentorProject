<?php

/* ================= AUTH ================= */

$router->get('/login', 'App\Controllers\Web\AuthController@loginForm');
$router->post('/login', 'App\Controllers\Web\AuthController@login');

$router->get('/register', 'App\Controllers\Web\AuthController@registerForm');
$router->post('/register', 'App\Controllers\Web\AuthController@register');

$router->get('/logout', 'App\Controllers\Web\AuthController@logout');


/* ================= WEB PAGES ================= */

$router->get('/', 'App\Controllers\Web\DashboardController@index');
$router->get('/dashboard', 'App\Controllers\Web\DashboardController@index');

$router->get('/calendar', 'App\Controllers\Web\CalendarController@index');
$router->get('/todo', 'App\Controllers\Web\TodoController@index');
$router->get('/profile', 'App\Controllers\Web\ProfileController@index');
$router->get('/settings', 'App\Controllers\Web\SettingsController@index');
$router->get('/profile', 'App\Controllers\Web\ProfileController@index');
$router->post('/profile/upload', 'App\Controllers\Web\ProfileController@upload');



/* ================= API ================= */

/* Calendar API */
$router->get('/calendar/api', 'App\Controllers\Api\CalendarController@handle');
$router->post('/calendar/api', 'App\Controllers\Api\CalendarController@handle');

/* Todo API */
$router->post('/todo/api/createGroup', 'App\Controllers\Api\TodoController@createGroup');
$router->post('/todo/api/updateGroup', 'App\Controllers\Api\TodoController@updateGroup');
$router->post('/todo/api/deleteGroup', 'App\Controllers\Api\TodoController@deleteGroup');

$router->get('/todo/api/task', 'App\Controllers\Api\TodoController@getAllTask');
$router->get('/todo/api/task/detail', 'App\Controllers\Api\TodoController@getTaskDetail');

$router->post('/todo/api/createTask', 'App\Controllers\Api\TodoController@createTask');
$router->post('/todo/api/toggleStatus', 'App\Controllers\Api\TodoController@toggleStatus');
$router->post('/todo/api/deleteTask', 'App\Controllers\Api\TodoController@deleteTask');
$router->post('/todo/api/updateTask', 'App\Controllers\Api\TodoController@updateTask');
