<?php

namespace Models;

use Utils\Database;

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Получить администратора по ID
    public function getById($id) {
        $sql = "SELECT id, email FROM administrators WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Получить администратора по Email
    public function getByEmail($email) {
        $sql = "SELECT id, email, password_hash FROM administrators WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }
    
    // Проверка правильности пароля
    public function verifyPassword($email, $password) {
        $admin = $this->getByEmail($email);
        
        if (!$admin) {
            return false;
        }
        
        return password_verify($password, $admin['password_hash']);
    }
}
