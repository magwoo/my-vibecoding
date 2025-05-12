<?php

namespace App\Core;

class Router
{
    private $routes = [
        // Home page
        "home" => ["controller" => "HomeController", "action" => "index"],
        
        // Auth routes
        "login" => ["controller" => "AuthController", "action" => "login"],
        "register" => ["controller" => "AuthController", "action" => "register"],
        "logout" => ["controller" => "AuthController", "action" => "logout"],
        
        // User routes
        "account" => ["controller" => "UserController", "action" => "account"],
        "orders" => ["controller" => "UserController", "action" => "orders"],
        
        // Product routes
        "products" => ["controller" => "ProductController", "action" => "index"],
        "products/search" => ["controller" => "ProductController", "action" => "search"],
        "product" => ["controller" => "ProductController", "action" => "show"],
        
        // Cart routes
        "cart" => ["controller" => "CartController", "action" => "index"],
        "cart/add" => ["controller" => "CartController", "action" => "add"],
        "cart/update" => ["controller" => "CartController", "action" => "update"],
        "cart/remove" => ["controller" => "CartController", "action" => "remove"],
        "checkout" => ["controller" => "OrderController", "action" => "checkout"],
        
        // Admin routes
        "admin" => ["controller" => "AdminController", "action" => "index"],
        "admin/products" => ["controller" => "AdminController", "action" => "products"],
        "admin/product/add" => ["controller" => "AdminController", "action" => "addProduct"],
        "admin/product/edit" => ["controller" => "AdminController", "action" => "editProduct"],
        "admin/product/delete" => ["controller" => "AdminController", "action" => "deleteProduct"],
        "admin/orders" => ["controller" => "AdminController", "action" => "orders"],
        "admin/order" => ["controller" => "AdminController", "action" => "showOrder"],
        "admin/order/update-status" => ["controller" => "AdminController", "action" => "updateOrderStatus"],
    ];

    public function dispatch($path)
    {
        // Check if the path exists in routes
        if (isset($this->routes[$path])) {
            $route = $this->routes[$path];
            $controllerName = "\\App\\Controllers\\" . $route["controller"];
            $actionName = $route["action"];
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
                    return;
                }
            }
        }
        
        // Check for dynamic routes (e.g., product/123)
        $pathParts = explode("/", $path);
        
        if (count($pathParts) == 2) {
            if ($pathParts[0] == "product" && is_numeric($pathParts[1])) {
                $controller = new \App\Controllers\ProductController();
                $controller->show($pathParts[1]);
                return;
            }
            
            if ($pathParts[0] == "admin" && $pathParts[1] == "order" && isset($_GET["id"])) {
                $controller = new \App\Controllers\AdminController();
                $controller->showOrder($_GET["id"]);
                return;
            }
            
            if ($pathParts[0] == "admin" && $pathParts[1] == "product" && isset($_GET["id"])) {
                $controller = new \App\Controllers\AdminController();
                $controller->editProduct($_GET["id"]);
                return;
            }
        }
        
        // No route found, show 404 page
        header("HTTP/1.0 404 Not Found");
        include PUBLIC_DIR . "/errors/404.php";
    }

    public static function redirect($path)
    {
        header("Location: /" . $path);
        exit;
    }
}
