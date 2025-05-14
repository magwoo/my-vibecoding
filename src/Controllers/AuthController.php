<?php

namespace Controllers;

use Models\User;
use Models\Cart;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Форма входа
    public function loginForm() {
        // Перенаправляем авторизованного пользователя на главную страницу
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        $this->render('auth/login', [
            'title' => 'Вход в аккаунт'
        ]);
    }
    
    // Обработка входа
    public function login() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Перенаправляем авторизованного пользователя на главную страницу
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Проверка наличия обязательных полей
        if (empty($email) || empty($password)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Все поля обязательны для заполнения'
            ];
            $this->redirect('/login');
        }
        
        // Проверка правильности учетных данных
        if ($this->userModel->verifyPassword($email, $password)) {
            $user = $this->userModel->getByEmail($email);
            
            // Сохраняем ID пользователя в сессии
            $_SESSION['user_id'] = $user['id'];
            
            // Перенос товаров из гостевой корзины в корзину пользователя
            $cartModel = new Cart();
            $cartModel->migrateGuestCart($user['id']);
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Вы успешно вошли в систему'
            ];
            
            // Перенаправление на страницу, с которой пользователь пришел, или на главную
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '/';
            unset($_SESSION['redirect_after_login']);
            
            $this->redirect($redirect);
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Неверный email или пароль'
            ];
            $this->redirect('/login');
        }
    }
    
    // Форма регистрации
    public function registerForm() {
        // Перенаправляем авторизованного пользователя на главную страницу
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        $this->render('auth/register', [
            'title' => 'Регистрация'
        ]);
    }
    
    // Обработка регистрации
    public function register() {
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Перенаправляем авторизованного пользователя на главную страницу
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        
        // Проверка наличия обязательных полей
        if (empty($email) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Все поля обязательны для заполнения'
            ];
            $this->redirect('/register');
        }
        
        // Проверка валидности email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Укажите корректный email'
            ];
            $this->redirect('/register');
        }
        
        // Проверка длины пароля
        if (strlen($password) < 8) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Пароль должен содержать минимум 8 символов'
            ];
            $this->redirect('/register');
        }
        
        // Проверка совпадения паролей
        if ($password !== $passwordConfirm) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Пароли не совпадают'
            ];
            $this->redirect('/register');
        }
        
        // Проверка уникальности email
        if ($this->userModel->getByEmail($email)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Пользователь с таким email уже существует'
            ];
            $this->redirect('/register');
        }
        
        // Создаем нового пользователя
        $userId = $this->userModel->create($email, $password);
        
        if ($userId) {
            // Сохраняем ID пользователя в сессии
            $_SESSION['user_id'] = $userId;
            
            // Перенос товаров из гостевой корзины в корзину пользователя
            $cartModel = new Cart();
            $cartModel->migrateGuestCart($userId);
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Вы успешно зарегистрировались'
            ];
            
            // Перенаправление на страницу, с которой пользователь пришел, или на главную
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '/';
            unset($_SESSION['redirect_after_login']);
            
            $this->redirect($redirect);
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Ошибка при регистрации. Пожалуйста, попробуйте позже.'
            ];
            $this->redirect('/register');
        }
    }
    
    // Выход из системы
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Удаляем ID пользователя из сессии
            unset($_SESSION['user_id']);
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Вы успешно вышли из системы'
            ];
        }
        
        $this->redirect('/');
    }
}
