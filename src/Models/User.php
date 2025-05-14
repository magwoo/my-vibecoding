<?php

namespace Models;

use Utils\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Получить пользователя по ID
    public function getById($id) {
        $sql = "SELECT id, email, created_at FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Получить пользователя по Email
    public function getByEmail($email) {
        $sql = "SELECT id, email, password_hash, created_at FROM users WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }
    
    // Создать нового пользователя
    public function create($email, $password) {
        // Проверяем, не существует ли уже пользователь с таким email
        if ($this->getByEmail($email)) {
            return false;
        }
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
        $this->db->query($sql, [$email, $passwordHash]);
        
        return $this->db->lastInsertId();
    }
    
    // Проверка правильности пароля
    public function verifyPassword($email, $password) {
        $user = $this->getByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password_hash']);
    }
    
    // Обновить данные пользователя
    public function update($id, $data) {
        $sets = [];
        $params = [];
        
        // Собираем поля для обновления
        if (isset($data['email'])) {
            $sets[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $sets[] = "password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        if (empty($sets)) {
            return false;
        }
        
        // Добавляем ID в конец массива параметров
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(", ", $sets) . " WHERE id = ?";
        
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
}
