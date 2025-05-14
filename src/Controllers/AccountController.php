<?php

namespace Controllers;

use Models\User;
use Models\Order;

class AccountController extends Controller {
    private $userModel;
    private $orderModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
    }
    
    // Главная страница личного кабинета
    public function index() {
        // Проверяем авторизацию
        $this->requireLogin();
        
        // Получаем данные пользователя
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        // Получаем последние заказы пользователя (3 последних)
        $recentOrders = array_slice($this->orderModel->getByUserId($_SESSION['user_id']), 0, 3);
        
        $this->render('account/index', [
            'title' => 'Личный кабинет',
            'user' => $user,
            'recentOrders' => $recentOrders
        ]);
    }
    
    // История заказов
    public function orders() {
        // Проверяем авторизацию
        $this->requireLogin();
        
        // Получаем все заказы пользователя
        $orders = $this->orderModel->getByUserId($_SESSION['user_id']);
        
        $this->render('account/orders', [
            'title' => 'История заказов',
            'orders' => $orders
        ]);
    }
    
    // Просмотр отдельного заказа
    public function viewOrder($id) {
        // Проверяем авторизацию
        $this->requireLogin();
        
        // Получаем заказ
        $order = $this->orderModel->getById($id);
        
        // Проверяем, принадлежит ли заказ текущему пользователю
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->render('errors/404', [
                'title' => 'Заказ не найден'
            ]);
            return;
        }
        
        // Получаем элементы заказа
        $orderItems = $this->orderModel->getOrderItems($id);
        
        $this->render('account/view-order', [
            'title' => 'Заказ №' . $id,
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
}
