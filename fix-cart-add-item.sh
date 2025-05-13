#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated Cart.php file with fixed addItem method
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
    
    public function initCart($userId)
    {
        // Check if user already has a cart
        $cart = $this->getCartByUserId($userId);
        
        if (!$cart) {
            // Create a new cart for the user
            $this->db->insert('carts', [
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    public function getCartByUserId($userId)
    {
        $sql = "SELECT * FROM carts WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetch();
    }
    
    public function getCartItems($cartId)
    {
        $sql = "SELECT ci.*, p.name, p.price, p.image_url FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        return $stmt->fetchAll();
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
        return $result ? (float)$result['total'] : 0;
    }
    
    public function addItem($cartId, $productId, $quantity = 1)
    {
        // Check if item already exists
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $stmt = $this->db->query($sql, [$cartId, $productId]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Update quantity
            $newQuantity = $item['quantity'] + $quantity;
            $this->db->update('cart_items', $item['id'], [
                'quantity' => $newQuantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Add new item
            $currentTime = date('Y-m-d H:i:s');
            $this->db->insert('cart_items', [
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }
        
        // Update cart last modified time
        $this->updateCartTimestamp($cartId);
    }
    
    public function updateItemQuantity($itemId, $quantity)
    {
        // Get cart ID for timestamp update
        $sql = "SELECT cart_id FROM cart_items WHERE id = ?";
        $result = $this->db->query($sql, [$itemId])->fetch();
        $cartId = $result ? $result['cart_id'] : null;
        
        if ($quantity > 0) {
            // Update quantity
            $this->db->update('cart_items', $itemId, [
                'quantity' => $quantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Remove item if quantity is 0 or negative
            $this->removeItem($itemId);
        }
        
        // Update cart last modified time
        if ($cartId) {
            $this->updateCartTimestamp($cartId);
        }
    }
    
    public function removeItem($itemId)
    {
        // Get cart ID for timestamp update
        $sql = "SELECT cart_id FROM cart_items WHERE id = ?";
        $result = $this->db->query($sql, [$itemId])->fetch();
        $cartId = $result ? $result['cart_id'] : null;
        
        // Remove item
        $this->db->delete('cart_items', $itemId);
        
        // Update cart last modified time
        if ($cartId) {
            $this->updateCartTimestamp($cartId);
        }
    }
    
    public function clearCart($cartId)
    {
        // Remove all items
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $this->db->query($sql, [$cartId]);
        
        // Update cart last modified time
        $this->updateCartTimestamp($cartId);
    }
    
    public function getCartItemCount($cartId)
    {
        $sql = "SELECT SUM(quantity) as count FROM cart_items WHERE cart_id = ?";
        $stmt = $this->db->query($sql, [$cartId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['count'] : 0;
    }
    
    public function getCartItemsWithProducts($cartId)
    {
        $sql = "
            SELECT ci.*, p.name, p.price, p.image_url as image, p.brand, p.storage 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ";
        
        $stmt = $this->db->query($sql, [$cartId]);
        return $stmt->fetchAll();
    }
    
    private function updateCartTimestamp($cartId)
    {
        $this->db->update('carts', $cartId, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
EOF

# Copy the fixed file to the container
docker cp tmp/Cart.php phone-store-php-1:/var/www/html/src/Models/Cart.php

# Restart the PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Fixed Cart model's addItem method to include updated_at field." 