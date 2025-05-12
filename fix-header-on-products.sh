#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create a fix for the header.php file to handle potential missing Cart model
cat > tmp/header.php << 'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - " : "" ?>Phone Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#10b981",
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        /* Custom CSS for dropdown menu */
        .user-dropdown {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .user-dropdown.show {
            display: block;
            opacity: 1;
        }
        /* Add padding to create a larger hover area */
        .user-menu-trigger {
            position: relative;
            padding: 10px;
            margin: -10px;
        }
        /* Extend the hover area to the dropdown */
        .dropdown-container {
            position: relative;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-stone-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-2">
            <div class="flex justify-between items-center">
                <!-- Logo and navigation -->
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-2xl font-bold">Phone Store</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="/" class="hover:text-stone-300 transition">Home</a>
                        <a href="/products" class="hover:text-stone-300 transition">Products</a>
                        <?php if (isset($user) && $user["role"] === "admin"): ?>
                            <a href="/admin" class="hover:text-stone-300 transition">Admin</a>
                        <?php endif; ?>
                    </nav>
                </div>
                
                <!-- User actions -->
                <div class="flex items-center space-x-4">
                    <!-- Search button -->
                    <div class="relative hidden md:block">
                        <form action="/products/search" method="GET" class="flex">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search products..."
                                class="px-3 py-1 w-64 rounded-l-md border-0 focus:ring-0 text-gray-800"
                            >
                            <button type="submit" class="bg-stone-600 text-white px-4 rounded-r-md hover:bg-stone-700 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Cart -->
                    <a href="/cart" class="text-white hover:text-stone-300 transition">
                        <span class="relative">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <?php
                            // Get cart count - with improved error handling
                            if (isset($user) && isset($_SESSION["user_id"])) {
                                try {
                                    if (!class_exists('\App\Models\Cart')) {
                                        // Make sure Cart model is included if autoload failed
                                        require_once SRC_DIR . '/Models/Cart.php';
                                    }
                                    $cartModel = new \App\Models\Cart();
                                    $cart = $cartModel->getCartByUserId($user["id"]);
                                    if ($cart) {
                                        $cartCount = $cartModel->getCartItemCount($cart["id"]);
                                        if ($cartCount > 0) {
                                            echo "<span class=\"absolute -top-2 -right-2 bg-stone-500 text-white rounded-full text-xs px-1.5\">". $cartCount ."</span>";
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Silently handle any errors with cart
                                    error_log("Error loading cart: " . $e->getMessage());
                                }
                            }
                            ?>
                        </span>
                    </a>
                    
                    <!-- User account -->
                    <?php if (isset($user)): ?>
                        <div class="dropdown-container">
                            <button class="flex items-center space-x-1 user-menu-trigger" id="user-menu-button">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="hidden md:inline"><?= $user["email"] ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 user-dropdown" id="user-dropdown">
                                <a href="/account" class="block px-4 py-2 text-sm text-gray-700 hover:bg-stone-100">Account</a>
                                <a href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-stone-100">My Orders</a>
                                <div class="border-t border-gray-100"></div>
                                <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-stone-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="space-x-2">
                            <a href="/login" class="text-white hover:text-stone-300 transition">Login</a>
                            <span class="text-stone-500">|</span>
                            <a href="/register" class="text-white hover:text-stone-300 transition">Register</a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mobile menu button -->
                    <button class="md:hidden text-white focus:outline-none" id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="mt-3 space-y-1 pb-3 pt-2">
                    <form action="/products/search" method="GET" class="flex mb-4">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search products..."
                            class="px-3 py-1 w-full rounded-l-md border-0 focus:ring-0 text-gray-800"
                        >
                        <button type="submit" class="bg-stone-600 text-white px-4 rounded-r-md hover:bg-stone-700 transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="/" class="block px-3 py-2 text-white hover:bg-stone-700 rounded-md">Home</a>
                    <a href="/products" class="block px-3 py-2 text-white hover:bg-stone-700 rounded-md">Products</a>
                    <?php if (isset($user) && $user["role"] === "admin"): ?>
                        <a href="/admin" class="block px-3 py-2 text-white hover:bg-stone-700 rounded-md">Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Flash messages -->
    <?php if (isset($_SESSION["success"])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4 mx-auto max-w-7xl" role="alert">
            <span class="block sm:inline"><?= $_SESSION["success"] ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION["error"])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4 mx-auto max-w-7xl" role="alert">
            <span class="block sm:inline"><?= $_SESSION["error"] ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>
    
    <main class="flex-grow container mx-auto px-4 py-6">
EOF

# Also create a fix for the controller to ensure user data is passed to the view
cat > tmp/ProductController.php << 'EOF'
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    private $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function index()
    {
        // Get filter parameters
        $filters = [];
        
        if (!empty($_GET['brand'])) {
            $filters['brand'] = $_GET['brand'];
        }
        
        if (!empty($_GET['min_price'])) {
            $filters['min_price'] = (float)$_GET['min_price'];
        }
        
        if (!empty($_GET['max_price'])) {
            $filters['max_price'] = (float)$_GET['max_price'];
        }
        
        if (!empty($_GET['screen_size'])) {
            $filters['screen_size'] = $_GET['screen_size'];
        }
        
        if (!empty($_GET['storage'])) {
            $filters['storage'] = $_GET['storage'];
        }
        
        if (!empty($_GET['os'])) {
            $filters['os'] = $_GET['os'];
        }
        
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12; // Products per page
        
        // Get sorting parameter
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';
        
        // Get products
        $products = $this->productModel->getProducts($filters, $sort, $page, $perPage);
        
        // Get total product count for pagination
        $totalProducts = $this->productModel->getProductCount($filters);
        $totalPages = ceil($totalProducts / $perPage);
        
        // Get filter options for sidebar
        $brands = $this->productModel->getAllBrands();
        $screenSizes = $this->productModel->getAllScreenSizes();
        $storageOptions = $this->productModel->getAllStorageOptions();
        $osOptions = $this->productModel->getAllOperatingSystems();
        
        // Get the current user
        $user = $this->getCurrentUser();
        
        // Make sure the Cart model is loaded
        if (!class_exists('\App\Models\Cart') && file_exists(SRC_DIR . '/Models/Cart.php')) {
            require_once SRC_DIR . '/Models/Cart.php';
        }
        
        // Render the products view
        $this->view('products/index', [
            'title' => 'Products',
            'products' => $products,
            'totalProducts' => $totalProducts,
            'filters' => $filters,
            'sort' => $sort,
            'brands' => $brands,
            'screenSizes' => $screenSizes,
            'storageOptions' => $storageOptions,
            'osOptions' => $osOptions,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'user' => $user
        ]);
    }
    
    public function show($id = null)
    {
        // Get product ID from URL if not provided as parameter
        if ($id === null) {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        // Get product details
        $product = $this->productModel->find($id);
        
        if (!$product) {
            // Product not found, show 404 page
            header('HTTP/1.0 404 Not Found');
            include PUBLIC_DIR . '/errors/404.php';
            return;
        }
        
        // Get related products (same brand)
        $relatedProducts = $this->productModel->getProducts(['brand' => $product['brand']], 'created_at DESC', 1, 4);
        
        // Get the current user
        $user = $this->getCurrentUser();
        
        // Make sure the Cart model is loaded
        if (!class_exists('\App\Models\Cart') && file_exists(SRC_DIR . '/Models/Cart.php')) {
            require_once SRC_DIR . '/Models/Cart.php';
        }
        
        // Render the product details page
        $this->view('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'user' => $user
        ]);
    }
    
    public function search()
    {
        $query = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        if (empty($query)) {
            // Redirect to products page if no search query
            header('Location: /products');
            exit;
        }
        
        // Search products by name or description
        $filters = ['search' => $query];
        $products = $this->productModel->getProducts($filters);
        
        // Get the current user
        $user = $this->getCurrentUser();
        
        // Make sure the Cart model is loaded
        if (!class_exists('\App\Models\Cart') && file_exists(SRC_DIR . '/Models/Cart.php')) {
            require_once SRC_DIR . '/Models/Cart.php';
        }
        
        // Add search results to title and render products page
        $this->view('products/index', [
            'title' => 'Search: ' . $query,
            'products' => $products,
            'filters' => $filters,
            'brands' => $this->productModel->getAllBrands(),
            'screenSizes' => $this->productModel->getAllScreenSizes(),
            'storageOptions' => $this->productModel->getAllStorageOptions(),
            'osOptions' => $this->productModel->getAllOperatingSystems(),
            'totalProducts' => count($products),
            'currentPage' => 1,
            'totalPages' => 1,
            'sort' => 'created_at DESC',
            'user' => $user
        ]);
    }
}
EOF

# Copy the updated files to the container
docker cp tmp/header.php phone-store-php-1:/var/www/html/src/Views/layouts/header.php
docker cp tmp/ProductController.php phone-store-php-1:/var/www/html/src/Controllers/ProductController.php

# Restart the container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Header fix for products pages applied successfully." 