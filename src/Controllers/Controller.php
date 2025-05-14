<?php

namespace Controllers;

class Controller {
    protected function render($view, $data = []) {
        // Извлекаем переменные из массива данных
        extract($data);
        
        // Определяем путь к файлу представления
        $viewPath = ROOT_PATH . '/src/Views/' . $view . '.php';
        
        // Начинаем буферизацию вывода
        ob_start();
        
        // Подключаем шаблон header
        include ROOT_PATH . '/src/Views/layouts/header.php';
        
        // Проверяем существование файла представления
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Ошибка: Представление '$view' не найдено.";
        }
        
        // Подключаем шаблон footer
        include ROOT_PATH . '/src/Views/layouts/footer.php';
        
        // Получаем содержимое буфера и очищаем его
        $content = ob_get_clean();
        
        // Выводим содержимое
        echo $content;
    }
    
    protected function renderAdmin($view, $data = []) {
        // Извлекаем переменные из массива данных
        extract($data);
        
        // Определяем путь к файлу представления
        $viewPath = ROOT_PATH . '/src/Views/admin/' . $view . '.php';
        
        // Начинаем буферизацию вывода
        ob_start();
        
        // Подключаем шаблон admin header
        include ROOT_PATH . '/src/Views/admin/layouts/header.php';
        
        // Проверяем существование файла представления
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Ошибка: Представление '$view' не найдено.";
        }
        
        // Подключаем шаблон admin footer
        include ROOT_PATH . '/src/Views/admin/layouts/footer.php';
        
        // Получаем содержимое буфера и очищаем его
        $content = ob_get_clean();
        
        // Выводим содержимое
        echo $content;
    }
    
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function isAdmin() {
        return isset($_SESSION['admin_id']);
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Для доступа к этой странице необходимо войти в систему.'
            ];
            $this->redirect('/login');
        }
    }
    
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Доступ запрещен. Необходимы права администратора.'
            ];
            $this->redirect('/admin/login');
        }
    }
    
    protected function validateCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';
        
        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo "Ошибка CSRF. Обновите страницу и попробуйте снова.";
            exit;
        }
    }
}
