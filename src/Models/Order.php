<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        return $this->db->find("orders", $id);
    }
    
    public function getUserOrders($userId)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        $stmt = $this->db->query($sql, [$orderId]);
        return $stmt->fetchAll();
    }
    
    public function createOrder($userId, $cartId, $data)
    {
        $this->db->beginTransaction();
        
        try {
            // Create order
            $orderId = $this->db->insert("orders", [
                "user_id" => $userId,
                "total" => $data["total"],
                "status" => "pending",
                "shipping_address" => $data["shipping_address"],
                "shipping_city" => $data["shipping_city"],
                "shipping_country" => $data["shipping_country"],
                "shipping_zip" => $data["shipping_zip"],
                "payment_method" => $data["payment_method"],
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ]);
            
            // Get cart items
            $cartModel = new Cart();
            $cartItems = $cartModel->getCartItems($cartId);
            
            // Add order items
            foreach ($cartItems as $item) {
                $this->db->insert("order_items", [
                    "order_id" => $orderId,
                    "product_id" => $item["product_id"],
                    "price" => $item["price"],
                    "quantity" => $item["quantity"],
                    "created_at" => date("Y-m-d H:i:s")
                ]);
            }
            
            // Clear cart
            $cartModel->clearCart($cartId);
            
            $this->db->commit();
            return $orderId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function updateStatus($orderId, $status)
    {
        return $this->db->update("orders", $orderId, [
            "status" => $status,
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
}
