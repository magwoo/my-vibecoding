<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-stone-800 mb-6">Checkout</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Order summary -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-stone-800 mb-4">Order Summary</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full mb-4">
                        <thead class="border-b border-stone-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-stone-600">Product</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-stone-600">Quantity</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-stone-600">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img src="<?= $item['image_url'] ?>" alt="<?= $item['name'] ?>" class="w-12 h-12 object-cover rounded-md mr-3">
                                            <div>
                                                <h3 class="text-sm font-medium text-stone-800"><?= $item['name'] ?></h3>
                                                <p class="text-xs text-stone-500"><?= $item['brand'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-stone-600">
                                        <?= $item['quantity'] ?>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-stone-800">
                                        $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="border-t border-stone-200 pt-4 mt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-stone-600">Subtotal</span>
                        <span class="font-medium">$<?= number_format($cartTotal, 2) ?></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-stone-600">Shipping</span>
                        <span class="font-medium">Free</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold mt-2 pt-2 border-t border-stone-100">
                        <span>Total</span>
                        <span>$<?= number_format($cartTotal, 2) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Shipping address info (assuming we're not collecting this, just showing the user's email) -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-stone-800 mb-4">Shipping Information</h2>
                
                <div class="mb-4">
                    <p class="text-stone-600 mb-2">
                        <strong>Email:</strong> <?= $user['email'] ?>
                    </p>
                    <p class="text-stone-600">
                        Once your order is placed, you will receive an email confirmation.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Order actions -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-4">
                <h2 class="text-lg font-semibold text-stone-800 mb-4">Order Total</h2>
                
                <div class="text-3xl font-bold text-stone-800 mb-6">
                    $<?= number_format($cartTotal, 2) ?>
                </div>
                
                <form action="/checkout" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <button type="submit" class="w-full bg-stone-700 hover:bg-stone-800 text-white py-3 px-4 rounded-md transition mb-4">
                        Place Order
                    </button>
                </form>
                
                <a href="/cart" class="w-full border border-stone-300 text-stone-700 py-2 px-4 rounded-md transition text-center block hover:bg-stone-50">
                    Return to Cart
                </a>
                
                <div class="mt-6 text-center text-sm text-stone-500">
                    <p class="mb-2">
                        <i class="fas fa-lock mr-1"></i>
                        Secure checkout
                    </p>
                </div>
            </div>
        </div>
    </div>
</div> 