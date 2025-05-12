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
