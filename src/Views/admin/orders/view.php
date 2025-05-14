<!-- Заголовок страницы -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Заказ #<?= $order['id'] ?></h1>
    <a href="/admin/orders" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Назад к списку
    </a>
</div>

<!-- Информация о заказе -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-xl font-semibold mb-4">Информация о заказе</h2>
            <table class="min-w-full text-sm">
                <tr>
                    <td class="py-2 text-gray-600 pr-4">ID заказа:</td>
                    <td class="py-2 font-medium"><?= $order['id'] ?></td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-600 pr-4">Дата заказа:</td>
                    <td class="py-2"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-600 pr-4">Email клиента:</td>
                    <td class="py-2"><?= htmlspecialchars($order['user_email']) ?></td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-600 pr-4">Сумма заказа:</td>
                    <td class="py-2 font-medium"><?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</td>
                </tr>
            </table>
        </div>
        
        <!-- Управление статусом заказа -->
        <div>
            <div class="mb-3">
                <span class="text-gray-600 text-sm mr-2">Текущий статус:</span>
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                    <?php
                    $statusColor = '';
                    switch ($order['status']) {
                        case 'в обработке':
                            $statusColor = 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'оплачен':
                            $statusColor = 'bg-blue-100 text-blue-800';
                            break;
                        case 'отправлен':
                            $statusColor = 'bg-indigo-100 text-indigo-800';
                            break;
                        case 'доставлен':
                            $statusColor = 'bg-green-100 text-green-800';
                            break;
                        case 'отменен':
                            $statusColor = 'bg-red-100 text-red-800';
                            break;
                        default:
                            $statusColor = 'bg-gray-100 text-gray-800';
                    }
                    echo $statusColor;
                    ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </span>
            </div>
            
            <div>
                <span class="text-gray-600 text-sm block mb-2">Изменить статус:</span>
                <div class="flex flex-wrap gap-2">
                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'в обработке')" 
                            class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200 transition <?= $order['status'] === 'в обработке' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $order['status'] === 'в обработке' ? 'disabled' : '' ?>>
                        В обработке
                    </button>
                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'оплачен')" 
                            class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition <?= $order['status'] === 'оплачен' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $order['status'] === 'оплачен' ? 'disabled' : '' ?>>
                        Оплачен
                    </button>
                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'отправлен')" 
                            class="px-3 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full hover:bg-indigo-200 transition <?= $order['status'] === 'отправлен' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $order['status'] === 'отправлен' ? 'disabled' : '' ?>>
                        Отправлен
                    </button>
                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'доставлен')" 
                            class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition <?= $order['status'] === 'доставлен' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $order['status'] === 'доставлен' ? 'disabled' : '' ?>>
                        Доставлен
                    </button>
                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'отменен')" 
                            class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200 transition <?= $order['status'] === 'отменен' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $order['status'] === 'отменен' ? 'disabled' : '' ?>>
                        Отменен
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Товары в заказе -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold">Товары в заказе</h2>
    </div>
    
    <?php if (empty($orderItems)): ?>
    <div class="p-6 text-center text-gray-600">
        <p>Информация о товарах не найдена.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товар</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Количество</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-md object-cover" src="<?= $item['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($item['brand']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= number_format($item['price'], 0, '.', ' ') ?> ₽</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="text-sm text-gray-900"><?= $item['quantity'] ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Итоговая строка -->
                <tr class="bg-gray-50">
                    <td colspan="3" class="px-6 py-4 text-right font-semibold">
                        Итого:
                    </td>
                    <td class="px-6 py-4 text-right font-bold">
                        <?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
