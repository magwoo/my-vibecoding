<?php

namespace Controllers\Admin;

use Controllers\Controller;
use Models\Admin;

class AuthController extends Controller {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
    }
    
    // Форма входа для администраторов
    public function loginForm() {
        // Перенаправляем авторизованного администратора на главную страницу админ-панели
        if ($this->isAdmin()) {
            $this->redirect('/admin');
        }
        
        $this->renderAdmin('auth/login', [
            'title' => 'Вход в панель администратора'
        ]);
    }
    
    // Обработка входа администратора
    public function login() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Перенаправляем авторизованного администратора на главную страницу админ-панели
        if ($this->isAdmin()) {
            $this->redirect('/admin');
        }
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Проверка наличия обязательных полей
        if (empty($email) || empty($password)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Все поля обязательны для заполнения'
            ];
            $this->redirect('/admin/login');
        }
        
        // Проверка правильности учетных данных
        if ($this->adminModel->verifyPassword($email, $password)) {
            $admin = $this->adminModel->getByEmail($email);
            
            // Сохраняем ID администратора в сессии
            $_SESSION['admin_id'] = $admin['id'];
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Вы успешно вошли в систему'
            ];
            
            $this->redirect('/admin');
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Неверный email или пароль'
            ];
            $this->redirect('/admin/login');
        }
    }
    
    // Выход из системы
    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            // Удаляем ID администратора из сессии
            unset($_SESSION['admin_id']);
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Вы успешно вышли из системы'
            ];
        }
        
        $this->redirect('/admin/login');
    }
}
