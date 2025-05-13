<?php
// Test script to add product to cart with proper CSRF token and authentication

require_once __DIR__ . '/../src/bootstrap.php';

use App\Controllers\CartController;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo '<h1>Cart "Add" Test</h1>';

// Set up a test user and CSRF token
$_SESSION['user_id'] = 1; 
$_SESSION['user'] = ['id' => 1, 'email' => 'test@example.com'];
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Show the token and session data
echo '<h2>Session Data</h2>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Set up the POST variables for the controller
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['product_id'] = 1; // ID of the product to add
$_POST['quantity'] = 1;
$_POST['csrf_token'] = $_SESSION['csrf_token'];

echo '<h2>Simulating POST request</h2>';
echo '<p>product_id: ' . $_POST['product_id'] . '</p>';
echo '<p>quantity: ' . $_POST['quantity'] . '</p>';
echo '<p>csrf_token: ' . substr($_POST['csrf_token'], 0, 8) . '...</p>';

try {
    // Create controller
    $controller = new CartController();
    
    // Capture output
    ob_start();
    
    // Call the add method
    $controller->add();
    
    // Capture any output
    $output = ob_get_clean();
    
    echo '<h2>Controller Response</h2>';
    
    if (empty($output)) {
        echo '<p style="color:green;">No output, likely successful! Check the session for success message:</p>';
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    } else {
        echo '<p>Response content:</p>';
        echo '<div style="border:1px solid #ccc; padding:10px; margin:10px 0;">';
        echo htmlspecialchars($output);
        echo '</div>';
    }
    
    echo '<p>Go to <a href="/cart">Cart Page</a> to verify the item was added.</p>';
    
} catch (Exception $e) {
    echo '<h2>Error</h2>';
    echo '<p style="color:red;">' . $e->getMessage() . '</p>';
    echo '<p>Trace: <pre>' . $e->getTraceAsString() . '</pre></p>';
} 