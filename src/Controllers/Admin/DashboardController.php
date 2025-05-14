<?php

namespace Controllers\Admin;

use Controllers\Controller;
use Models\Order;
use Models\Product;

class DashboardController extends Controller {
    // Главная страница админ-панели
    public function index() {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Получаем статистику для дашборда
        $orderModel = new Order();
        $productModel = new Product();
        
        // Количество заказов по статусам
        $ordersByStatus = [
            'в обработке' => $orderModel->countAll(['status' => 'в обработке']),
            'оплачен' => $orderModel->countAll(['status' => 'оплачен']),
            'отправлен' => $orderModel->countAll(['status' => 'отправлен']),
            'доставлен' => $orderModel->countAll(['status' => 'доставлен']),
            'отменен' => $orderModel->countAll(['status' => 'отменен'])
        ];
        
        // Общее количество заказов
        $totalOrders = array_sum($ordersByStatus);
        
        // Общее количество товаров
        $totalProducts = $productModel->countAll();
        
        // Последние 5 заказов
        $recentOrders = array_slice($orderModel->getAll(5), 0, 5);
        
        $this->renderAdmin('dashboard', [
            'title' => 'Панель управления',
            'ordersByStatus' => $ordersByStatus,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'recentOrders' => $recentOrders
        ]);
    }
}
