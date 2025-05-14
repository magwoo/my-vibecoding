<!-- Заголовок корзины -->
<div class="bg-stone-600 py-6 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Корзина</h1>
    </div>
</div>

<div class="container mx-auto px-4 mb-12">
    <?php if (empty($items)): ?>
    <!-- Пустая корзина -->
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h2 class="text-2xl font-semibold mb-4">Ваша корзина пуста</h2>
        <p class="text-gray-600 mb-6">Выберите товары в каталоге и добавьте их в корзину</p>
        <a href="/catalog" class="bg-stone-600 hover:bg-stone-700 text-white font-medium py-3 px-6 rounded-lg transition">
            Перейти в каталог
        </a>
    </div>
    <?php else: ?>
    <!-- Корзина с товарами -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Список товаров -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="cart-items">
                    <?php foreach ($items as $item): ?>
                    <div class="border-b border-gray-200 last:border-b-0 p-6 flex flex-col sm:flex-row gap-4">
                        <!-- Изображение товара -->
                        <div class="sm:w-24 flex-shrink-0">
                            <a href="/product/<?= $item['product_id'] ?>">
                                <img src="<?= $item['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                     class="w-24 h-24 object-cover rounded-md">
                            </a>
                        </div>
                        
                        <!-- Информация о товаре -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold mb-1">
                                <a href="/product/<?= $item['product_id'] ?>" class="text-stone-800 hover:text-stone-600">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-2 text-sm"><?= htmlspecialchars($item['brand']) ?></p>
                            
                            <div class="flex justify-between items-center mt-4">
                                <div class="flex items-center">
                                    <span class="mr-3 text-gray-600">Количество:</span>
                                    <div class="flex items-center border border-gray-300 rounded-md">
                                        <button onclick="updateCartItem(<?= $item['product_id'] ?>, <?= max(1, $item['quantity'] - 1) ?>)" 
                                                class="px-3 py-1 text-lg text-gray-600 hover:bg-gray-100">-</button>
                                        <input type="number" value="<?= $item['quantity'] ?>" min="1" class="w-12 text-center p-1 border-0 focus:outline-none"
                                               onchange="updateCartItem(<?= $item['product_id'] ?>, this.value)">
                                        <button onclick="updateCartItem(<?= $item['product_id'] ?>, <?= $item['quantity'] + 1 ?>)" 
                                                class="px-3 py-1 text-lg text-gray-600 hover:bg-gray-100">+</button>
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <span class="font-semibold text-lg mr-4"><?= number_format($item['total'], 0, '.', ' ') ?> ₽</span>
                                    <button onclick="removeFromCart(<?= $item['product_id'] ?>)" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Итого и оформление заказа -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold mb-4">Ваш заказ</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Товары (<?= count($items) ?>):</span>
                        <span class="font-medium"><?= number_format($total, 0, '.', ' ') ?> ₽</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Доставка:</span>
                        <span class="text-green-600 font-medium">Бесплатно</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Итого:</span>
                            <span class="text-lg font-bold text-stone-600 cart-total"><?= number_format($total, 0, '.', ' ') ?> ₽</span>
                        </div>
                    </div>
                </div>
                
                <form action="/api/checkout" method="POST" class="checkout-button">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="w-full bg-stone-600 hover:bg-stone-700 text-white font-medium py-3 px-4 rounded-lg transition">
                        Оформить заказ
                    </button>
                </form>
                
                <div class="mt-6 text-sm text-gray-600">
                    <p class="mb-2 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Бесплатная доставка по всей России</span>
                    </p>
                    <p class="mb-2 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Оплата при получении</span>
                    </p>
                    <p class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Возврат в течение 14 дней</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
