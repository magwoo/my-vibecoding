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
                "total_amount" => $data['total'],
                "status" => "pending",
                "shipping_address" => $data['shipping_address'],
                "payment_method" => $data['payment_method'],
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
                    "product_id" => $item['product_id'],
                    "quantity" => $item['quantity'],
                    "price" => $item['price']
                ]);
                
                // Update product stock
                $this->db->query(
                    "UPDATE products SET stock = stock - ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
            
            // Clear the cart
            $cartModel->clearCart($cartId);
            
            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getOrderCount($status = null)
    {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getAllOrders($status = null, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $params = [];
        
        $sql = "SELECT o.*, u.email as user_email 
               FROM orders o 
               LEFT JOIN users u ON o.user_id = u.id";
        
        if ($status) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        $orders = $stmt->fetchAll();
        
        // Get items for each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
            $order['total_items'] = array_sum(array_column($order['items'], 'quantity'));
        }
        
        return $orders;
    }
    
    public function updateStatus($orderId, $status)
    {
        return $this->db->update("orders", $orderId, [
            "status" => $status,
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
}
