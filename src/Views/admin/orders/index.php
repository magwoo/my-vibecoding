<!-- Заголовок страницы -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Управление заказами</h1>
</div>

<!-- Фильтры -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Фильтры</h2>
    
    <form action="/admin/orders" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Статус заказа -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус заказа</label>
            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                <option value="">Все статусы</option>
                <option value="в обработке" <?= isset($filters['status']) && $filters['status'] === 'в обработке' ? 'selected' : '' ?>>В обработке</option>
                <option value="оплачен" <?= isset($filters['status']) && $filters['status'] === 'оплачен' ? 'selected' : '' ?>>Оплачен</option>
                <option value="отправлен" <?= isset($filters['status']) && $filters['status'] === 'отправлен' ? 'selected' : '' ?>>Отправлен</option>
                <option value="доставлен" <?= isset($filters['status']) && $filters['status'] === 'доставлен' ? 'selected' : '' ?>>Доставлен</option>
                <option value="отменен" <?= isset($filters['status']) && $filters['status'] === 'отменен' ? 'selected' : '' ?>>Отменен</option>
            </select>
        </div>
        
        <!-- Дата от -->
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Дата от</label>
            <input type="date" id="date_from" name="date_from" value="<?= isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : '' ?>" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
        </div>
        
        <!-- Дата до -->
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Дата до</label>
            <input type="date" id="date_to" name="date_to" value="<?= isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : '' ?>" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
        </div>
        
        <!-- Кнопки -->
        <div class="md:col-span-3 flex items-center">
            <button type="submit" class="bg-stone-600 hover:bg-stone-700 text-white font-medium py-2 px-4 rounded-md transition mr-2">
                Применить
            </button>
            <a href="/admin/orders" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition">
                Сбросить
            </a>
        </div>
    </form>
</div>

<!-- Таблица заказов -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($orders)): ?>
    <div class="p-6 text-center text-gray-600">
        <p>Заказы не найдены.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">№</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Клиент</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Дата</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Сумма</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Статус</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
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
                        <a href="/admin/orders/<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-800 mx-1" title="Просмотреть детали">
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

<!-- Пагинация -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center">
    <div class="flex space-x-1">
        <?php 
        // Формируем URL для пагинации с сохранением всех параметров фильтрации
        $queryParams = $_GET;
        
        // Кнопка "Предыдущая страница"
        if ($currentPage > 1):
            $queryParams['page'] = $currentPage - 1;
            $prevPageUrl = '/admin/orders?' . http_build_query($queryParams);
        ?>
        <a href="<?= $prevPageUrl ?>" class="px-4 py-2 border border-gray-300 rounded-md text-stone-600 hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <?php endif; ?>
        
        <?php 
        // Формируем ссылки на страницы
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        // Добавляем первую страницу и многоточие, если нужно
        if ($startPage > 1):
            $queryParams['page'] = 1;
            $firstPageUrl = '/admin/orders?' . http_build_query($queryParams);
        ?>
        <a href="<?= $firstPageUrl ?>" class="px-4 py-2 border border-gray-300 rounded-md text-stone-600 hover:bg-stone-50 transition">
            1
        </a>
        <?php if ($startPage > 2): ?>
        <span class="px-4 py-2 text-gray-500">...</span>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php 
        // Выводим страницы
        for ($i = $startPage; $i <= $endPage; $i++):
            $queryParams['page'] = $i;
            $pageUrl = '/admin/orders?' . http_build_query($queryParams);
        ?>
        <a href="<?= $pageUrl ?>" class="px-4 py-2 border <?= $i === $currentPage ? 'bg-stone-600 text-white' : 'border-gray-300 text-stone-600 hover:bg-stone-50' ?> rounded-md transition">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php 
        // Добавляем последнюю страницу и многоточие, если нужно
        if ($endPage < $totalPages):
            $queryParams['page'] = $totalPages;
            $lastPageUrl = '/admin/orders?' . http_build_query($queryParams);
        ?>
        <?php if ($endPage < $totalPages - 1): ?>
        <span class="px-4 py-2 text-gray-500">...</span>
        <?php endif; ?>
        <a href="<?= $lastPageUrl ?>" class="px-4 py-2 border border-gray-300 rounded-md text-stone-600 hover:bg-stone-50 transition">
            <?= $totalPages ?>
        </a>
        <?php endif; ?>
        
        <?php 
        // Кнопка "Следующая страница"
        if ($currentPage < $totalPages):
            $queryParams['page'] = $currentPage + 1;
            $nextPageUrl = '/admin/orders?' . http_build_query($queryParams);
        ?>
        <a href="<?= $nextPageUrl ?>" class="px-4 py-2 border border-gray-300 rounded-md text-stone-600 hover:bg-stone-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
