<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Router;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    private $productModel;
    private $orderModel;
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }
    
    public function index()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        
        // Get counts for dashboard
        $totalProducts = $this->productModel->getProductCount();
        $totalOrders = $this->orderModel->getOrderCount();
        $userCount = $this->userModel->getUserCount();
        $pendingOrders = $this->orderModel->getOrderCount('pending');
        
        // Get recent orders
        $recentOrders = $this->orderModel->getAllOrders(null, 1, 5);
        
        $this->view('admin/dashboard', [
            'user' => $user,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'userCount' => $userCount,
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders
        ]);
    }
    
    public function products()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        
        // Get products with pagination
        $products = $this->productModel->getProducts([], 'created_at DESC', $page, $perPage);
        
        // Get total count for pagination
        $totalProducts = $this->productModel->getProductCount();
        $totalPages = ceil($totalProducts / $perPage);
        
        $this->view('admin/products', [
            'user' => $user,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function addProduct()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        $errors = [];
        $success = null;
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
            $brand = trim($_POST['brand'] ?? '');
            $os = trim($_POST['os'] ?? '');
            $storage = trim($_POST['storage'] ?? '');
            $screenSize = isset($_POST['screen_size']) ? (float)$_POST['screen_size'] : 0;
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            // Validate input
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'brand' => 'required',
                'os' => 'required',
                'storage' => 'required',
                'screen_size' => 'required|numeric'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            // Handle image upload
            $image = 'default.jpg'; // Default image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_DIR . '/uploads/products/';
                
                // Create upload directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('product_') . '.' . $fileExt;
                $uploadFile = $uploadDir . $fileName;
                
                // Check if it's a valid image
                $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $validTypes)) {
                    $errors['image'] = 'Invalid image format. Only JPEG, PNG, and GIF are allowed.';
                } else if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $image = $fileName;
                } else {
                    $errors['image'] = 'Failed to upload image.';
                }
            }
            
            if (empty($errors)) {
                // Create product
                $productId = $this->productModel->create([
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'brand' => $brand,
                    'os' => $os,
                    'storage' => $storage,
                    'screen_size' => $screenSize,
                    'image' => $image,
                    'featured' => $featured,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($productId) {
                    $success = 'Product added successfully';
                    // Redirect to products page
                    Router::redirect('admin/products');
                    return;
                } else {
                    $errors['product'] = 'Failed to add product';
                }
            }
        }
        
        $this->view('admin/add_product', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function editProduct($id = null)
    {
        // Require admin privileges
        $this->requireAdmin();
        
        // Get product ID from query string if not provided
        if ($id === null) {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        $user = $this->getCurrentUser();
        
        // Get product
        $product = $this->productModel->find($id);
        
        if (!$product) {
            header('HTTP/1.0 404 Not Found');
            include PUBLIC_DIR . '/errors/404.php';
            return;
        }
        
        $errors = [];
        $success = null;
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
            $brand = trim($_POST['brand'] ?? '');
            $os = trim($_POST['os'] ?? '');
            $storage = trim($_POST['storage'] ?? '');
            $screenSize = isset($_POST['screen_size']) ? (float)$_POST['screen_size'] : 0;
            $featured = isset($_POST['featured']) ? 1 : 0;
            
            // Validate input
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'brand' => 'required',
                'os' => 'required',
                'storage' => 'required',
                'screen_size' => 'required|numeric'
            ];
            
            $errors = $this->validateInput($_POST, $rules);
            
            // Handle image upload
            $image = $product['image']; // Keep existing image by default
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_DIR . '/uploads/products/';
                
                // Create upload directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('product_') . '.' . $fileExt;
                $uploadFile = $uploadDir . $fileName;
                
                // Check if it's a valid image
                $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $validTypes)) {
                    $errors['image'] = 'Invalid image format. Only JPEG, PNG, and GIF are allowed.';
                } else if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $image = $fileName;
                    
                    // Delete old image if it's not the default
                    if ($product['image'] !== 'default.jpg' && file_exists($uploadDir . $product['image'])) {
                        unlink($uploadDir . $product['image']);
                    }
                } else {
                    $errors['image'] = 'Failed to upload image.';
                }
            }
            
            if (empty($errors)) {
                // Update product
                $updated = $this->productModel->update($id, [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'brand' => $brand,
                    'os' => $os,
                    'storage' => $storage,
                    'screen_size' => $screenSize,
                    'image' => $image,
                    'featured' => $featured,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($updated) {
                    $success = 'Product updated successfully';
                    
                    // Refresh product data
                    $product = $this->productModel->find($id);
                } else {
                    $errors['product'] = 'Failed to update product';
                }
            }
        }
        
        $this->view('admin/edit_product', [
            'user' => $user,
            'product' => $product,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function deleteProduct()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('admin/products');
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        // Get product
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            Router::redirect('admin/products');
            return;
        }
        
        // Delete product
        $deleted = $this->productModel->delete($id);
        
        if ($deleted) {
            // Delete product image if it's not the default
            if ($product['image'] !== 'default.jpg') {
                $imagePath = PUBLIC_DIR . '/uploads/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['success'] = 'Product deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete product';
        }
        
        Router::redirect('admin/products');
    }
    
    public function orders()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        $user = $this->getCurrentUser();
        
        // Get filter parameters
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        
        // Get orders
        $orders = $this->orderModel->getAllOrders($status, $page, $perPage);
        
        // Get total count for pagination
        $totalOrders = $this->orderModel->getOrderCount($status);
        $totalPages = ceil($totalOrders / $perPage);
        
        $this->view('admin/orders', [
            'user' => $user,
            'orders' => $orders,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function showOrder($id = null)
    {
        // Require admin privileges
        $this->requireAdmin();
        
        // Get order ID from query string if not provided
        if ($id === null) {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        $user = $this->getCurrentUser();
        
        // Get order
        $order = $this->orderModel->find($id);
        
        if (!$order) {
            header('HTTP/1.0 404 Not Found');
            include PUBLIC_DIR . '/errors/404.php';
            return;
        }
        
        // Get order items
        $orderItems = $this->orderModel->getOrderItems($order['id']);
        
        // Get customer details
        $customer = $this->userModel->find($order['user_id']);
        
        $this->view('admin/order_details', [
            'user' => $user,
            'order' => $order,
            'orderItems' => $orderItems,
            'customer' => $customer
        ]);
    }
    
    public function updateOrderStatus()
    {
        // Require admin privileges
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('admin/orders');
            return;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // Validate status
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $_SESSION['error'] = 'Invalid order status';
            Router::redirect('admin/order?id=' . $id);
            return;
        }
        
        // Update order status
        $updated = $this->orderModel->updateOrderStatus($id, $status);
        
        if ($updated) {
            $_SESSION['success'] = 'Order status updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update order status';
        }
        
        Router::redirect('admin/order?id=' . $id);
    }
} 