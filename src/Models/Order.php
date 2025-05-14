<?php

namespace Models;

use Utils\Database;

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Получить заказ по ID
    public function getById($id) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Получить все заказы пользователя
    public function getByUserId($userId) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    // Получить все заказы (для админа)
    public function getAll($limit = null, $offset = 0, $filters = []) {
        $sql = "SELECT o.*, u.email as user_email FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE 1=1";
        $params = [];
        
        // Применяем фильтры
        if (!empty($filters)) {
            // Фильтр по статусу
            if (!empty($filters['status'])) {
                $sql .= " AND o.status = ?";
                $params[] = $filters['status'];
            }
            
            // Фильтр по ID пользователя
            if (!empty($filters['user_id'])) {
                $sql .= " AND o.user_id = ?";
                $params[] = $filters['user_id'];
            }
            
            // Фильтр по дате (от)
            if (!empty($filters['date_from'])) {
                $sql .= " AND DATE(o.created_at) >= ?";
                $params[] = $filters['date_from'];
            }
            
            // Фильтр по дате (до)
            if (!empty($filters['date_to'])) {
                $sql .= " AND DATE(o.created_at) <= ?";
                $params[] = $filters['date_to'];
            }
        }
        
        // Сортировка по дате (новые сначала)
        $sql .= " ORDER BY o.created_at DESC";
        
        // Применяем ограничение выборки
        if ($limit !== null) {
            $sql .= " LIMIT ?, ?";
            $params[] = (int)$offset;
            $params[] = (int)$limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Получить количество заказов
    public function countAll($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE 1=1";
        $params = [];
        
        // Применяем фильтры
        if (!empty($filters)) {
            // Фильтр по статусу
            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }
            
            // Фильтр по ID пользователя
            if (!empty($filters['user_id'])) {
                $sql .= " AND user_id = ?";
                $params[] = $filters['user_id'];
            }
            
            // Фильтр по дате (от)
            if (!empty($filters['date_from'])) {
                $sql .= " AND DATE(created_at) >= ?";
                $params[] = $filters['date_from'];
            }
            
            // Фильтр по дате (до)
            if (!empty($filters['date_to'])) {
                $sql .= " AND DATE(created_at) <= ?";
                $params[] = $filters['date_to'];
            }
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    // Создать новый заказ
    public function create($userId, $totalAmount) {
        $this->db->beginTransaction();
        
        try {
            // Создаем заказ
            $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
            $this->db->query($sql, [$userId, $totalAmount]);
            $orderId = $this->db->lastInsertId();
            
            // Получаем товары из корзины пользователя
            $cartModel = new Cart();
            $cartItems = $cartModel->getItems();
            
            // Добавляем товары в заказ
            foreach ($cartItems as $item) {
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES (?, ?, ?, ?)";
                $this->db->query($sql, [
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
            
            // Очищаем корзину пользователя
            $cartModel->clear();
            
            // Фиксируем транзакцию
            $this->db->commit();
            
            return $orderId;
        } catch (\Exception $e) {
            // В случае ошибки отменяем транзакцию
            $this->db->rollback();
            return false;
        }
    }
    
    // Обновить статус заказа
    public function updateStatus($orderId, $status) {
        $allowedStatuses = ['в обработке', 'оплачен', 'отправлен', 'доставлен', 'отменен'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        return $this->db->query($sql, [$status, $orderId])->rowCount() > 0;
    }
    
    // Получить элементы заказа
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name, p.brand, p.image_path
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        
        return $this->db->fetchAll($sql, [$orderId]);
    }
}
