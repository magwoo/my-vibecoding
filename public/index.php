<?php 
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../src/bootstrap.php"; 

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); 
$path = trim($path, "/"); 
$path = empty($path) ? "home" : $path; 

try { 
    $router = new \App\Core\Router(); 
    $router->dispatch($path); 
} catch (\Exception $e) { 
    error_log($e->getMessage()); 
    include __DIR__ . "/errors/500.php"; 
}
