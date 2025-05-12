<?php

namespace App\Controllers;

use App\Core\Controller;
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
        // Check if user is logged in
        $user = $this->getCurrentUser();
        $cart = null;
        $cartItems = [];
        $total = 0;
        
        // Get cart either by user_id or session_id
        if ($user) {
            $cart = $this->cartModel->getCartByUserId($user["id"]);
        } else {
            $cart = $this->cartModel->getCartBySessionId(session_id());
        }
        
        // If cart exists, get items
        if ($cart) {
            $cartItems = $this->cartModel->getCartItemsWithProducts($cart["id"]);
            
            // Calculate total
            foreach ($cartItems as $item) {
                $total += $item["price"] * $item["quantity"];
            }
        }
        
        $this->view("cart/index", [
            "title" => "Shopping Cart",
            "user" => $user,
            "cartItems" => $cartItems,
            "total" => $total
        ]);
    }
    
    public function add()
    {
        // Check if request is POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect("cart");
            return;
        }
        
        // Validate CSRF token
        if (!isset($_POST["csrf_token"]) || !isset($_SESSION["csrf_token"]) || 
            $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
            $_SESSION["error"] = "CSRF token validation failed";
            $this->redirect("cart");
            return;
        }
        
        // Validate product_id and quantity
        if (!isset($_POST["product_id"]) || !is_numeric($_POST["product_id"])) {
            $_SESSION["error"] = "Invalid product";
            $this->redirect("cart");
            return;
        }
        
        $productId = (int)$_POST["product_id"];
        $quantity = isset($_POST["quantity"]) && is_numeric($_POST["quantity"]) ? (int)$_POST["quantity"] : 1;
        
        // Minimum quantity is 1
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            $_SESSION["error"] = "Product not found";
            $this->redirect("cart");
            return;
        }
        
        // Get or create cart
        $user = $this->getCurrentUser();
        $cart = null;
        
        if ($user) {
            $cart = $this->cartModel->getCartByUserId($user["id"]);
            if (!$cart) {
                $cartId = $this->cartModel->createCart($user["id"]);
                $cart = ["id" => $cartId];
            }
        } else {
            $cart = $this->cartModel->getCartBySessionId(session_id());
            if (!$cart) {
                $cartId = $this->cartModel->createCartWithSession(session_id());
                $cart = ["id" => $cartId];
            }
        }
        
        // Add item to cart
        $this->cartModel->addCartItem($cart["id"], $productId, $quantity);
        
        $_SESSION["success"] = "Product added to cart successfully";
        
        // Redirect back to the previous page or product page
        if (isset($_SERVER["HTTP_REFERER"]) && strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) !== false) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        } else {
            $this->redirect("product/" . $productId);
        }
    }
    
    public function update()
    {
        // Check if request is POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect("cart");
            return;
        }
        
        // Validate CSRF token
        if (!isset($_POST["csrf_token"]) || !isset($_SESSION["csrf_token"]) || 
            $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
            $_SESSION["error"] = "CSRF token validation failed";
            $this->redirect("cart");
            return;
        }
        
        // Validate item_id and quantity
        if (!isset($_POST["item_id"]) || !is_numeric($_POST["item_id"]) || 
            !isset($_POST["quantity"]) || !is_numeric($_POST["quantity"])) {
            $_SESSION["error"] = "Invalid cart item or quantity";
            $this->redirect("cart");
            return;
        }
        
        $itemId = (int)$_POST["item_id"];
        $quantity = (int)$_POST["quantity"];
        
        // Minimum quantity is 1
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Update cart item
        $this->cartModel->updateCartItemQuantity($itemId, $quantity);
        
        $_SESSION["success"] = "Cart updated successfully";
        $this->redirect("cart");
    }
    
    public function remove()
    {
        // Check if request is POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect("cart");
            return;
        }
        
        // Validate CSRF token
        if (!isset($_POST["csrf_token"]) || !isset($_SESSION["csrf_token"]) || 
            $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
            $_SESSION["error"] = "CSRF token validation failed";
            $this->redirect("cart");
            return;
        }
        
        // Validate item_id
        if (!isset($_POST["item_id"]) || !is_numeric($_POST["item_id"])) {
            $_SESSION["error"] = "Invalid cart item";
            $this->redirect("cart");
            return;
        }
        
        $itemId = (int)$_POST["item_id"];
        
        // Remove cart item
        $this->cartModel->removeCartItem($itemId);
        
        $_SESSION["success"] = "Item removed from cart successfully";
        $this->redirect("cart");
    }
}
