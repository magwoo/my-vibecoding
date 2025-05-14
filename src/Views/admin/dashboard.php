<!-- Заголовок панели управления -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Панель управления</h1>
    <p class="text-gray-600">Добро пожаловать в административную панель интернет-магазина телефонов.</p>
</div>

<!-- Статистика -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Общее количество заказов -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-stone-100 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Всего заказов</p>
                <p class="text-2xl font-bold"><?= $totalOrders ?></p>
            </div>
        </div>
    </div>
    
    <!-- Количество товаров -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-stone-100 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Всего товаров</p>
                <p class="text-2xl font-bold"><?= $totalProducts ?></p>
            </div>
        </div>
    </div>
    
    <!-- Заказы в обработке -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-600 text-sm">В обработке</p>
                <p class="text-2xl font-bold"><?= $ordersByStatus['в обработке'] ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Статистика заказов по статусам -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Статистика заказов</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <!-- В обработке -->
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-yellow-800 font-semibold">В обработке</p>
            <p class="text-2xl font-bold"><?= $ordersByStatus['в обработке'] ?></p>
        </div>
        
        <!-- Оплачен -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-blue-800 font-semibold">Оплачен</p>
            <p class="text-2xl font-bold"><?= $ordersByStatus['оплачен'] ?></p>
        </div>
        
        <!-- Отправлен -->
        <div class="bg-indigo-50 p-4 rounded-lg">
            <p class="text-indigo-800 font-semibold">Отправлен</p>
            <p class="text-2xl font-bold"><?= $ordersByStatus['отправлен'] ?></p>
        </div>
        
        <!-- Доставлен -->
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-green-800 font-semibold">Доставлен</p>
            <p class="text-2xl font-bold"><?= $ordersByStatus['доставлен'] ?></p>
        </div>
        
        <!-- Отменен -->
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-red-800 font-semibold">Отменен</p>
            <p class="text-2xl font-bold"><?= $ordersByStatus['отменен'] ?></p>
        </div>
    </div>
</div>

<!-- Последние заказы -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold">Последние заказы</h2>
        <a href="/admin/orders" class="text-stone-600 hover:text-stone-800 font-medium">
            Все заказы
        </a>
    </div>
    
    <?php if (empty($recentOrders)): ?>
    <div class="p-6 text-center text-gray-600">
        Пока нет заказов
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">№</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Клиент</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Дата</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Сумма</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Статус</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold text-gray-700">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr class="border-t border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm"><?= $order['id'] ?></td>
                    <td class="py-3 px-4 text-sm"><?= htmlspecialchars($order['user_email']) ?></td>
                    <td class="py-3 px-4 text-sm"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                    <td class="py-3 px-4 text-sm font-medium"><?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
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
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="/admin/orders/<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-800 mx-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
