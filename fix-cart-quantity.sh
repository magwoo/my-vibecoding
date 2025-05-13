#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated cart/index.php file with fixed quantity update functionality
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
                                            <img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>" class="w-16 h-16 object-cover mr-4">
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
                                    <form action="/cart/update" method="POST" class="flex justify-center" id="form-<?= $item["id"] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                                        <input type="hidden" name="item_id" value="<?= $item["id"] ?>">
                                        <div class="flex items-center">
                                            <button type="button" class="quantity-btn px-2 py-1 border rounded-l bg-gray-100 hover:bg-gray-200" 
                                                onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                                            <input type="number" name="quantity" value="<?= $item["quantity"] ?>" min="1" 
                                                class="w-12 py-1 text-center border-t border-b" id="quantity-<?= $item["id"] ?>"
                                                onchange="document.getElementById('form-<?= $item["id"] ?>').submit()">
                                            <button type="button" class="quantity-btn px-2 py-1 border rounded-r bg-gray-100 hover:bg-gray-200"
                                                onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                                        </div>
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
                    <span>$<?= number_format($cartTotal, 2) ?></span>
                </div>
                
                <div class="flex justify-between mb-2">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                
                <div class="border-t mt-4 pt-4">
                    <div class="flex justify-between font-bold">
                        <span>Total</span>
                        <span>$<?= number_format($cartTotal, 2) ?></span>
                    </div>
                </div>
                
                <a href="/checkout" class="block w-full bg-primary text-white text-center px-4 py-2 rounded mt-6 hover:bg-green-600 transition">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function updateQuantity(itemId, change) {
            const input = document.getElementById('quantity-' + itemId);
            const currentValue = parseInt(input.value, 10);
            const newValue = currentValue + change;
            
            // Ensure quantity is at least 1
            if (newValue >= 1) {
                input.value = newValue;
                // Submit the form automatically
                document.getElementById('form-' + itemId).submit();
            }
        }
    </script>
<?php endif; ?>
EOF

# Copy the fixed file to the container
docker cp tmp/index.php phone-store-php-1:/var/www/html/src/Views/cart/index.php

# Restart the PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Fixed cart quantity update functionality to work automatically when clicking +/- buttons." 