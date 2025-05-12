<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-stone-800">Orders</h1>
        <div>
            <div class="relative inline-block">
                <select id="status-filter" class="appearance-none bg-stone-100 border border-stone-300 text-stone-700 py-2 px-4 pr-8 rounded-md focus:outline-none focus:ring-stone-500 focus:border-stone-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-stone-700">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="bg-stone-50 p-8 rounded-md text-stone-600 text-center">
            <i class="fas fa-shopping-bag text-3xl text-stone-400 mb-3"></i>
            <p class="text-lg mb-4">No orders found</p>
            <?php if ($status): ?>
                <a href="/admin/orders" class="text-stone-700 hover:text-stone-900 underline">
                    Clear filter
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-stone-50 border-b border-stone-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Order ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Customer</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Date</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Status</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-stone-600">Total</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium text-stone-800">
                                #<?= $order['id'] ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-stone-600">
                                <?= $order['user_email'] ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-stone-600 text-center">
                                <?= date('M j, Y', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="px-4 py-4 text-center">
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
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-bold text-stone-800">
                                $<?= number_format($order['total'], 2) ?>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="/admin/order?id=<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-800">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="flex space-x-1">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/admin/orders?page=<?= $i ?><?= $status ? '&status=' . $status : '' ?>" class="px-3 py-1 rounded-md <?= $i === $page ? 'bg-stone-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    // Handle status filter change
    document.getElementById('status-filter').addEventListener('change', function() {
        const status = this.value;
        if (status) {
            window.location.href = '/admin/orders?status=' + status;
        } else {
            window.location.href = '/admin/orders';
        }
    });
</script> 