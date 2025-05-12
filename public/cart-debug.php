<?php
// Debug script for Cart functionality

// Include necessary files
require_once __DIR__ . '/../src/bootstrap.php';

use App\Models\Cart;
use App\Core\Database;

// Output header
echo '<h1>Cart Debug</h1>';

try {
    // Initialize database
    $db = Database::getInstance();
    
    // Initialize cart model
    $cart = new Cart();
    
    // Get a sample cart ID (for debugging)
    echo '<h2>Available Carts</h2>';
    $carts = $db->query("SELECT * FROM carts LIMIT 5")->fetchAll();
    
    if (empty($carts)) {
        echo '<p>No carts found in database.</p>';
    } else {
        echo '<pre>';
        print_r($carts);
        echo '</pre>';
        
        $sampleCartId = $carts[0]['id'];
        
        // Try to get cart items
        echo '<h2>Cart Items for Cart ID: ' . $sampleCartId . '</h2>';
        $cartItems = $cart->getCartItems($sampleCartId);
        
        if (empty($cartItems)) {
            echo '<p>No items in this cart.</p>';
        } else {
            echo '<pre>';
            print_r($cartItems);
            echo '</pre>';
            
            // Try to get cart items with products
            echo '<h2>Cart Items With Products</h2>';
            try {
                $cartItemsWithProducts = $cart->getCartItemsWithProducts($sampleCartId);
                echo '<pre>';
                print_r($cartItemsWithProducts);
                echo '</pre>';
            } catch (Exception $e) {
                echo '<p>Error getting cart items with products: ' . $e->getMessage() . '</p>';
            }
        }
    }
    
    // Debug products table
    echo '<h2>Products Table Structure</h2>';
    $tableInfo = $db->query("DESCRIBE products")->fetchAll();
    echo '<pre>';
    print_r($tableInfo);
    echo '</pre>';
    
    // Check for sample product data
    echo '<h2>Sample Product Data</h2>';
    $products = $db->query("SELECT * FROM products LIMIT 3")->fetchAll();
    echo '<pre>';
    print_r($products);
    echo '</pre>';
    
} catch (Exception $e) {
    echo '<h2>Error</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p>Trace: ' . $e->getTraceAsString() . '</p>';
} 