<?php

namespace Utils;

class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct() {
        // Маршруты для основных страниц
        $this->routes['GET']['/'] = ['Controllers\HomeController', 'index'];
        $this->routes['GET']['/catalog'] = ['Controllers\ProductController', 'catalog'];
        $this->routes['GET']['/product/(\d+)'] = ['Controllers\ProductController', 'view'];
        $this->routes['GET']['/cart'] = ['Controllers\CartController', 'view'];
        $this->routes['GET']['/login'] = ['Controllers\AuthController', 'loginForm'];
        $this->routes['GET']['/register'] = ['Controllers\AuthController', 'registerForm'];
        $this->routes['GET']['/logout'] = ['Controllers\AuthController', 'logout'];
        $this->routes['GET']['/account'] = ['Controllers\AccountController', 'index'];
        $this->routes['GET']['/account/orders'] = ['Controllers\AccountController', 'orders'];
        $this->routes['GET']['/account/order/(\d+)'] = ['Controllers\AccountController', 'viewOrder'];
        
        // Маршруты для API
        $this->routes['POST']['/api/cart/add'] = ['Controllers\CartController', 'add'];
        $this->routes['POST']['/api/cart/update'] = ['Controllers\CartController', 'update'];
        $this->routes['POST']['/api/cart/remove'] = ['Controllers\CartController', 'remove'];
        $this->routes['POST']['/api/checkout'] = ['Controllers\OrderController', 'checkout'];
        $this->routes['POST']['/login'] = ['Controllers\AuthController', 'login'];
        $this->routes['POST']['/register'] = ['Controllers\AuthController', 'register'];
        
        // Маршруты для админ-панели
        $this->routes['GET']['/admin'] = ['Controllers\Admin\DashboardController', 'index'];
        $this->routes['GET']['/admin/login'] = ['Controllers\Admin\AuthController', 'loginForm'];
        $this->routes['POST']['/admin/login'] = ['Controllers\Admin\AuthController', 'login'];
        $this->routes['GET']['/admin/products'] = ['Controllers\Admin\ProductController', 'index'];
        $this->routes['GET']['/admin/products/create'] = ['Controllers\Admin\ProductController', 'create'];
        $this->routes['POST']['/admin/products/store'] = ['Controllers\Admin\ProductController', 'store'];
        $this->routes['GET']['/admin/products/edit/(\d+)'] = ['Controllers\Admin\ProductController', 'edit'];
        $this->routes['POST']['/admin/products/update/(\d+)'] = ['Controllers\Admin\ProductController', 'update'];
        $this->routes['POST']['/admin/products/delete/(\d+)'] = ['Controllers\Admin\ProductController', 'delete'];
        $this->routes['GET']['/admin/orders'] = ['Controllers\Admin\OrderController', 'index'];
        $this->routes['GET']['/admin/orders/(\d+)'] = ['Controllers\Admin\OrderController', 'view'];
        $this->routes['POST']['/admin/orders/update/(\d+)'] = ['Controllers\Admin\OrderController', 'updateStatus'];
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Проверяем наличие маршрута для данного метода
        if (!isset($this->routes[$method])) {
            $this->notFound();
            return;
        }
        
        // Проверяем все маршруты текущего метода
        foreach ($this->routes[$method] as $route => $handler) {
            // Преобразуем шаблон маршрута в регулярное выражение
            $pattern = '#^' . $route . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                // Удаляем полное совпадение из массива совпадений
                array_shift($matches);
                
                // Получаем класс контроллера и метод для вызова
                list($controllerClass, $methodName) = $handler;
                
                // Создаем экземпляр контроллера
                $controller = new $controllerClass();
                
                // Вызываем метод контроллера с параметрами из URL
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }
        
        // Если маршрут не найден
        $this->notFound();
    }
    
    private function notFound() {
        header('HTTP/1.1 404 Not Found');
        include ROOT_PATH . '/src/Views/errors/404.php';
        exit;
    }
}
