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
