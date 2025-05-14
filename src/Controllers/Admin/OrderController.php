<?php

namespace Controllers\Admin;

use Controllers\Controller;
use Models\Order;

class OrderController extends Controller {
    private $orderModel;
    
    public function __construct() {
        $this->orderModel = new Order();
    }
    
    // Список всех заказов
    public function index() {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Параметры пагинации
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Заказов на странице
        $offset = ($page - 1) * $limit;
        
        // Параметры фильтрации
        $filters = [];
        
        // Фильтр по статусу
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        // Фильтр по дате (от)
        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        
        // Фильтр по дате (до)
        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        
        // Получаем заказы с учетом пагинации и фильтров
        $orders = $this->orderModel->getAll($limit, $offset, $filters);
        
        // Получаем общее количество заказов для пагинации
        $totalOrders = $this->orderModel->countAll($filters);
        $totalPages = ceil($totalOrders / $limit);
        
        $this->renderAdmin('orders/index', [
            'title' => 'Управление заказами',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'filters' => $filters
        ]);
    }
    
    // Просмотр отдельного заказа
    public function view($id) {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Получаем заказ
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->renderAdmin('errors/404', [
                'title' => 'Заказ не найден'
            ]);
            return;
        }
        
        // Получаем элементы заказа
        $orderItems = $this->orderModel->getOrderItems($id);
        
        $this->renderAdmin('orders/view', [
            'title' => 'Заказ №' . $id,
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
    
    // Обновление статуса заказа
    public function updateStatus($id) {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Получаем заказ
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->json(['success' => false, 'message' => 'Заказ не найден'], 404);
        }
        
        // Получаем новый статус из запроса
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // Проверяем корректность статуса
        $allowedStatuses = ['в обработке', 'оплачен', 'отправлен', 'доставлен', 'отменен'];
        
        if (empty($status) || !in_array($status, $allowedStatuses)) {
            $this->json(['success' => false, 'message' => 'Некорректный статус заказа'], 400);
        }
        
        // Обновляем статус заказа
        $result = $this->orderModel->updateStatus($id, $status);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Статус заказа успешно обновлен']);
        } else {
            $this->json(['success' => false, 'message' => 'Ошибка при обновлении статуса заказа'], 500);
        }
    }
}
