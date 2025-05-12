<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../src/bootstrap.php"; 

echo "Bootstrap loaded<br>";

$controller = new \App\Controllers\ProductController();
echo "ProductController created<br>";

$controller->index();
echo "index method called<br>";

// Debug script to check if products are being retrieved correctly

// Include necessary files
require_once __DIR__ . '/../src/bootstrap.php';

use App\Models\Product;
use App\Core\Database;

// Initialize database and product model
$db = Database::getInstance();
$productModel = new Product();

echo '<h1>Products Debug</h1>';

// Check if products table exists
echo '<h2>Database Tables Check</h2>';
try {
    $result = $db->query("SHOW TABLES LIKE 'products'")->fetchAll();
    if (!empty($result)) {
        echo '<p style="color:green;">Products table exists in the database</p>';
    } else {
        echo '<p style="color:red;">Products table does NOT exist in the database!</p>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">Error checking tables: ' . $e->getMessage() . '</p>';
}

// Check product count
echo '<h2>Product Count Check</h2>';
try {
    $count = $productModel->getProductCount();
    echo '<p>Total products in the database: ' . $count . '</p>';
} catch (Exception $e) {
    echo '<p style="color:red;">Error getting product count: ' . $e->getMessage() . '</p>';
}

// Get and display all products
echo '<h2>Products Retrieval Check</h2>';
try {
    $products = $productModel->getProducts();
    
    if (empty($products)) {
        echo '<p style="color:red;">No products found in the database!</p>';
    } else {
        echo '<p style="color:green;">Found ' . count($products) . ' products</p>';
        
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
        echo '<tr><th>ID</th><th>Name</th><th>Brand</th><th>Price</th><th>Image URL</th></tr>';
        
        foreach ($products as $product) {
            echo '<tr>';
            echo '<td>' . $product['id'] . '</td>';
            echo '<td>' . $product['name'] . '</td>';
            echo '<td>' . $product['brand'] . '</td>';
            echo '<td>$' . number_format($product['price'], 2) . '</td>';
            echo '<td>' . $product['image_url'] . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">Error retrieving products: ' . $e->getMessage() . '</p>';
}

// Check SQL query directly
echo '<h2>Direct SQL Query Check</h2>';
try {
    $sql = "SELECT * FROM products LIMIT 5";
    $result = $db->query($sql)->fetchAll();
    
    if (empty($result)) {
        echo '<p style="color:red;">No products found via direct SQL query!</p>';
    } else {
        echo '<p style="color:green;">Found ' . count($result) . ' products via direct SQL</p>';
        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">Error with direct SQL query: ' . $e->getMessage() . '</p>';
} 