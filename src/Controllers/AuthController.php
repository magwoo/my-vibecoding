<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Router;
use App\Models\User;
use App\Models\Cart;

class AuthController extends Controller
{
    private $userModel;
    private $cartModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->cartModel = new Cart();
    }
    
    public function login()
    {
        // If user is already logged in, redirect to home
        if ($this->isLoggedIn()) {
            Router::redirect('');
            return;
        }
        
        $errors = [];
        
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validate input
            $rules = [
                'email' => 'required|email',
                'password' => 'required'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                // Check if user exists
                $user = $this->userModel->findByEmail($email);
                
                if (!$user || !$this->userModel->verifyPassword($user, $password)) {
                    $errors['login'] = 'Invalid email or password';
                } else {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Initialize cart for the logged-in user
                    $this->cartModel->initCart($user['id']);
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        Router::redirect('admin');
                    } else {
                        Router::redirect('');
                    }
                    
                    return;
                }
            }
        }
        
        // Display login form
        $this->view('auth/login', [
            'errors' => $errors,
            'email' => $email ?? '',
        ]);
    }
    
    public function register()
    {
        // If user is already logged in, redirect to home
        if ($this->isLoggedIn()) {
            Router::redirect('');
            return;
        }
        
        $errors = [];
        
        // Handle registration form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate input
            $rules = [
                'email' => 'required|email',
                'password' => 'required|min:8',
                'confirm_password' => 'required'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            // Additional validation
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Password confirmation does not match';
            }
            
            // Check if email already exists
            if (empty($errors['email']) && $this->userModel->findByEmail($email)) {
                $errors['email'] = 'Email is already taken';
            }
            
            if (empty($errors)) {
                // Register user
                $userId = $this->userModel->register($email, $password);
                
                if ($userId) {
                    // Login user
                    $user = $this->userModel->find($userId);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Initialize cart for the new user
                    $this->cartModel->initCart($user['id']);
                    
                    // Redirect to home
                    Router::redirect('');
                    return;
                } else {
                    $errors['register'] = 'Registration failed';
                }
            }
        }
        
        // Display registration form
        $this->view('auth/register', [
            'errors' => $errors,
            'email' => $email ?? '',
        ]);
    }
    
    public function logout()
    {
        // Clear session
        session_unset();
        session_destroy();
        
        // Start a new session for the guest
        session_start();
        
        // Redirect to home
        Router::redirect('');
    }
} 