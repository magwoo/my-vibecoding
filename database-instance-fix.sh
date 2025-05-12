#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated Database.php file
cat > tmp/Database.php << 'EOF'
<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;
    
    private function __construct()
    {
        $dsn = "mysql:host=" . Config::get("db.host") . ";dbname=" . Config::get("db.name") . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, Config::get("db.user"), Config::get("db.pass"), $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please check logs for details.");
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        
        return self::$instance;
    }
    
    // Get the PDO instance
    public function getPDO()
    {
        return $this->pdo;
    }
    
    public static function init()
    {
        // Just trigger the connection to be established
        self::getInstance();
    }
    
    // Find a record by ID
    public function find($table, $id)
    {
        $sql = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
    
    // Insert a new record
    public function insert($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->pdo->lastInsertId();
    }
    
    // Update a record
    public function update($table, $id, $data)
    {
        $setClause = implode(" = ?, ", array_keys($data)) . " = ?";
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    // Delete a record
    public function delete($table, $id)
    {
        $sql = "DELETE FROM {$table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$id]);
    }
    
    // Execute a custom query
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt;
    }
    
    // Begin a transaction
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    // Commit a transaction
    public function commit()
    {
        return $this->pdo->commit();
    }
    
    // Rollback a transaction
    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}
EOF

# Copy fixed Database.php to Docker container
docker cp tmp/Database.php phone-store-php-1:/var/www/html/src/Core/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 

echo "Database instance fix applied successfully." 