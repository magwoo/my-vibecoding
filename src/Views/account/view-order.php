<!-- Заголовок детали заказа -->
<div class="bg-stone-600 py-6 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Заказ #<?= $order['id'] ?></h1>
    </div>
</div>

<div class="container mx-auto px-4 mb-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Навигация личного кабинета -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Меню</h2>
                <nav class="space-y-2">
                    <a href="/account" class="block px-4 py-2 text-gray-700 hover:bg-stone-50 rounded-md">
                        Мой профиль
                    </a>
                    <a href="/account/orders" class="block px-4 py-2 text-gray-700 hover:bg-stone-50 rounded-md">
                        История заказов
                    </a>
                    <a href="/logout" class="block px-4 py-2 text-gray-700 hover:bg-stone-50 rounded-md">
                        Выйти
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Контент детали заказа -->
        <div class="lg:w-3/4">
            <!-- Информация о заказе -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold">Информация о заказе</h2>
                        <p class="text-gray-600 mt-1">Дата: <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>
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
                
                <div class="border-t border-gray-200 pt-4">
                    <p class="text-gray-700">
                        <span class="font-medium">Сумма заказа:</span> <?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽
                    </p>
                </div>
            </div>
            
            <!-- Товары в заказе -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="p-6 border-b border-gray-200 font-semibold text-lg">Товары в заказе</h3>
                
                <?php foreach ($orderItems as $item): ?>
                <div class="border-b border-gray-200 last:border-b-0 p-6 flex flex-col sm:flex-row gap-4">
                    <!-- Изображение товара -->
                    <div class="sm:w-24 flex-shrink-0">
                        <img src="<?= $item['image_path'] ?: '/uploads/placeholder.jpg' ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                             class="w-24 h-24 object-cover rounded-md">
                    </div>
                    
                    <!-- Информация о товаре -->
                    <div class="flex-grow">
                        <h4 class="text-lg font-semibold mb-1">
                            <?= htmlspecialchars($item['name']) ?>
                        </h4>
                        <p class="text-gray-600 mb-2 text-sm"><?= htmlspecialchars($item['brand']) ?></p>
                        
                        <div class="flex justify-between items-center mt-4">
                            <div class="text-gray-700">
                                <span>Количество: <?= $item['quantity'] ?></span>
                            </div>
                            
                            <div class="font-semibold">
                                <span><?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Итого -->
                <div class="bg-gray-50 p-6">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Итого:</span>
                        <span class="text-xl font-bold text-stone-600"><?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</span>
                    </div>
                </div>
            </div>
            
            <!-- Кнопка возврата к списку заказов -->
            <div class="mt-6">
                <a href="/account/orders" class="inline-block bg-stone-600 hover:bg-stone-700 text-white font-medium py-2 px-4 rounded-md transition">
                    Вернуться к списку заказов
                </a>
            </div>
        </div>
    </div>
</div>
