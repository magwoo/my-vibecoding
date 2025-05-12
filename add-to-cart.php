<?php
// Script to add a test product to the cart

// Include necessary files
require_once __DIR__ . '/src/bootstrap.php';

use App\Models\Cart;
use App\Core\Database;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Create a default user ID for testing
$userId = 2; // This should match an existing user ID in your database

try {
    // Initialize cart
    $cart = new Cart();
    
    // Get or create a cart for the user
    $cartId = $cart->initCart($userId);
    
    // Add a product to the cart
    $productId = 1; // This should match an existing product ID
    $quantity = 2;
    
    $result = $cart->addCartItem($cartId, $productId, $quantity);
    
    echo "Product added to cart successfully! Cart ID: $cartId, Product ID: $productId, Quantity: $quantity";
    echo "<br><a href='/cart'>View Cart</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 