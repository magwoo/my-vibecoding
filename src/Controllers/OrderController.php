<?php

namespace Controllers;

use Models\Cart;
use Models\Order;

class OrderController extends Controller {
    // Обработка оформления заказа
    public function checkout() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Проверяем авторизацию
        if (!$this->isLoggedIn()) {
            // Сохраняем URL для перенаправления после авторизации
            $_SESSION['redirect_after_login'] = '/cart';
            
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Для оформления заказа необходимо войти в систему'
            ];
            
            $this->redirect('/login');
        }
        
        // Получаем данные корзины
        $cartModel = new Cart();
        $items = $cartModel->getItems();
        $totalAmount = $cartModel->getTotal();
        
        // Проверяем, не пуста ли корзина
        if (empty($items)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Ваша корзина пуста'
            ];
            
            $this->redirect('/cart');
        }
        
        // Создаем заказ
        $orderModel = new Order();
        $orderId = $orderModel->create($_SESSION['user_id'], $totalAmount);
        
        if ($orderId) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Заказ успешно оформлен'
            ];
            
            $this->redirect('/account/order/' . $orderId);
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Ошибка при оформлении заказа. Пожалуйста, попробуйте позже.'
            ];
            
            $this->redirect('/cart');
        }
    }
}
