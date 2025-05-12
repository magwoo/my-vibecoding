<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-stone-800">Order #<?= $order['id'] ?></h1>
            <div>
                <a href="/orders" class="text-stone-600 hover:text-stone-800 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Orders
                </a>
            </div>
        </div>
        
        <!-- Order Information -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-stone-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Order Date</h3>
                    <p class="text-stone-800"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="bg-stone-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Order Status</h3>
                    <?php 
                        $statusClass = '';
                        switch ($order['status']) {
                            case 'pending':
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                break;
                            case 'processing':
                                $statusClass = 'bg-blue-100 text-blue-800';
                                break;
                            case 'completed':
                                $statusClass = 'bg-green-100 text-green-800';
                                break;
                            case 'cancelled':
                                $statusClass = 'bg-red-100 text-red-800';
                                break;
                            default:
                                $statusClass = 'bg-gray-100 text-gray-800';
                        }
                    ?>
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="bg-stone-50 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-stone-500 mb-1">Order Total</h3>
                    <p class="text-stone-800 font-bold">$<?= number_format($order['total'], 2) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-stone-800 mb-3">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Product</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Price</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Quantity</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-stone-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <img src="<?= $item['image_url'] ?>" alt="<?= $item['name'] ?>" class="w-12 h-12 object-cover rounded-md mr-4">
                                        <div>
                                            <h3 class="text-sm font-medium text-stone-800"><?= $item['name'] ?></h3>
                                            <p class="text-xs text-stone-500"><?= $item['brand'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center text-sm text-stone-600">
                                    $<?= number_format($item['price'], 2) ?>
                                </td>
                                <td class="px-4 py-4 text-center text-sm text-stone-600">
                                    <?= $item['quantity'] ?>
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-bold text-stone-800">
                                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="border-t border-stone-200">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-stone-600">Subtotal:</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-stone-800">$<?= number_format($order['total'], 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-stone-600">Shipping:</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-stone-800">Free</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-base font-semibold text-stone-800">Total:</td>
                            <td class="px-4 py-3 text-right text-base font-bold text-stone-800">$<?= number_format($order['total'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Order Status Information -->
        <div class="p-4 border border-stone-200 rounded-md">
            <h3 class="text-md font-medium text-stone-800 mb-2">Order Status Information</h3>
            
            <div class="flex items-center mb-4">
                <div class="<?= $order['status'] === 'pending' || $order['status'] === 'processing' || $order['status'] === 'completed' ? 'bg-green-500' : 'bg-stone-300' ?> h-8 w-8 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-stone-800">Order Received</h4>
                    <p class="text-sm text-stone-600">We've received your order</p>
                </div>
            </div>
            
            <div class="flex items-center mb-4">
                <div class="<?= $order['status'] === 'processing' || $order['status'] === 'completed' ? 'bg-green-500' : 'bg-stone-300' ?> h-8 w-8 rounded-full flex items-center justify-center text-white">
                    <i class="fas <?= $order['status'] === 'processing' || $order['status'] === 'completed' ? 'fa-check' : 'fa-clock' ?>"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-stone-800">Processing</h4>
                    <p class="text-sm text-stone-600">Your order is being processed</p>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="<?= $order['status'] === 'completed' ? 'bg-green-500' : 'bg-stone-300' ?> h-8 w-8 rounded-full flex items-center justify-center text-white">
                    <i class="fas <?= $order['status'] === 'completed' ? 'fa-check' : 'fa-clock' ?>"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-stone-800">Completed</h4>
                    <p class="text-sm text-stone-600">Your order has been completed</p>
                </div>
            </div>
            
            <?php if ($order['status'] === 'cancelled'): ?>
                <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-md">
                    <div class="flex items-center">
                        <div class="bg-red-500 h-8 w-8 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-medium text-red-800">Order Cancelled</h4>
                            <p class="text-sm text-red-600">This order has been cancelled</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 