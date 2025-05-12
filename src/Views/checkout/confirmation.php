<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center bg-green-100 text-green-700 w-16 h-16 rounded-full mb-4">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-stone-800 mb-2">Order Confirmed!</h1>
            <p class="text-stone-600">Thank you for your purchase. Your order has been received.</p>
        </div>
        
        <div class="border-t border-stone-200 pt-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-stone-50 p-4 rounded-md text-center">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Order Number</h3>
                    <p class="text-stone-800 font-bold">#<?= $order['id'] ?></p>
                </div>
                <div class="bg-stone-50 p-4 rounded-md text-center">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Date</h3>
                    <p class="text-stone-800"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="bg-stone-50 p-4 rounded-md text-center">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Total</h3>
                    <p class="text-stone-800 font-bold">$<?= number_format($order['total'], 2) ?></p>
                </div>
            </div>
            
            <div class="bg-stone-50 p-4 rounded-md mb-6">
                <h3 class="text-md font-medium text-stone-800 mb-3">Order Summary</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-stone-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-stone-600">Product</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-stone-600">Quantity</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-stone-600">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($order['items'] as $item): ?>
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
                        <tfoot class="border-t border-stone-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-sm font-medium text-stone-600">Subtotal:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-stone-800">$<?= number_format($order['total'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-sm font-medium text-stone-600">Shipping:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-stone-800">Free</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-base font-semibold text-stone-800">Total:</td>
                                <td class="px-4 py-3 text-right text-base font-bold text-stone-800">$<?= number_format($order['total'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3">
                <a href="/orders" class="bg-stone-700 hover:bg-stone-800 text-white py-2 px-4 rounded-md transition text-center md:w-1/2">
                    View Your Orders
                </a>
                <a href="/products" class="bg-stone-100 hover:bg-stone-200 text-stone-800 py-2 px-4 rounded-md transition text-center md:w-1/2">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div> 