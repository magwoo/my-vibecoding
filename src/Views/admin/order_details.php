<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-stone-800">
                Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
            </h1>
            <a href="/admin/orders" class="text-stone-600 hover:text-stone-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
        </div>

        <!-- Order Status -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-stone-800 mb-4">Order Status</h2>
            <div class="bg-stone-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-500">Current Status</p>
                        <select onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)" 
                                class="mt-1 text-sm rounded-full px-3 py-1 <?= getStatusClass($order['status']) ?>">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-stone-500">Order Date</p>
                        <p class="text-sm font-medium text-stone-800">
                            <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-stone-800 mb-4">Customer Information</h2>
            <div class="bg-stone-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-stone-500">Email</p>
                        <p class="text-sm font-medium text-stone-800"><?= htmlspecialchars($order['user_email']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-stone-500">Phone</p>
                        <p class="text-sm font-medium text-stone-800"><?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Information -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-stone-800 mb-4">Shipping Information</h2>
            <div class="bg-stone-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-sm text-stone-500">Shipping Address</p>
                        <p class="text-sm font-medium text-stone-800">
                            <?= htmlspecialchars($order['shipping_address']) ?>
                        </p>
                    </div>
                    <?php if (!empty($order['tracking_number'])): ?>
                        <div>
                            <p class="text-sm text-stone-500">Tracking Number</p>
                            <p class="text-sm font-medium text-stone-800"><?= htmlspecialchars($order['tracking_number']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-stone-800 mb-4">Order Items</h2>
            <div class="bg-stone-50 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-stone-100 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 bg-stone-100 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 bg-stone-100 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 bg-stone-100 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>"
                                             class="h-12 w-12 object-cover rounded-lg mr-4">
                                        <div>
                                            <div class="text-sm font-medium text-stone-900"><?= htmlspecialchars($item['name']) ?></div>
                                            <div class="text-sm text-stone-500">SKU: <?= htmlspecialchars($item['product_id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-stone-500">
                                    $<?= number_format($item['price'], 2) ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-stone-500">
                                    <?= $item['quantity'] ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-stone-900">
                                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-stone-500">Subtotal</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-stone-900">
                                $<?= number_format($order['subtotal'], 2) ?>
                            </td>
                        </tr>
                        <?php if ($order['tax'] > 0): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-stone-500">Tax</td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-stone-900">
                                    $<?= number_format($order['tax'], 2) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($order['shipping'] > 0): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-stone-500">Shipping</td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-stone-900">
                                    $<?= number_format($order['shipping'], 2) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr class="bg-stone-100">
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-stone-800">Total</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-stone-800">
                                $<?= number_format($order['total_amount'], 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update was successful
            const select = event.target;
            select.className = `text-sm rounded-full px-3 py-1 ${getStatusClassJS(newStatus)}`;
        } else {
            alert('Error updating order status');
            // Reset the select to its previous value
            const select = event.target;
            select.value = select.getAttribute('data-previous-value');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order status');
        // Reset the select to its previous value
        const select = event.target;
        select.value = select.getAttribute('data-previous-value');
    });
}

function getStatusClassJS(status) {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'shipped':
            return 'bg-indigo-100 text-indigo-800';
        case 'delivered':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-stone-100 text-stone-800';
    }
}

// Store the previous value when selecting
document.querySelectorAll('select[onchange^="updateOrderStatus"]').forEach(select => {
    select.addEventListener('focus', function() {
        this.setAttribute('data-previous-value', this.value);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
