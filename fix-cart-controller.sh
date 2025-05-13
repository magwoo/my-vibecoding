#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated CartController.php file
cat > tmp/CartController.php << 'EOF'
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Router;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    private $cartModel;
    private $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    public function index()
    {
        if (!$this->isLoggedIn()) {
            Router::redirect('login');
            return;
        }
        
        $user = $this->getCurrentUser();
        $cart = $this->cartModel->getCartByUserId($user['id']);
        
        if (!$cart) {
            // Initialize cart if it doesn't exist
            $this->cartModel->initCart($user['id']);
            $cart = $this->cartModel->getCartByUserId($user['id']);
        }
        
        $cartItems = $this->cartModel->getCartItemsWithProducts($cart['id']);
        $cartTotal = $this->cartModel->getCartTotal($cart['id']);
        
        $this->view('cart/index', [
            'user' => $user,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal
        ]);
    }
    
    public function add()
    {
        if (!$this->isLoggedIn()) {
            Router::redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('products');
            return;
        }
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($productId <= 0 || $quantity <= 0) {
            $_SESSION['error'] = 'Invalid product or quantity';
            Router::redirect('products');
            return;
        }
        
        // Validate product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            Router::redirect('products');
            return;
        }
        
        $user = $this->getCurrentUser();
        $cart = $this->cartModel->getCartByUserId($user['id']);
        
        if (!$cart) {
            // Initialize cart if it doesn't exist
            $this->cartModel->initCart($user['id']);
            $cart = $this->cartModel->getCartByUserId($user['id']);
        }
        
        // Add product to cart
        $this->cartModel->addItem($cart['id'], $productId, $quantity);
        
        $_SESSION['success'] = 'Product added to cart';
        
        // Redirect back to product page or referrer
        $referer = $_SERVER['HTTP_REFERER'] ?? '/products';
        header('Location: ' . $referer);
        exit;
    }
    
    public function update()
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'You must be logged in to update cart';
            Router::redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('cart');
            return;
        }
        
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if ($itemId <= 0 || $quantity <= 0) {
            $_SESSION['error'] = 'Invalid cart item or quantity';
            Router::redirect('cart');
            return;
        }
        
        // Update item quantity
        $this->cartModel->updateItemQuantity($itemId, $quantity);
        
        $_SESSION['success'] = 'Cart updated successfully';
        
        // Redirect back to cart page
        Router::redirect('cart');
    }
    
    public function remove()
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'You must be logged in to remove items';
            Router::redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Router::redirect('cart');
            return;
        }
        
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        
        if ($itemId <= 0) {
            $_SESSION['error'] = 'Invalid cart item';
            Router::redirect('cart');
            return;
        }
        
        // Remove item from cart
        $this->cartModel->removeItem($itemId);
        
        $_SESSION['success'] = 'Item removed from cart';
        
        // Redirect back to cart page
        Router::redirect('cart');
    }
    
    // Helper method to output JSON response
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
EOF

# Copy the fixed file to the container
docker cp tmp/CartController.php phone-store-php-1:/var/www/html/src/Controllers/CartController.php

# Restart the PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Fixed CartController to redirect back to the cart page after updating cart items." 