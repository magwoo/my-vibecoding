<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h1 class="text-2xl font-bold text-stone-800 mb-6">My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <div class="text-center py-12">
            <i class="fas fa-shopping-bag text-4xl text-stone-300 mb-4"></i>
            <h2 class="text-xl font-semibold text-stone-700 mb-2">No orders yet</h2>
            <p class="text-stone-500 mb-6">You haven't placed any orders yet.</p>
            <a href="/products" class="bg-stone-700 hover:bg-stone-800 text-white px-6 py-3 rounded-md inline-block transition">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-stone-50 border-b border-stone-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Order ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Date</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Status</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-stone-600">Total</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium text-stone-800">
                                #<?= $order['id'] ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-stone-600">
                                <?= date('M j, Y', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="px-4 py-4">
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
                                <a href="/order/<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-800">
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
                        <a href="/orders?page=<?= $i ?>" class="px-3 py-1 rounded-md <?= $i === $page ? 'bg-stone-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div> 