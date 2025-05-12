#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create User model
echo '<?php
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
}' > tmp/User.php

# Create Cart model
echo '<?php
namespace App\Models;

use App\Core\Database;

class Cart
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function initCart($userId)
    {
        // Check if user already has a cart
        $cart = $this->getCartByUserId($userId);
        
        if (!$cart) {
            // Create a new cart for the user
            $this->db->insert("carts", [
                "user_id" => $userId,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ]);
        }
    }
    
    public function getCartByUserId($userId)
    {
        $sql = "SELECT * FROM carts WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetch();
    }
    
    public function getCartItems($cartId)
    {
        $sql = "SELECT ci.*, p.name, p.price, p.image_url FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        return $stmt->fetchAll();
    }
    
    public function getCartTotal($cartId)
    {
        $sql = "
            SELECT SUM(ci.quantity * p.price) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ";
        
        $result = $this->db->query($sql, [$cartId])->fetch();
        return $result ? (float)$result["total"] : 0;
    }
    
    public function addItem($cartId, $productId, $quantity = 1)
    {
        // Check if item already exists
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $stmt = $this->db->query($sql, [$cartId, $productId]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Update quantity
            $newQuantity = $item["quantity"] + $quantity;
            $this->db->update("cart_items", $item["id"], ["quantity" => $newQuantity]);
        } else {
            // Add new item
            $this->db->insert("cart_items", [
                "cart_id" => $cartId,
                "product_id" => $productId,
                "quantity" => $quantity,
                "created_at" => date("Y-m-d H:i:s")
            ]);
        }
        
        // Update cart last modified time
        $this->updateCartTimestamp($cartId);
    }
    
    public function updateItemQuantity($itemId, $quantity)
    {
        // Get cart ID for timestamp update
        $sql = "SELECT cart_id FROM cart_items WHERE id = ?";
        $result = $this->db->query($sql, [$itemId])->fetch();
        $cartId = $result ? $result["cart_id"] : null;
        
        if ($quantity > 0) {
            // Update quantity
            $this->db->update("cart_items", $itemId, ["quantity" => $quantity]);
        } else {
            // Remove item if quantity is 0 or negative
            $this->removeItem($itemId);
        }
        
        // Update cart last modified time
        if ($cartId) {
            $this->updateCartTimestamp($cartId);
        }
    }
    
    public function removeItem($itemId)
    {
        // Get cart ID for timestamp update
        $sql = "SELECT cart_id FROM cart_items WHERE id = ?";
        $result = $this->db->query($sql, [$itemId])->fetch();
        $cartId = $result ? $result["cart_id"] : null;
        
        // Remove item
        $this->db->delete("cart_items", $itemId);
        
        // Update cart last modified time
        if ($cartId) {
            $this->updateCartTimestamp($cartId);
        }
    }
    
    public function clearCart($cartId)
    {
        // Remove all items
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $this->db->query($sql, [$cartId]);
        
        // Update cart last modified time
        $this->updateCartTimestamp($cartId);
    }
    
    public function getCartItemCount($cartId)
    {
        $sql = "SELECT SUM(quantity) as count FROM cart_items WHERE cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        $result = $stmt->fetch();
        return $result ? (int)$result["count"] : 0;
    }
    
    private function updateCartTimestamp($cartId)
    {
        $this->db->update("carts", $cartId, [
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
}' > tmp/Cart.php

# Copy models to Docker container
docker cp tmp/User.php phone-store-php-1:/var/www/html/src/Models/
docker cp tmp/Cart.php phone-store-php-1:/var/www/html/src/Models/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 