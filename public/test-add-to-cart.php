<?php
// Test script to verify adding items to cart works

require_once __DIR__ . '/../src/bootstrap.php';

use App\Models\Cart;
use App\Models\Product;

echo '<h1>Add to Cart Test</h1>';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up test user
$userId = 1; // assuming user with ID 1 exists
$_SESSION['user_id'] = $userId;

try {
    // Initialize cart model and product model
    $cartModel = new Cart();
    $productModel = new Product();
    
    // Get a product to add to cart
    $products = $productModel->getProducts();
    if (empty($products)) {
        die('<p style="color:red;">No products found to test with</p>');
    }
    
    $testProduct = $products[0];
    $productId = $testProduct['id'];
    $quantity = 1;
    
    echo '<p>Test product: ' . $testProduct['name'] . ' (ID: ' . $productId . ')</p>';
    
    // Initialize cart if needed
    $cart = $cartModel->getCartByUserId($userId);
    if (!$cart) {
        $cartModel->initCart($userId);
        $cart = $cartModel->getCartByUserId($userId);
        echo '<p>Created new cart for user ID ' . $userId . '</p>';
    } else {
        echo '<p>Using existing cart (ID: ' . $cart['id'] . ') for user ID ' . $userId . '</p>';
    }
    
    // Add item to cart
    echo '<h2>Testing addItem method</h2>';
    $cartModel->addItem($cart['id'], $productId, $quantity);
    echo '<p style="color:green;">Successfully added product ID ' . $productId . ' to cart!</p>';
    
    // Get cart items to verify
    $cartItems = $cartModel->getCartItems($cart['id']);
    echo '<h2>Cart Items:</h2>';
    
    if (empty($cartItems)) {
        echo '<p style="color:red;">No items found in cart after adding.</p>';
    } else {
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
        echo '<tr><th>ID</th><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>';
        
        foreach ($cartItems as $item) {
            echo '<tr>';
            echo '<td>' . $item['id'] . '</td>';
            echo '<td>' . $item['name'] . '</td>';
            echo '<td>' . $item['quantity'] . '</td>';
            echo '<td>$' . number_format($item['price'], 2) . '</td>';
            echo '<td>$' . number_format($item['quantity'] * $item['price'], 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    // Show cart total
    $cartTotal = $cartModel->getCartTotal($cart['id']);
    echo '<p>Cart Total: $' . number_format($cartTotal, 2) . '</p>';
    
    echo '<p><a href="/cart">View Cart Page</a></p>';
    
} catch (Exception $e) {
    echo '<h2>Error:</h2>';
    echo '<p style="color:red;">' . $e->getMessage() . '</p>';
    echo '<p>Trace: ' . nl2br($e->getTraceAsString()) . '</p>';
} 