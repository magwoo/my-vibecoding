<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Router;
use App\Models\Order;
use App\Models\Cart;
use App\Models\User;

class OrderController extends Controller
{
    private $orderModel;
    private $cartModel;
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->userModel = new User();
    }
    
    public function checkout()
    {
        // Require user to be logged in
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        $cart = $this->cartModel->getCartByUserId($user['id']);
        
        if (!$cart) {
            $_SESSION['error'] = 'Your cart is empty';
            Router::redirect('cart');
            return;
        }
        
        $cartItems = $this->cartModel->getCartItems($cart['id']);
        
        if (empty($cartItems)) {
            $_SESSION['error'] = 'Your cart is empty';
            Router::redirect('cart');
            return;
        }
        
        $cartTotal = $this->cartModel->getCartTotal($cart['id']);
        
        // Handle checkout form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate input
            $shippingAddress = trim($_POST['shipping_address'] ?? '');
            $paymentMethod = trim($_POST['payment_method'] ?? '');
            
            $rules = [
                'shipping_address' => 'required',
                'payment_method' => 'required'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            if (empty($errors)) {
                try {
                    // Create order data
                    $orderData = [
                        'total' => $cartTotal,
                        'shipping_address' => $shippingAddress,
                        'payment_method' => $paymentMethod
                    ];

                    // Create order
                    $orderId = $this->orderModel->createOrder(
                        $user['id'],
                        $cart['id'],
                        $orderData
                    );
                    
                    // Redirect to order confirmation
                    $_SESSION['success'] = 'Your order has been placed successfully!';
                    Router::redirect('orders');
                    return;
                } catch (\Exception $e) {
                    $errors['order'] = 'An error occurred while processing your order. Please try again.';
                }
            }
            
            // If there are errors, redisplay the checkout form with errors
            $this->view('checkout/index', [
                'user' => $user,
                'cartItems' => $cartItems,
                'cartTotal' => $cartTotal,
                'errors' => $errors,
                'shipping_address' => $shippingAddress,
                'payment_method' => $paymentMethod
            ]);
            return;
        }
        
        // Display checkout form
        $this->view('checkout/index', [
            'user' => $user,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }
    
    public function orderHistory()
    {
        // Require user to be logged in
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Get page number
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        
        // Get user's orders
        $orders = $this->orderModel->getUserOrders($user['id'], $page, $perPage);
        
        // Get order count for pagination
        $totalOrders = $this->orderModel->getUserOrderCount($user['id']);
        $totalPages = ceil($totalOrders / $perPage);
        
        $this->view('orders/history', [
            'user' => $user,
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function orderDetails($id = null)
    {
        // Require user to be logged in
        $this->requireLogin();
        
        // Get order ID from query string if not provided
        if ($id === null) {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        $user = $this->getCurrentUser();
        
        // Get order details
        $order = $this->orderModel->find($id);
        
        // Check if order exists and belongs to the user
        if (!$order || $order['user_id'] != $user['id']) {
            header('HTTP/1.0 404 Not Found');
            include PUBLIC_DIR . '/errors/404.php';
            return;
        }
        
        // Get order items
        $orderItems = $this->orderModel->getOrderItems($order['id']);
        
        $this->view('orders/details', [
            'user' => $user,
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
} 