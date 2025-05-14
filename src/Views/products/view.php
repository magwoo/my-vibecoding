<!-- Хлебные крошки -->
<div class="bg-gray-100 py-2">
    <div class="container mx-auto px-4">
        <div class="flex items-center text-sm text-gray-600">
            <a href="/" class="hover:text-stone-600">Главная</a>
            <span class="mx-2">/</span>
            <a href="/catalog" class="hover:text-stone-600">Каталог</a>
            <span class="mx-2">/</span>
            <span class="text-stone-800"><?= htmlspecialchars($product['name']) ?></span>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Изображение товара -->
            <div class="md:w-1/2 p-6">
                <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center h-full">
                    <img src="<?= $product['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="max-h-80 max-w-full object-contain">
                </div>
            </div>
            
            <!-- Информация о товаре -->
            <div class="md:w-1/2 p-6 flex flex-col">
                <h1 class="text-2xl md:text-3xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-gray-600 mb-4">Бренд: <span class="font-medium"><?= htmlspecialchars($product['brand']) ?></span></p>
                
                <!-- Цена и кнопка добавления в корзину -->
                <div class="bg-stone-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-3xl font-bold text-stone-600"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</span>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">В наличии</span>
                    </div>
                    
                    <div class="flex space-x-3">
                        <div class="flex-1">
                            <button onclick="addToCart(<?= $product['id'] ?>)" class="w-full bg-stone-600 hover:bg-stone-700 text-white font-medium py-3 px-4 rounded-lg transition flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                В корзину
                            </button>
                        </div>
                        
                        <div class="w-1/4">
                            <input type="number" id="quantity" min="1" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-3 text-center">
                        </div>
                    </div>
                </div>
                
                <!-- Характеристики телефона -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Основные характеристики</h2>
                    <div class="space-y-2">
                        <?php if (!empty($product['specs']['ram'])): ?>
                        <div class="flex">
                            <span class="w-1/3 text-gray-600">Оперативная память:</span>
                            <span class="w-2/3 font-medium"><?= htmlspecialchars($product['specs']['ram']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['specs']['storage'])): ?>
                        <div class="flex">
                            <span class="w-1/3 text-gray-600">Встроенная память:</span>
                            <span class="w-2/3 font-medium"><?= htmlspecialchars($product['specs']['storage']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['specs']['screen'])): ?>
                        <div class="flex">
                            <span class="w-1/3 text-gray-600">Экран:</span>
                            <span class="w-2/3 font-medium"><?= htmlspecialchars($product['specs']['screen']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['specs']['processor'])): ?>
                        <div class="flex">
                            <span class="w-1/3 text-gray-600">Процессор:</span>
                            <span class="w-2/3 font-medium"><?= htmlspecialchars($product['specs']['processor']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['specs']['camera'])): ?>
                        <div class="flex">
                            <span class="w-1/3 text-gray-600">Камера:</span>
                            <span class="w-2/3 font-medium"><?= htmlspecialchars($product['specs']['camera']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Преимущества -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Преимущества покупки</h2>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Официальная гарантия</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Быстрая доставка</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Возможность возврата в течение 14 дней</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Описание товара -->
        <div class="p-6 border-t border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Описание</h2>
            <div class="prose max-w-none text-gray-700">
                <?php if (!empty($product['description'])): ?>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <?php else: ?>
                    <p>Подробное описание данного товара отсутствует.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Скрипт для добавления в корзину с указанным количеством -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartBtn = document.querySelector('button[onclick^="addToCart"]');
        const quantityInput = document.getElementById('quantity');
        
        if (addToCartBtn && quantityInput) {
            // Переопределяем функцию onclick для кнопки
            addToCartBtn.onclick = function() {
                const quantity = parseInt(quantityInput.value, 10) || 1;
                addToCart(<?= $product['id'] ?>, quantity);
            };
        }
    });
</script>
