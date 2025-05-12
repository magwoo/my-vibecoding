<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-stone-800">Admin Dashboard</h1>
        <div>
            <a href="/admin/products/add" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>
    </div>
    
    <!-- Statistics cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-stone-50 p-6 rounded-lg shadow-sm border border-stone-100">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-stone-800"><?= $totalProducts ?></h2>
                    <p class="text-stone-500 text-sm">Total Products</p>
                </div>
                <div class="bg-stone-200 text-stone-700 rounded-full p-3">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/products" class="text-stone-600 hover:text-stone-800 text-sm">
                    View All Products <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <div class="bg-stone-50 p-6 rounded-lg shadow-sm border border-stone-100">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-stone-800"><?= $totalOrders ?></h2>
                    <p class="text-stone-500 text-sm">Total Orders</p>
                </div>
                <div class="bg-stone-200 text-stone-700 rounded-full p-3">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/orders" class="text-stone-600 hover:text-stone-800 text-sm">
                    View All Orders <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <div class="bg-stone-50 p-6 rounded-lg shadow-sm border border-stone-100">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-stone-800"><?= $pendingOrders ?></h2>
                    <p class="text-stone-500 text-sm">Pending Orders</p>
                </div>
                <div class="bg-yellow-100 text-yellow-700 rounded-full p-3">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/orders?status=pending" class="text-stone-600 hover:text-stone-800 text-sm">
                    View Pending Orders <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent orders table -->
    <div>
        <h2 class="text-xl font-semibold text-stone-800 mb-4">Recent Orders</h2>
        
        <?php if (empty($recentOrders)): ?>
            <div class="bg-stone-50 p-4 rounded-md text-stone-600 text-center">
                No orders yet.
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
                        <?php foreach ($recentOrders as $order): ?>
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
            
            <div class="mt-4 text-right">
                <a href="/admin/orders" class="text-stone-600 hover:text-stone-800 text-sm">
                    View All Orders <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div> 