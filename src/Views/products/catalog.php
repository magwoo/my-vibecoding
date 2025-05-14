<!-- Заголовок каталога -->
<div class="bg-stone-600 py-6 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Каталог телефонов</h1>
    </div>
</div>

<div class="container mx-auto px-4">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Фильтры -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Фильтры</h2>
                
                <form action="/catalog" method="GET" class="space-y-6">
                    <!-- Поиск -->
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                    <?php endif; ?>
                    
                    <!-- Цена -->
                    <div>
                        <h3 class="font-medium mb-3">Цена, ₽</h3>
                        <div class="flex gap-2">
                            <div>
                                <label for="price_min" class="sr-only">От</label>
                                <input type="number" id="price_min" name="price_min" placeholder="От" min="0" 
                                    value="<?= isset($filters['price_min']) ? (int)$filters['price_min'] : '' ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            </div>
                            <div>
                                <label for="price_max" class="sr-only">До</label>
                                <input type="number" id="price_max" name="price_max" placeholder="До" min="0" 
                                    value="<?= isset($filters['price_max']) ? (int)$filters['price_max'] : '' ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Бренд -->
                    <div>
                        <h3 class="font-medium mb-3">Бренд</h3>
                        <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            <option value="">Все бренды</option>
                            <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand ?>" <?= isset($filters['brand']) && $filters['brand'] === $brand ? 'selected' : '' ?>>
                                <?= $brand ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Оперативная память -->
                    <?php if (!empty($ramValues)): ?>
                    <div>
                        <h3 class="font-medium mb-3">Оперативная память</h3>
                        <select name="ram" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            <option value="">Любая</option>
                            <?php foreach ($ramValues as $ram): ?>
                            <option value="<?= $ram ?>" <?= isset($filters['ram']) && $filters['ram'] === $ram ? 'selected' : '' ?>>
                                <?= $ram ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Объем памяти -->
                    <?php if (!empty($storageValues)): ?>
                    <div>
                        <h3 class="font-medium mb-3">Встроенная память</h3>
                        <select name="storage" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            <option value="">Любая</option>
                            <?php foreach ($storageValues as $storage): ?>
                            <option value="<?= $storage ?>" <?= isset($filters['storage']) && $filters['storage'] === $storage ? 'selected' : '' ?>>
                                <?= $storage ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Диагональ экрана -->
                    <?php if (!empty($screenValues)): ?>
                    <div>
                        <h3 class="font-medium mb-3">Диагональ экрана</h3>
                        <select name="screen" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            <option value="">Любая</option>
                            <?php foreach ($screenValues as $screen): ?>
                            <option value="<?= $screen ?>" <?= isset($filters['screen']) && $filters['screen'] === $screen ? 'selected' : '' ?>>
                                <?= $screen ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Сортировка -->
                    <div>
                        <h3 class="font-medium mb-3">Сортировка</h3>
                        <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Сначала новые</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена (по возрастанию)</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена (по убыванию)</option>
                        </select>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex gap-2">
                        <button type="submit" class="bg-stone-600 hover:bg-stone-700 text-white font-medium px-4 py-2 rounded-md transition">
                            Применить
                        </button>
                        <a href="/catalog" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-md transition">
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Список товаров -->
        <div class="lg:w-3/4">
            <!-- Информация о результатах поиска -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <p class="text-gray-600">
                        Найдено товаров: <span class="font-semibold"><?= $totalProducts ?></span>
                        <?php if (isset($filters['search']) && !empty($filters['search'])): ?>
                        по запросу "<span class="font-semibold"><?= htmlspecialchars($filters['search']) ?></span>"
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- Мобильная кнопка фильтров (для адаптивности) -->
                <button id="mobileFilterToggle" class="lg:hidden mt-3 sm:mt-0 bg-stone-600 text-white px-4 py-2 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Фильтры
                </button>
            </div>
            
            <?php if (empty($products)): ?>
            <!-- Товары не найдены -->
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">Товары не найдены</h3>
                <p class="text-gray-600 mb-4">Попробуйте изменить параметры фильтрации или поиска</p>
                <a href="/catalog" class="bg-stone-600 hover:bg-stone-700 text-white font-medium px-4 py-2 rounded-md transition">
                    Сбросить фильтры
                </a>
            </div>
            <?php else: ?>
            <!-- Сетка товаров -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:transform hover:scale-105">
                    <a href="/product/<?= $product['id'] ?>">
                        <img src="<?= $product['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-56 object-cover">
                    </a>
                    <div class="p-5">
                        <h3 class="text-lg font-semibold mb-2">
                            <a href="/product/<?= $product['id'] ?>" class="text-stone-800 hover:text-stone-600">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-2 text-sm"><?= htmlspecialchars($product['brand']) ?></p>
                        
                        <?php 
                        // Декодируем JSON спецификаций
                        $specs = json_decode($product['specs'], true);
                        ?>
                        
                        <div class="text-xs text-gray-500 mb-3 space-y-1">
                            <?php if (!empty($specs['ram'])): ?>
                            <p>RAM: <?= htmlspecialchars($specs['ram']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($specs['storage'])): ?>
                            <p>Память: <?= htmlspecialchars($specs['storage']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($specs['screen'])): ?>
                            <p>Экран: <?= htmlspecialchars($specs['screen']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-stone-600"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</span>
                            <button onclick="addToCart(<?= $product['id'] ?>)" class="bg-stone-600 hover:bg-stone-700 text-white px-3 py-1 rounded focus:outline-none transition">
                                В корзину
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Пагинация -->
            <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center">
                <div class="flex space-x-1">
                    <?php 
                    // Формируем URL для пагинации с сохранением всех параметров фильтрации
                    $queryParams = $_GET;
                    
                    // Кнопка "Предыдущая страница"
                    if ($currentPage > 1):
                        $queryParams['page'] = $currentPage - 1;
                        $prevPageUrl = '/catalog?' . http_build_query($queryParams);
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
                        $firstPageUrl = '/catalog?' . http_build_query($queryParams);
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
                        $pageUrl = '/catalog?' . http_build_query($queryParams);
                    ?>
                    <a href="<?= $pageUrl ?>" class="px-4 py-2 border <?= $i === $currentPage ? 'bg-stone-600 text-white' : 'border-gray-300 text-stone-600 hover:bg-stone-50' ?> rounded-md transition">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php 
                    // Добавляем последнюю страницу и многоточие, если нужно
                    if ($endPage < $totalPages):
                        $queryParams['page'] = $totalPages;
                        $lastPageUrl = '/catalog?' . http_build_query($queryParams);
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
                        $nextPageUrl = '/catalog?' . http_build_query($queryParams);
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
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Скрипт для мобильного переключения фильтров -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterToggle = document.getElementById('mobileFilterToggle');
        const filterContainer = document.querySelector('.lg\\:w-1\\/4');
        
        if (filterToggle && filterContainer) {
            filterToggle.addEventListener('click', function() {
                filterContainer.classList.toggle('hidden');
                filterContainer.classList.toggle('lg:block');
            });
            
            // Скрываем фильтры на мобильных устройствах по умолчанию
            if (window.innerWidth < 1024) {
                filterContainer.classList.add('hidden');
            }
        }
    });
</script>
