<?php
namespace App\Models;

use App\Core\Database;

class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        return $this->db->find("users", $id);
    }
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }
    
    public function create($data)
    {
        return $this->db->insert("users", $data);
    }
    
    public function update($id, $data)
    {
        return $this->db->update("users", $id, $data);
    }
    
    public function delete($id)
    {
        return $this->db->delete("users", $id);
    }
    
    public function register($email, $password)
    {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        return $this->db->insert("users", [
            "email" => $email,
            "password" => $passwordHash,
            "role" => "customer",
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
    
    public function verifyPassword($user, $password)
    {
        return password_verify($password, $user["password"]);
    }
    
    public function updateProfile($userId, $data)
    {
        // Only allow updating specific fields
        $allowedFields = [
            "name",
            "phone",
            "address"
        ];
        
        $updateData = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (!empty($updateData)) {
            $updateData["updated_at"] = date("Y-m-d H:i:s");
            return $this->db->update("users", $userId, $updateData);
        }
        
        return false;
    }
    
    public function changePassword($userId, $newPassword)
    {
        // Hash new password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->db->update("users", $userId, [
            "password" => $passwordHash,
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
    
    public function getAllUsers($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT id, email, name, role, created_at, updated_at
            FROM users
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        return $this->db->query($sql, [$perPage, $offset])->fetchAll();
    }
    
    public function getUserCount()
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $this->db->query($sql)->fetch();
        return $result ? (int)$result["count"] : 0;
    }
}
