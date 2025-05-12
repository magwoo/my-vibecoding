#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create CartController.php file
cat > tmp/CartController.php << 'EOF'
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
EOF

# Create cart index view template
mkdir -p tmp/Views/cart

cat > tmp/Views/cart/index.php << 'EOF'
<h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <p class="text-gray-600">Your shopping cart is empty.</p>
        <a href="/products" class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded hover:bg-green-600 transition">Continue Shopping</a>
    </div>
<?php else: ?>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Product</th>
                            <th class="text-center py-2">Price</th>
                            <th class="text-center py-2">Quantity</th>
                            <th class="text-right py-2">Total</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr class="border-b">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <?php if (!empty($item["image"])): ?>
                                            <img src="/images/products/<?= $item["image"] ?>" alt="<?= $item["name"] ?>" class="w-16 h-16 object-cover mr-4">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-200 flex items-center justify-center mr-4">
                                                <i class="fas fa-mobile-alt text-gray-400 text-xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="font-medium"><?= $item["name"] ?></h3>
                                            <p class="text-gray-500"><?= $item["brand"] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">$<?= number_format($item["price"], 2) ?></td>
                                <td class="py-4">
                                    <form action="/cart/update" method="POST" class="flex justify-center">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                                        <input type="hidden" name="item_id" value="<?= $item["id"] ?>">
                                        <div class="flex items-center">
                                            <button type="button" class="quantity-btn quantity-decrease px-2 py-1 border rounded-l bg-gray-100 hover:bg-gray-200">-</button>
                                            <input type="number" name="quantity" value="<?= $item["quantity"] ?>" min="1" class="w-12 py-1 text-center border-t border-b">
                                            <button type="button" class="quantity-btn quantity-increase px-2 py-1 border rounded-r bg-gray-100 hover:bg-gray-200">+</button>
                                        </div>
                                        <button type="submit" class="ml-2 text-xs text-primary hover:text-green-700">Update</button>
                                    </form>
                                </td>
                                <td class="py-4 text-right">$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                                <td class="py-4 text-right">
                                    <form action="/cart/remove" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                                        <input type="hidden" name="item_id" value="<?= $item["id"] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-between mb-6">
                <a href="/products" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Continue Shopping</a>
            </div>
        </div>
        
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Order Summary</h2>
                
                <div class="flex justify-between mb-2">
                    <span>Subtotal</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                
                <div class="flex justify-between mb-2">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                
                <div class="border-t mt-4 pt-4">
                    <div class="flex justify-between font-bold">
                        <span>Total</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
                
                <a href="/checkout" class="block w-full bg-primary text-white text-center px-4 py-2 rounded mt-6 hover:bg-green-600 transition">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Quantity button handlers
        document.querySelectorAll('.quantity-decrease').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input');
                const value = parseInt(input.value, 10);
                if (value > 1) {
                    input.value = value - 1;
                }
            });
        });
        
        document.querySelectorAll('.quantity-increase').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input');
                const value = parseInt(input.value, 10);
                input.value = value + 1;
            });
        });
    </script>
<?php endif; ?>
EOF

# Create Cart model if not already exists
docker exec phone-store-php-1 cat /var/www/html/src/Models/Cart.php > /dev/null 2>&1
if [ $? -ne 0 ]; then
  cat > tmp/Cart.php << 'EOF'
<?php

namespace App\Models;

use App\Core\Database;

