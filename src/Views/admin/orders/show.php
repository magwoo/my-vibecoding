<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-stone-800">Order #<?= $order['id'] ?></h1>
        <div>
            <a href="/admin/orders" class="text-stone-600 hover:text-stone-800 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
        </div>
    </div>
    
    <!-- Order Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-stone-800 mb-3">Order Details</h2>
            <div class="bg-stone-50 p-4 rounded-md">
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Order Date:</span>
                    <span class="text-stone-800 font-medium block"><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Order Status:</span>
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
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?> mt-1">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Order Total:</span>
                    <span class="text-stone-800 font-bold block">$<?= number_format($order['total'], 2) ?></span>
                </div>
            </div>
            
            <!-- Update Status Form -->
            <div class="mt-4">
                <h3 class="text-md font-semibold text-stone-800 mb-2">Update Order Status</h3>
                <form action="/admin/order/update-status" method="POST" class="flex space-x-2">
                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <select name="status" class="border border-stone-300 rounded-md focus:ring-stone-500 focus:border-stone-500">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    
                    <button type="submit" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md">
                        Update
                    </button>
                </form>
            </div>
        </div>
        
        <div>
            <h2 class="text-lg font-semibold text-stone-800 mb-3">Customer Information</h2>
            <div class="bg-stone-50 p-4 rounded-md">
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Email:</span>
                    <span class="text-stone-800 font-medium block"><?= $customer['email'] ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Customer ID:</span>
                    <span class="text-stone-800 font-medium block"><?= $customer['id'] ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-stone-600 text-sm">Account Created:</span>
                    <span class="text-stone-800 font-medium block"><?= date('F j, Y', strtotime($customer['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div>
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
</div> 