<?php

namespace Models;

use Utils\Database;

class Cart {
    private $db;
    private $userId = null;
    private $sessionId = null;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Определяем, авторизован ли пользователь
        if (isset($_SESSION['user_id'])) {
            $this->userId = $_SESSION['user_id'];
        } else {
            // Для гостей используем ID сессии
            if (!isset($_SESSION['guest_cart_id'])) {
                $_SESSION['guest_cart_id'] = session_id();
            }
            $this->sessionId = $_SESSION['guest_cart_id'];
        }
    }
    
    // Добавление товара в корзину
    public function add($productId, $quantity = 1) {
        // Проверяем существование товара
        $productModel = new Product();
        $product = $productModel->getById($productId);
        
        if (!$product) {
            return false;
        }
        
        // Если пользователь авторизован
        if ($this->userId) {
            // Проверяем, есть ли уже такой товар в корзине
            $sql = "SELECT quantity FROM user_cart WHERE user_id = ? AND product_id = ?";
            $currentItem = $this->db->fetch($sql, [$this->userId, $productId]);
            
            if ($currentItem) {
                // Увеличиваем количество
                $newQuantity = $currentItem['quantity'] + $quantity;
                $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $this->db->query($sql, [$newQuantity, $this->userId, $productId]);
            } else {
                // Добавляем новый товар
                $sql = "INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $this->db->query($sql, [$this->userId, $productId, $quantity]);
            }
        } else {
            // Для гостя
            // Проверяем, есть ли уже такой товар в корзине
            $sql = "SELECT quantity FROM guest_cart WHERE session_id = ? AND product_id = ?";
            $currentItem = $this->db->fetch($sql, [$this->sessionId, $productId]);
            
            if ($currentItem) {
                // Увеличиваем количество
                $newQuantity = $currentItem['quantity'] + $quantity;
                $sql = "UPDATE guest_cart SET quantity = ? WHERE session_id = ? AND product_id = ?";
                $this->db->query($sql, [$newQuantity, $this->sessionId, $productId]);
            } else {
                // Добавляем новый товар
                $sql = "INSERT INTO guest_cart (session_id, product_id, quantity) VALUES (?, ?, ?)";
                $this->db->query($sql, [$this->sessionId, $productId, $quantity]);
            }
        }
        
        return true;
    }
    
    // Обновление количества товара в корзине
    public function update($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->remove($productId);
        }
        
        if ($this->userId) {
            $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            return $this->db->query($sql, [$quantity, $this->userId, $productId])->rowCount() > 0;
        } else {
            $sql = "UPDATE guest_cart SET quantity = ? WHERE session_id = ? AND product_id = ?";
            return $this->db->query($sql, [$quantity, $this->sessionId, $productId])->rowCount() > 0;
        }
    }
    
    // Удаление товара из корзины
    public function remove($productId) {
        if ($this->userId) {
            $sql = "DELETE FROM user_cart WHERE user_id = ? AND product_id = ?";
            return $this->db->query($sql, [$this->userId, $productId])->rowCount() > 0;
        } else {
            $sql = "DELETE FROM guest_cart WHERE session_id = ? AND product_id = ?";
            return $this->db->query($sql, [$this->sessionId, $productId])->rowCount() > 0;
        }
    }
    
    // Очистка корзины
    public function clear() {
        if ($this->userId) {
            $sql = "DELETE FROM user_cart WHERE user_id = ?";
            $this->db->query($sql, [$this->userId]);
        } else {
            $sql = "DELETE FROM guest_cart WHERE session_id = ?";
            $this->db->query($sql, [$this->sessionId]);
        }
        return true;
    }
    
    // Получение содержимого корзины
    public function getItems() {
        $productModel = new Product();
        
        if ($this->userId) {
            $sql = "SELECT uc.product_id, uc.quantity, p.name, p.price, p.image_path, p.brand 
                    FROM user_cart uc 
                    JOIN products p ON uc.product_id = p.id 
                    WHERE uc.user_id = ?";
            $items = $this->db->fetchAll($sql, [$this->userId]);
        } else {
            $sql = "SELECT gc.product_id, gc.quantity, p.name, p.price, p.image_path, p.brand 
                    FROM guest_cart gc 
                    JOIN products p ON gc.product_id = p.id 
                    WHERE gc.session_id = ?";
            $items = $this->db->fetchAll($sql, [$this->sessionId]);
        }
        
        // Рассчитываем итоговую стоимость для каждого товара
        foreach ($items as &$item) {
            $item['total'] = $item['price'] * $item['quantity'];
        }
        
        return $items;
    }
    
    // Получение общей стоимости корзины
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['total'];
        }
        
        return $total;
    }
    
    // Получение количества товаров в корзине
    public function getCount() {
        if ($this->userId) {
            $sql = "SELECT COUNT(*) as count FROM user_cart WHERE user_id = ?";
            $result = $this->db->fetch($sql, [$this->userId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM guest_cart WHERE session_id = ?";
            $result = $this->db->fetch($sql, [$this->sessionId]);
        }
        
        return $result['count'];
    }
    
    // Перенос товаров из гостевой корзины в корзину пользователя при авторизации
    public function migrateGuestCart($userId) {
        // Получаем все товары из гостевой корзины
        $sql = "SELECT product_id, quantity FROM guest_cart WHERE session_id = ?";
        $guestItems = $this->db->fetchAll($sql, [$this->sessionId]);
        
        if (empty($guestItems)) {
            return true;
        }
        
        // Получаем товары из корзины пользователя
        $sql = "SELECT product_id, quantity FROM user_cart WHERE user_id = ?";
        $userItems = $this->db->fetchAll($sql, [$userId]);
        
        // Преобразуем в ассоциативный массив для удобства
        $userItemsMap = [];
        foreach ($userItems as $item) {
            $userItemsMap[$item['product_id']] = $item['quantity'];
        }
        
        // Начинаем транзакцию
        $this->db->beginTransaction();
        
        try {
            // Обрабатываем каждый товар из гостевой корзины
            foreach ($guestItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                
                if (isset($userItemsMap[$productId])) {
                    // Обновляем существующий товар в корзине пользователя
                    $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $this->db->query($sql, [$userItemsMap[$productId] + $quantity, $userId, $productId]);
                } else {
                    // Добавляем новый товар в корзину пользователя
                    $sql = "INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                    $this->db->query($sql, [$userId, $productId, $quantity]);
                }
            }
            
            // Очищаем гостевую корзину
            $sql = "DELETE FROM guest_cart WHERE session_id = ?";
            $this->db->query($sql, [$this->sessionId]);
            
            // Фиксируем транзакцию
            $this->db->commit();
            
            // Обновляем ID пользователя
            $this->userId = $userId;
            $this->sessionId = null;
            
            return true;
        } catch (\Exception $e) {
            // В случае ошибки отменяем транзакцию
            $this->db->rollback();
            return false;
        }
    }
}