class Cart
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function getCartByUserId($userId)
    {
        $sql = "SELECT * FROM carts WHERE user_id = ? ORDER BY id DESC LIMIT 1";
        return $this->db->query($sql, [$userId])->fetch();
    }
    
    public function getCartBySessionId($sessionId)
    {
        $sql = "SELECT * FROM carts WHERE session_id = ? ORDER BY id DESC LIMIT 1";
        return $this->db->query($sql, [$sessionId])->fetch();
    }
    
    public function createCart($userId)
    {
        $data = [
            "user_id" => $userId,
            "created_at" => date("Y-m-d H:i:s")
        ];
        
        return $this->db->insert("carts", $data);
    }
    
    public function createCartWithSession($sessionId)
    {
        $data = [
            "session_id" => $sessionId,
            "created_at" => date("Y-m-d H:i:s")
        ];
        
        return $this->db->insert("carts", $data);
    }
    
    public function getCartItemCount($cartId)
    {
        $sql = "SELECT SUM(quantity) as count FROM cart_items WHERE cart_id = ?";
        $result = $this->db->query($sql, [$cartId])->fetch();
        
        return $result && $result["count"] ? (int)$result["count"] : 0;
    }
    
    public function getCartItems($cartId)
    {
        $sql = "SELECT * FROM cart_items WHERE cart_id = ?";
        return $this->db->query($sql, [$cartId])->fetchAll();
    }
    
    public function getCartItemsWithProducts($cartId)
    {
        $sql = "SELECT ci.*, p.name, p.brand, p.price, p.image 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.cart_id = ?";
        return $this->db->query($sql, [$cartId])->fetchAll();
    }
    
    public function addCartItem($cartId, $productId, $quantity = 1)
    {
        // Check if the product is already in the cart
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $existingItem = $this->db->query($sql, [$cartId, $productId])->fetch();
        
        if ($existingItem) {
            // Update quantity if already exists
            $newQuantity = $existingItem["quantity"] + $quantity;
            return $this->updateCartItemQuantity($existingItem["id"], $newQuantity);
        } else {
            // Add new item
            $data = [
                "cart_id" => $cartId,
                "product_id" => $productId,
                "quantity" => $quantity,
                "created_at" => date("Y-m-d H:i:s")
            ];
            
            return $this->db->insert("cart_items", $data);
        }
    }
    
    public function updateCartItemQuantity($itemId, $quantity)
    {
        $data = [
            "quantity" => $quantity,
            "updated_at" => date("Y-m-d H:i:s")
        ];
        
        return $this->db->update("cart_items", $itemId, $data);
    }
    
    public function removeCartItem($itemId)
    {
        return $this->db->delete("cart_items", $itemId);
    }
    
    public function clearCart($cartId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        return $stmt->rowCount();
    }
    
    public function migrateCart($sessionId, $userId)
    {
        // Get cart by session ID
        $sessionCart = $this->getCartBySessionId($sessionId);
        
        if (!$sessionCart) {
            return true;
        }
        
        // Get or create user cart
        $userCart = $this->getCartByUserId($userId);
        
        if (!$userCart) {
            $userCartId = $this->createCart($userId);
            $userCart = ["id" => $userCartId];
        }
        
        // Get session cart items
        $sessionCartItems = $this->getCartItems($sessionCart["id"]);
        
        if (empty($sessionCartItems)) {
            return true;
        }
        
        // Add session cart items to user cart
        foreach ($sessionCartItems as $item) {
            $this->addCartItem($userCart["id"], $item["product_id"], $item["quantity"]);
        }
        
        // Clear session cart
        $this->clearCart($sessionCart["id"]);
        
        return true;
    }
}
EOF
  
  docker cp tmp/Cart.php phone-store-php-1:/var/www/html/src/Models/
fi

# Create cart database tables (if needed)
docker exec phone-store-mysql-1 mysql -u phone_store -pphone_store_password phone_store -e "
    -- Create carts table if it doesn't exist
    CREATE TABLE IF NOT EXISTS carts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        session_id VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    
    -- Create cart_items table if it doesn't exist
    CREATE TABLE IF NOT EXISTS cart_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );
"

# Copy the CartController file to the Docker container
docker cp tmp/CartController.php phone-store-php-1:/var/www/html/src/Controllers/

# Create the cart view directory in Docker container
docker exec phone-store-php-1 mkdir -p /var/www/html/src/Views/cart

# Copy the cart view file to the Docker container
docker cp tmp/Views/cart/index.php phone-store-php-1:/var/www/html/src/Views/cart/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Cart functionality fixed successfully." 