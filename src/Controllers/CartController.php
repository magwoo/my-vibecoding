<?php

namespace Controllers;

use Models\Cart;

class CartController extends Controller {
    private $cartModel;
    
    public function __construct() {
        $this->cartModel = new Cart();
    }
    
    // Отображение корзины
    public function view() {
        $items = $this->cartModel->getItems();
        $total = $this->cartModel->getTotal();
        
        $this->render('cart/view', [
            'title' => 'Корзина',
            'items' => $items,
            'total' => $total
        ]);
    }
    
    // Добавление товара в корзину (AJAX)
    public function add() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($productId <= 0 || $quantity <= 0) {
            $this->json(['success' => false, 'message' => 'Некорректные данные'], 400);
        }
        
        $result = $this->cartModel->add($productId, $quantity);
        
        if ($result) {
            $cartCount = $this->cartModel->getCount();
            $this->json(['success' => true, 'message' => 'Товар добавлен в корзину', 'cart_count' => $cartCount]);
        } else {
            $this->json(['success' => false, 'message' => 'Не удалось добавить товар в корзину'], 500);
        }
    }
    
    // Обновление количества товара в корзине (AJAX)
    public function update() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Некорректные данные'], 400);
        }
        
        $result = $this->cartModel->update($productId, $quantity);
        
        if ($result) {
            // Получаем обновленные данные о корзине
            $items = $this->cartModel->getItems();
            $total = $this->cartModel->getTotal();
            $cartCount = $this->cartModel->getCount();
            
            $this->json([
                'success' => true, 
                'message' => 'Корзина обновлена', 
                'cart_count' => $cartCount,
                'total' => $total,
                'items' => $items
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Не удалось обновить корзину'], 500);
        }
    }
    
    // Удаление товара из корзины (AJAX)
    public function remove() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Некорректные данные'], 400);
        }
        
        $result = $this->cartModel->remove($productId);
        
        if ($result) {
            // Получаем обновленные данные о корзине
            $items = $this->cartModel->getItems();
            $total = $this->cartModel->getTotal();
            $cartCount = $this->cartModel->getCount();
            
            $this->json([
                'success' => true, 
                'message' => 'Товар удален из корзины', 
                'cart_count' => $cartCount,
                'total' => $total,
                'items' => $items
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Не удалось удалить товар из корзины'], 500);
        }
    }
}
