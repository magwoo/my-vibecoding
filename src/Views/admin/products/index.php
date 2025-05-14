<!-- Заголовок и действия -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Управление товарами</h1>
    <a href="/admin/products/create" class="bg-stone-600 hover:bg-stone-700 text-white font-medium py-2 px-4 rounded-md transition flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Добавить товар
    </a>
</div>

<!-- Фильтры -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Фильтры</h2>
    
    <form action="/admin/products" method="GET" class="flex flex-wrap gap-4">
        <!-- Поиск -->
        <div class="w-full md:w-1/2 lg:w-1/3">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Поиск по названию</label>
            <input type="text" id="search" name="search" 
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
        </div>
        
        <!-- Бренд -->
        <div class="w-full md:w-1/2 lg:w-1/3">
            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Бренд</label>
            <select id="brand" name="brand" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                <option value="">Все бренды</option>
                <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand ?>" <?= isset($_GET['brand']) && $_GET['brand'] === $brand ? 'selected' : '' ?>>
                    <?= $brand ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Кнопки -->
        <div class="w-full flex items-end">
            <button type="submit" class="bg-stone-600 hover:bg-stone-700 text-white font-medium py-2 px-4 rounded-md transition mr-2">
                Применить
            </button>
            <a href="/admin/products" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition">
                Сбросить
            </a>
        </div>
    </form>
</div>

<!-- Таблица товаров -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($products)): ?>
    <div class="p-6 text-center text-gray-600">
        <p>Товары не найдены.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Изображение</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Название</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Бренд</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Цена</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Дата добавления</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($products as $product): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-4 text-sm text-gray-900"><?= $product['id'] ?></td>
                    <td class="py-4 px-4">
                        <img src="<?= $product['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-900 max-w-xs truncate font-medium">
                        <?= htmlspecialchars($product['name']) ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-900"><?= htmlspecialchars($product['brand']) ?></td>
                    <td class="py-4 px-4 text-sm text-gray-900 font-medium"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</td>
                    <td class="py-4 px-4 text-sm text-gray-600"><?= date('d.m.Y', strtotime($product['created_at'])) ?></td>
                    <td class="py-4 px-4 text-sm text-center">
                        <div class="flex justify-center space-x-2">
                            <!-- Просмотр -->
                            <a href="/product/<?= $product['id'] ?>" target="_blank" title="Просмотр на сайте" 
                               class="text-gray-600 hover:text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <!-- Редактирование -->
                            <a href="/admin/products/edit/<?= $product['id'] ?>" title="Редактировать" 
                               class="text-blue-600 hover:text-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <!-- Удаление -->
                            <button onclick="deleteProduct(<?= $product['id'] ?>)" title="Удалить" 
                                    class="text-red-600 hover:text-red-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
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
            $prevPageUrl = '/admin/products?' . http_build_query($queryParams);
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
            $firstPageUrl = '/admin/products?' . http_build_query($queryParams);
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
            $pageUrl = '/admin/products?' . http_build_query($queryParams);
        ?>
        <a href="<?= $pageUrl ?>" class="px-4 py-2 border <?= $i === $currentPage ? 'bg-stone-600 text-white' : 'border-gray-300 text-stone-600 hover:bg-stone-50' ?> rounded-md transition">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php 
        // Добавляем последнюю страницу и многоточие, если нужно
        if ($endPage < $totalPages):
            $queryParams['page'] = $totalPages;
            $lastPageUrl = '/admin/products?' . http_build_query($queryParams);
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
            $nextPageUrl = '/admin/products?' . http_build_query($queryParams);
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
