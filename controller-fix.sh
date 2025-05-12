#!/bin/bash

# Create temporary directory
mkdir -p tmp/Core

# Create Controller class
echo '<?php

namespace App\Core;

abstract class Controller
{
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = [])
    {
        // Extract data to make variables available in the view
        extract($data);
        
        // Include header
        require_once VIEWS_DIR . "/layouts/header.php";
        
        // Include the view file
        require_once VIEWS_DIR . "/" . $view . ".php";
        
        // Include footer
        require_once VIEWS_DIR . "/layouts/footer.php";
    }
    
    protected function json($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
    
    protected function isLoggedIn()
    {
        return isset($_SESSION["user_id"]);
    }
    
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            Router::redirect("login");
        }
    }
    
    protected function requireAdmin()
    {
        if (!$this->isLoggedIn() || $_SESSION["user_role"] !== "admin") {
            header("HTTP/1.1 403 Forbidden");
            require_once PUBLIC_DIR . "/errors/403.php";
            exit;
        }
    }
    
    protected function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return $this->db->find("users", $_SESSION["user_id"]);
        }
        return null;
    }
    
    protected function validateInput($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            // Check if the field is required and empty
            if (strpos($rule, "required") !== false && (!isset($data[$field]) || trim($data[$field]) === "")) {
                $errors[$field] = ucfirst($field) . " is required";
                continue;
            }
            
            // Skip validation if the field is not required and empty
            if (!isset($data[$field]) || $data[$field] === "") {
                continue;
            }
            
            // Email validation
            if (strpos($rule, "email") !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Invalid email format";
            }
            
            // Min length validation
            if (preg_match("/min:(\d+)/", $rule, $matches)) {
                $minLength = (int)$matches[1];
                if (strlen($data[$field]) < $minLength) {
                    $errors[$field] = ucfirst($field) . " must be at least " . $minLength . " characters long";
                }
            }
            
            // Numeric validation
            if (strpos($rule, "numeric") !== false && !is_numeric($data[$field])) {
                $errors[$field] = ucfirst($field) . " must be a number";
            }
        }
        
        return $errors;
    }
}' > tmp/Core/Controller.php

# Copy controller to Docker container
docker cp tmp/Core/Controller.php phone-store-php-1:/var/www/html/src/Core/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 