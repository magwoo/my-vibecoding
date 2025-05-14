<!-- Баннер -->
<section class="bg-stone-700 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Добро пожаловать в ТелефонМаркет</h1>
        <p class="text-xl mb-8">Широкий выбор современных смартфонов от ведущих производителей</p>
        <a href="/catalog" class="bg-stone-600 hover:bg-stone-500 text-white font-bold py-3 px-6 rounded-lg transition duration-300 inline-block">
            Перейти в каталог
        </a>
    </div>
</section>

<!-- Преимущества магазина -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-10">Наши преимущества</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Преимущество 1 -->
            <div class="bg-stone-50 p-6 rounded-lg shadow-md text-center">
                <div class="bg-stone-600 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Быстрая доставка</h3>
                <p class="text-gray-600">Доставляем заказы в кратчайшие сроки в любой город России</p>
            </div>
            
            <!-- Преимущество 2 -->
            <div class="bg-stone-50 p-6 rounded-lg shadow-md text-center">
                <div class="bg-stone-600 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Гарантия качества</h3>
                <p class="text-gray-600">Все телефоны проходят тщательную проверку перед отправкой</p>
            </div>
            
            <!-- Преимущество 3 -->
            <div class="bg-stone-50 p-6 rounded-lg shadow-md text-center">
                <div class="bg-stone-600 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Техническая поддержка</h3>
                <p class="text-gray-600">Наши специалисты всегда готовы помочь с выбором и настройкой</p>
            </div>
        </div>
    </div>
</section>

<!-- Новые поступления -->
<section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Новые поступления</h2>
            <a href="/catalog" class="text-stone-600 hover:text-stone-800 font-medium transition">
                Смотреть все
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:transform hover:scale-105">
                <a href="/product/<?= $product['id'] ?>">
                    <img src="<?= $product['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-64 object-cover">
                </a>
                <div class="p-5">
                    <h3 class="text-xl font-semibold mb-2">
                        <a href="/product/<?= $product['id'] ?>" class="text-stone-800 hover:text-stone-600">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-3 text-sm"><?= htmlspecialchars($product['brand']) ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-stone-600"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</span>
                        <button onclick="addToCart(<?= $product['id'] ?>)" class="bg-stone-600 hover:bg-stone-700 text-white px-4 py-2 rounded focus:outline-none transition">
                            В корзину
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Бренды -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-10">Популярные бренды</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Apple -->
            <div class="bg-stone-50 p-6 rounded-lg text-center">
                <h3 class="text-xl font-bold">Apple</h3>
                <a href="/catalog?brand=Apple" class="text-stone-600 hover:text-stone-800 mt-2 inline-block">Смотреть все</a>
            </div>
            
            <!-- Samsung -->
            <div class="bg-stone-50 p-6 rounded-lg text-center">
                <h3 class="text-xl font-bold">Samsung</h3>
                <a href="/catalog?brand=Samsung" class="text-stone-600 hover:text-stone-800 mt-2 inline-block">Смотреть все</a>
            </div>
            
            <!-- Xiaomi -->
            <div class="bg-stone-50 p-6 rounded-lg text-center">
                <h3 class="text-xl font-bold">Xiaomi</h3>
                <a href="/catalog?brand=Xiaomi" class="text-stone-600 hover:text-stone-800 mt-2 inline-block">Смотреть все</a>
            </div>
            
            <!-- Google -->
            <div class="bg-stone-50 p-6 rounded-lg text-center">
                <h3 class="text-xl font-bold">Google</h3>
                <a href="/catalog?brand=Google" class="text-stone-600 hover:text-stone-800 mt-2 inline-block">Смотреть все</a>
            </div>
        </div>
    </div>
</section>

<!-- Информация о доставке и оплате -->
<section class="py-12 bg-stone-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-10">Доставка и оплата</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Доставка -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    Доставка
                </h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Курьерская доставка по Москве и Санкт-Петербургу</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Доставка Почтой России по всей стране</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Транспортные компании: СДЭК, DPD, BoxBerry</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Самовывоз из пунктов выдачи в крупных городах</span>
                    </li>
                </ul>
            </div>
            
            <!-- Оплата -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Оплата
                </h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Банковские карты Visa, MasterCard, МИР</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Электронные платежи (Яндекс.Деньги, QIWI)</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Оплата наличными при получении</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Безналичный расчет для юридических лиц</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
