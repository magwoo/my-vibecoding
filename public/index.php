<?php
session_start();

// Определяем корневой путь приложения
define('ROOT_PATH', dirname(__DIR__));

// Автозагрузка классов
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $path = ROOT_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Инициализация CSRF-токена для сессии
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Роутер запросов
require_once ROOT_PATH . '/src/Utils/Router.php';
$router = new Utils\Router();

// Обработка запроса
$router->dispatch();
