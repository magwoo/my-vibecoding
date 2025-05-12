<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . "/src/bootstrap.php"; 

echo "Bootstrap loaded<br>";

$controller = new \App\Controllers\AuthController();
echo "AuthController created<br>";

$controller->register();
echo "register method called<br>"; 