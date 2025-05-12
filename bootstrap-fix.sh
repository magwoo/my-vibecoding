#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create fixed bootstrap.php file
echo '<?php
// Start session
session_start();

// Define constants
define("ROOT_DIR", dirname(__DIR__));
define("SRC_DIR", ROOT_DIR . "/src");
define("PUBLIC_DIR", ROOT_DIR . "/public");
define("VIEWS_DIR", SRC_DIR . "/Views");

// Load configuration
require_once SRC_DIR . "/config/config.php";

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace separator to directory separator
    $class = str_replace("\\", "/", $class);
    
    // Remove App\ prefix
    $class = str_replace("App/", "", $class);
    
    // Include the file
    $file = SRC_DIR . "/" . $class . ".php";
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

// Set error handling
error_reporting(E_ALL);
ini_set("display_errors", \App\Core\Config::get("app.debug") ? 1 : 0);
ini_set("log_errors", 1);
ini_set("error_log", ROOT_DIR . "/logs/php-error.log");

// Create logs directory if it doesn'\''t exist
if (!is_dir(ROOT_DIR . "/logs")) {
    mkdir(ROOT_DIR . "/logs", 0755, true);
}

// Initialize database connection
\App\Core\Database::init();

// CSRF protection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["csrf_token"]) || !isset($_SESSION["csrf_token"]) || 
        $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        header("HTTP/1.1 403 Forbidden");
        echo "CSRF token validation failed";
        exit;
    }
}

// Generate CSRF token if not already set
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}' > tmp/bootstrap.php

# Copy fixed bootstrap.php to Docker container
docker cp tmp/bootstrap.php phone-store-php-1:/var/www/html/src/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 