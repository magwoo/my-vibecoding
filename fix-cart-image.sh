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

# Copy the updated file to the Docker container
docker cp tmp/Cart.php phone-store-php-1:/var/www/html/src/Models/

# Restart PHP container to apply changes
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Cart image field fix applied successfully." 