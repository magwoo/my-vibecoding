#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated Cart.php file with corrected SQL query
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
    
    public function initCart($userId)
    {
        // Check if user already has a cart
        $cart = $this->getCartByUserId($userId);
        
        if (!$cart) {
            // Create a new cart for the user
            return $this->createCart($userId);
        }
        
        return $cart["id"];
    }
    
    public function getCartItems($cartId)
    {
        $sql = "SELECT * FROM cart_items WHERE cart_id = ?";
        return $this->db->query($sql, [$cartId])->fetchAll();
    }
    
    public function getCartItemsWithProducts($cartId)
    {
        // Fixed SQL query to use image_url instead of image
        $sql = "SELECT ci.*, p.name, p.brand, p.price, p.image_url as image 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.cart_id = ?";
        return $this->db->query($sql, [$cartId])->fetchAll();
    }
    
    public function getCartTotal($cartId)
    {
        $sql = "
            SELECT SUM(ci.quantity * p.price) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ";
        
        $result = $this->db->query($sql, [$cartId])->fetch();
        return $result ? (float)$result["total"] : 0;
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
    
    // Alias for compatibility with different naming conventions
    public function addItem($cartId, $productId, $quantity = 1)
    {
        return $this->addCartItem($cartId, $productId, $quantity);
    }
    
    public function updateCartItemQuantity($itemId, $quantity)
    {
        $data = [
            "quantity" => $quantity,
            "updated_at" => date("Y-m-d H:i:s")
        ];
        
        return $this->db->update("cart_items", $itemId, $data);
    }
    
    // Alias for compatibility
    public function updateItemQuantity($itemId, $quantity)
    {
        return $this->updateCartItemQuantity($itemId, $quantity);
    }
    
    public function removeCartItem($itemId)
    {
        return $this->db->delete("cart_items", $itemId);
    }
    
    // Alias for compatibility
    public function removeItem($itemId)
    {
        return $this->removeCartItem($itemId);
    }
    
    public function clearCart($cartId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        return $stmt->rowCount();
    }
    
    public function getCartItemCount($cartId)
    {
        $sql = "SELECT SUM(quantity) as count FROM cart_items WHERE cart_id = ?";
        $result = $this->db->query($sql, [$cartId])->fetch();
        
        return $result && $result["count"] ? (int)$result["count"] : 0;
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
    
    private function updateCartTimestamp($cartId)
    {
        $this->db->update("carts", $cartId, [
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }
}
EOF

# Create a fix for the cart view template to use the correct image field
cat > tmp/index.php << 'EOF'
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

# Copy the updated files to the Docker container
docker cp tmp/Cart.php phone-store-php-1:/var/www/html/src/Models/
docker cp tmp/index.php phone-store-php-1:/var/www/html/src/Views/cart/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Cart image field fix applied successfully." 