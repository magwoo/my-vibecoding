<?php
// Debug script to test the ProductController's index method directly

// Include necessary files
require_once __DIR__ . '/../src/bootstrap.php';

use App\Controllers\ProductController;

// Create a new controller instance
$controller = new ProductController();

echo '<h1>ProductController Debug</h1>';

try {
    // Capture output buffer
    ob_start();
    
    // Call the controller's index method
    $controller->index();
    
    // Get the output
    $output = ob_get_clean();
    
    // Check if the output contains product content
    $hasProducts = strpos($output, 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6') !== false;
    $hasNoProductsMessage = strpos($output, 'No products found') !== false;
    
    echo '<h2>Controller Output Check</h2>';
    
    if ($hasProducts) {
        echo '<p style="color:green;">Products grid found in output!</p>';
    } elseif ($hasNoProductsMessage) {
        echo '<p style="color:orange;">No products message found in output.</p>';
    } else {
        echo '<p style="color:red;">No products grid or message found in output!</p>';
    }
    
    echo '<h2>Output Preview (first 500 characters)</h2>';
    echo '<div style="border:1px solid #ccc; padding:10px; margin:10px 0;">';
    echo htmlspecialchars(substr($output, 0, 500)) . '...';
    echo '</div>';
    
    echo '<h2>Controller Method Details</h2>';
    $productControllerReflection = new ReflectionClass(ProductController::class);
    $indexMethod = $productControllerReflection->getMethod('index');
    echo '<p>Method exists: ' . ($indexMethod ? 'Yes' : 'No') . '</p>';
    echo '<p>Method is public: ' . ($indexMethod->isPublic() ? 'Yes' : 'No') . '</p>';
    
} catch (Exception $e) {
    echo '<h2>Error</h2>';
    echo '<p style="color:red;">Error executing controller: ' . $e->getMessage() . '</p>';
    echo '<p>Trace: ' . $e->getTraceAsString() . '</p>';
} 