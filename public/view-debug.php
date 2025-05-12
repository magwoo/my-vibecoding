<?php
// Debug script to include the products/index.php view directly

// Include necessary files
require_once __DIR__ . '/../src/bootstrap.php';

use App\Models\Product;

// Set up the necessary data for the view
$productModel = new Product();
$filters = [];
$sort = 'created_at DESC';
$page = 1;
$perPage = 12;

// Get products
$products = $productModel->getProducts($filters, $sort, $page, $perPage);
$totalProducts = $productModel->getProductCount($filters);
$totalPages = ceil($totalProducts / $perPage);

// Get filter options
$brands = $productModel->getAllBrands();
$screenSizes = $productModel->getAllScreenSizes();
$storageOptions = $productModel->getAllStorageOptions();
$osOptions = $productModel->getAllOperatingSystems();

// Set up for the view
$title = 'Products';
$currentPage = $page;

// Create a session CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Debug information before including the view
echo '<h1>View Debug - Before Including View</h1>';
echo '<h2>Data Available to View</h2>';
echo '<p>Products count: ' . count($products) . '</p>';
echo '<p>Total products: ' . $totalProducts . '</p>';
echo '<p>First product name: ' . ($products[0]['name'] ?? 'None') . '</p>';

// Helper functions used in the view
function removeFilterFromUrl($param, $value = null) {
    $params = $_GET;
    
    if ($value !== null && isset($params[$param]) && is_array($params[$param])) {
        $key = array_search($value, $params[$param]);
        if ($key !== false) {
            unset($params[$param][$key]);
        }
        if (empty($params[$param])) {
            unset($params[$param]);
        }
    } else {
        unset($params[$param]);
    }
    
    $queryString = http_build_query($params);
    return '/products' . ($queryString ? '?' . $queryString : '');
}

function paginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    $queryString = http_build_query($params);
    return '/products?' . $queryString;
}

// Include the actual view content here
ob_start();
include VIEWS_DIR . '/products/index.php';
$viewOutput = ob_get_clean();

// Check if the grid is present in the view output
$hasProductsGrid = strpos($viewOutput, 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6') !== false;
$hasNoProductsMessage = strpos($viewOutput, 'No products found') !== false;

echo '<h2>View Output Analysis</h2>';
if ($hasProductsGrid) {
    echo '<p style="color:green;">Products grid found in view output!</p>';
} elseif ($hasNoProductsMessage) {
    echo '<p style="color:orange;">No products message found in view output.</p>';
} else {
    echo '<p style="color:red;">No products grid or message found in view output!</p>';
}

// Show the view content
echo '<h2>View Content</h2>';
echo $viewOutput; 