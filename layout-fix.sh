#!/bin/bash

# Create temporary directory
mkdir -p tmp/layouts

# Create header.php
echo '<!DOCTYPE html>
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
                            // Get cart count
                            if (isset($user) && isset($_SESSION["user_id"])) {
                                try {
                                    $cartModel = new \App\Models\Cart();
                                    $cart = $cartModel->getCartByUserId($user["id"]);
                                    if ($cart) {
                                        $cartCount = $cartModel->getCartItemCount($cart["id"]);
                                        if ($cartCount > 0) {
                                            echo "<span class=\"absolute -top-2 -right-2 bg-stone-500 text-white rounded-full text-xs px-1.5\">". $cartCount ."</span>";
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Do nothing if there is an error
                                }
                            }
                            ?>
                        </span>
                    </a>
                    
                    <!-- User account -->
                    <?php if (isset($user)): ?>
                        <div class="relative group">
                            <button class="flex items-center space-x-1">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="hidden md:inline"><?= $user["email"] ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
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
    
    <main class="flex-grow container mx-auto px-4 py-6">' > tmp/layouts/header.php

# Create footer.php
echo '    </main>
    
    <footer class="bg-stone-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Phone Store</h3>
                    <p class="text-stone-300">
                        Your one-stop shop for the latest smartphones from top brands at competitive prices.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-stone-300 hover:text-white transition">Home</a></li>
                        <li><a href="/products" class="text-stone-300 hover:text-white transition">Products</a></li>
                        <li><a href="/cart" class="text-stone-300 hover:text-white transition">Cart</a></li>
                        <?php if (isset($user)): ?>
                            <li><a href="/account" class="text-stone-300 hover:text-white transition">My Account</a></li>
                        <?php else: ?>
                            <li><a href="/login" class="text-stone-300 hover:text-white transition">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-stone-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-2"></i>
                            123 Tech Street, Digital City
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone w-5 text-center mr-2"></i>
                            +1 (123) 456-7890
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope w-5 text-center mr-2"></i>
                            info@phonestore.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-stone-700 text-center text-stone-400">
                <p>&copy; <?= date("Y") ?> Phone Store. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        document.getElementById("mobile-menu-button").addEventListener("click", function() {
            const menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");
        });
        
        // Close alert messages
        document.querySelectorAll("[role=\"alert\"] svg").forEach(function(element) {
            element.addEventListener("click", function() {
                this.closest("[role=\"alert\"]").remove();
            });
        });
    </script>
</body>
</html>' > tmp/layouts/footer.php

# Create layouts directory in Docker container
docker exec phone-store-php-1 mkdir -p /var/www/html/src/Views/layouts

# Copy layout files to Docker container
docker cp tmp/layouts/header.php phone-store-php-1:/var/www/html/src/Views/layouts/
docker cp tmp/layouts/footer.php phone-store-php-1:/var/www/html/src/Views/layouts/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 