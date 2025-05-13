<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-stone-800">Orders</h1>
            
            <!-- Status filter -->
            <div class="flex items-center space-x-4">
                <span class="text-sm text-stone-500">Filter by status:</span>
                <select id="statusFilter" onchange="window.location.href=this.value" class="border border-stone-300 rounded-md text-sm">
                    <option value="/admin/orders" <?= empty($status) ? 'selected' : '' ?>>All Orders</option>
                    <option value="/admin/orders?status=pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="/admin/orders?status=processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="/admin/orders?status=shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="/admin/orders?status=delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="/admin/orders?status=cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="text-center py-8">
                <p class="text-stone-500">No orders found</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 bg-stone-50 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-stone-900">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-900"><?= htmlspecialchars($order['user_email']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-500"><?= $order['total_items'] ?> items</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-900">$<?= number_format($order['total_amount'], 2) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <select onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)" 
                                            class="text-sm rounded-full px-3 py-1 <?= getStatusClass($order['status']) ?>">
                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-500">
                                        <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="/admin/orders/<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-900">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?><?= $status ? "&status=$status" : '' ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-stone-300 bg-white text-sm font-medium text-stone-500 hover:bg-stone-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?><?= $status ? "&status=$status" : '' ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-stone-300 bg-white text-sm font-medium <?= $i === $currentPage ? 'text-stone-900 bg-stone-100' : 'text-stone-500 hover:bg-stone-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?><?= $status ? "&status=$status" : '' ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-stone-300 bg-white text-sm font-medium text-stone-500 hover:bg-stone-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
function getStatusClass($status) {
    switch ($status) {
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
?>

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
